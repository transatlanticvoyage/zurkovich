<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recursively extract only visible text content from Elementor data.
 *
 * @param array $element Elementor element array.
 * @param array $lines Accumulator for text lines.
 * @return void
 */
function zurko_extract_elementor_text($element, &$lines) {
    // If this is a widget, extract only output fields
    if (isset($element['widgetType']) && isset($element['settings']) && is_array($element['settings'])) {
        $widget_type = $element['widgetType'];
        $settings = $element['settings'];
        
        // Common output fields by widget type
        $output_fields = [];
        switch ($widget_type) {
            case 'heading':
            case 'heading.default':
                $output_fields = ['title', 'heading'];
                break;
            case 'text-editor':
            case 'text-editor.default':
                $output_fields = ['editor', 'content', 'text'];
                break;
            case 'image-box':
            case 'image-box.default':
                // For image-box widgets, we need to check both title and description
                if (isset($settings['title_text']) && is_string($settings['title_text'])) {
                    $text = wp_strip_all_tags($settings['title_text']);
                    if (preg_match('/^(y_|k_)/i', $text)) {
                        $lines[] = $text;
                    }
                }
                if (isset($settings['description_text']) && is_string($settings['description_text'])) {
                    $text = wp_strip_all_tags($settings['description_text']);
                    if (preg_match('/^(y_|k_)/i', $text)) {
                        $lines[] = $text;
                    }
                }
                return; // Skip the general field processing for image-box
            case 'button':
            case 'button.default':
                $output_fields = ['text', 'button_text'];
                break;
            default:
                // For other widgets, try common output fields
                $output_fields = ['title', 'heading', 'editor', 'content', 'text', 'description', 'button_text'];
                break;
        }
        
        // Always check for 'title' and 'description' as a fallback
        $output_fields = array_unique(array_merge($output_fields, ['title', 'description']));
        foreach ($output_fields as $field) {
            if (isset($settings[$field]) && is_string($settings[$field]) && trim($settings[$field]) !== '') {
                $text = wp_strip_all_tags($settings[$field]);
                // Split the text into words and check each word
                $words = preg_split('/\s+/', $text);
                foreach ($words as $word) {
                    if (preg_match('/^(y_|k_)/i', $word)) {
                        $lines[] = $word;
                    }
                }
            }
        }
    }
    // Recursively process children
    if (isset($element['elements']) && is_array($element['elements'])) {
        foreach ($element['elements'] as $child) {
            zurko_extract_elementor_text($child, $lines);
        }
    }
}

/**
 * Scrape Elementor internal data for a given page and save as custom field.
 *
 * @param int $page_id The ID of the page to scrape.
 * @return bool True on success, false on failure.
 */
function function_scrape_temprex_1($page_id) {
    // Get Elementor data from post meta
    $elementor_data = get_post_meta($page_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        return false;
    }
    $data = json_decode($elementor_data, true);
    if (!is_array($data)) {
        return false;
    }
    $lines = [];
    foreach ($data as $element) {
        zurko_extract_elementor_text($element, $lines);
    }
    $result = implode("\n", array_filter(array_map('trim', $lines)));
    update_post_meta($page_id, 'temprex_1_scraped', $result);
    return true;
}

/**
 * Send a prompt to the OpenAI GPT API and return the output.
 *
 * @param string $prompt The prompt to send to the AI tool.
 * @return string The AI's response, or an error message.
 */
function function_prompt_ai_tool_and_receive_output_1($prompt) {
    $api_key = zurkovich_get_api_key();
    if (empty($api_key)) {
        return 'No API key found.';
    }
    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'body' => json_encode(array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => 1024,
        )),
        'timeout' => 30,
    );
    $response = wp_remote_post($endpoint, $args);
    if (is_wp_error($response)) {
        return 'Request error: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (isset($data['choices'][0]['message']['content'])) {
        return trim($data['choices'][0]['message']['content']);
    } elseif (isset($data['error']['message'])) {
        return 'API error: ' . $data['error']['message'];
    }
    return 'Unknown error or no response from AI tool.';
}

/**
 * Helper function to safely update Elementor data and regenerate CSS
 * 
 * @param int $page_id The page ID
 * @param array $data The Elementor data to save
 * @return bool True on success, false on failure
 */
function zurko_update_elementor_data($page_id, $data) {
    if (!class_exists('Elementor\\Plugin')) {
        update_post_meta($page_id, '_elementor_data', wp_json_encode($data));
        return true;
    }

    try {
        $document = \Elementor\Plugin::instance()->documents->get($page_id);
        if (!$document) {
            error_log('Elementor document not found for page_id: ' . $page_id);
            return false;
        }

        // Ensure we have the correct data structure
        $elements = $data;
        if (isset($data[0]['elType']) && $data[0]['elType'] === 'container' && isset($data[0]['elements'])) {
            $elements = $data;
        } elseif (isset($data['elements'])) {
            $elements = $data['elements'];
        }

        // Save using Elementor's document API
        $document->save([
            'elements' => $elements,
            'settings' => $document->get_settings()
        ]);

        // Force CSS regeneration
        if (method_exists(\Elementor\Plugin::instance()->files_manager, 'clear_cache')) {
            \Elementor\Plugin::instance()->files_manager->clear_cache();
        }

        // Trigger Elementor's CSS regeneration
        if (method_exists($document, 'get_css_wrapper_selector')) {
            $css_file = \Elementor\Core\Files\CSS\Post::create($page_id);
            if ($css_file) {
                $css_file->update();
            }
        }

        // Clear WordPress cache
        wp_cache_delete($page_id, 'post_meta');
        clean_post_cache($page_id);

        // Force Elementor to regenerate CSS
        do_action('elementor/css-file/post/parse', $css_file);
        do_action('elementor/css-file/post/update', $css_file);

        return true;
    } catch (Throwable $e) {
        error_log('Elementor save error: ' . $e->getMessage());
        // Fallback to direct meta update if Elementor API fails
        update_post_meta($page_id, '_elementor_data', wp_json_encode($data));
        return false;
    }
}

/**
 * Create a mapping of content identifiers to their Elementor element locations
 *
 * @param int $page_id The ID of the page to process
 * @return bool True on success, false on failure
 */
function function_create_prexchor_rubrickey_1($page_id) {
    error_log('Elementor data structure:');
    
    // Get the content from ante_prexchor_rubrickey
    $ante_content = get_post_meta($page_id, 'ante_prexchor_rubrickey', true);
    if (empty($ante_content)) {
        error_log('ante_prexchor_rubrickey content is empty');
        return;
    }

    // Get Elementor data
    $elementor_data = get_post_meta($page_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        error_log('Elementor data is empty');
        return;
    }

    $elementor_data = json_decode($elementor_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to decode Elementor data: ' . json_last_error_msg());
        return;
    }

    error_log(print_r($elementor_data, true));

    $mapping = array();
    $lines = explode("\n", $ante_content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        error_log('Processing line: ' . $line);
        $found = false;
        
        // Function to check widget settings
        $check_widget = function($widget, $line) use (&$found, &$mapping) {
            if (!isset($widget['settings'])) return;
            
            error_log('Checking widget: ' . $widget['widgetType']);
            error_log('Settings keys: ' . implode(', ', array_keys($widget['settings'])));
            
            // Check all possible text fields
            $text_fields = array('title', 'title_text', 'description_text', 'editor', 'content', 'text');
            foreach ($text_fields as $field) {
                if (isset($widget['settings'][$field])) {
                    error_log('Checking field ' . $field . ': ' . $widget['settings'][$field]);
                    
                    // For editor/content fields, strip HTML tags before matching
                    if ($field === 'editor' || $field === 'content') {
                        $plain = wp_strip_all_tags($widget['settings'][$field]);
                        if (trim($plain) === $line) {
                            error_log('Found exact match in editor/content (stripped)');
                            $mapping[] = '>' . $line . ' -> .elementor-element-' . $widget['id'] . ' [settings.' . $field . ']';
                            $found = true;
                            return true;
                        } elseif (strpos($plain, $line) !== false) {
                            error_log('Found substring match in editor/content (stripped)');
                            $mapping[] = '>' . $line . ' -> .elementor-element-' . $widget['id'] . ' [settings.' . $field . ']';
                            $found = true;
                            return true;
                        }
                    }
                    
                    if ($widget['settings'][$field] === $line) {
                        error_log('Found match in field ' . $field);
                        $mapping[] = '>' . $line . ' -> .elementor-element-' . $widget['id'] . ' [settings.' . $field . ']';
                        $found = true;
                        return true;
                    }
                }
            }
            return false;
        };

        // Recursive function to check all levels of elements (define by reference)
        $check_elements = null;
        $check_elements = function($elements, $line) use (&$check_widget, &$check_elements, &$found) {
            foreach ($elements as $element) {
                if ($check_widget($element, $line)) {
                    $found = true;
                    return true;
                }
                if (isset($element['elements'])) {
                    if ($check_elements($element['elements'], $line)) {
                        return true;
                    }
                }
            }
            return false;
        };

        // Start checking from the top level
        $check_elements($elementor_data, $line);
        
        if (!$found) {
            error_log('No match found for line: ' . $line);
        }
    }

    error_log('Saving mapping for page ' . $page_id . ': ' . implode("\n", $mapping));
    update_post_meta($page_id, 'prexchor_rubrickey', implode("\n", $mapping));

    // Save the original Elementor data back without modification
    update_post_meta($page_id, '_elementor_data', wp_json_encode($elementor_data));
    
    // Ensure Elementor meta fields are set
    update_post_meta($page_id, '_elementor_edit_mode', 'builder');
    update_post_meta($page_id, '_elementor_template_type', 'page');

    // Clear caches
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }

    // Clear Elementor cache if available
    if (class_exists('Elementor\\Plugin') && method_exists(\Elementor\Plugin::instance()->files_manager, 'clear_cache')) {
        \Elementor\Plugin::instance()->files_manager->clear_cache();
    }
}

/**
 * Inject content into Elementor widgets based on user input codes.
 *
 * @param int $page_id The ID of the page to update.
 * @param string $zeeprex_content The user-submitted content.
 * @return bool True on success, false on failure.
 */
function function_inject_content_1($page_id, $zeeprex_content) {
    if (empty($zeeprex_content)) return false;
    $elementor_data = get_post_meta($page_id, '_elementor_data', true);
    if (empty($elementor_data)) return false;
    $data = json_decode($elementor_data, true);
    if (!is_array($data)) return false;

    // Parse zeeprex_content into code => content
    $lines = preg_split('/\r?\n/', $zeeprex_content);
    $map = [];
    $current_code = null;
    foreach ($lines as $line) {
        $line = rtrim($line);
        if (preg_match('/^>([kK_yY][^\s]*)/', $line, $m)) {
            $current_code = $m[1];
            $map[$current_code] = '';
        } elseif (preg_match('/^>/', $line)) {
            // Ignore codes not starting with k_ or y_
            $current_code = null;
        } elseif ($current_code !== null) {
            $map[$current_code] .= ($map[$current_code] === '' ? '' : "\n") . $line;
        }
    }
    if (empty($map)) return false;

    // Helper to recursively update widgets
    $update_widgets = function (&$elements) use (&$update_widgets, $map) {
        foreach ($elements as &$el) {
            if (isset($el['settings']) && isset($el['widgetType'])) {
                // Check all possible text fields
                $fields = ['title', 'title_text', 'description_text', 'editor', 'content', 'text'];
                foreach ($fields as $field) {
                    if (isset($el['settings'][$field]) && is_string($el['settings'][$field])) {
                        foreach ($map as $code => $content) {
                            if ($el['settings'][$field] === $code || strpos($el['settings'][$field], $code) !== false) {
                                // For text editor content, ensure HTML is preserved
                                if ($field === 'editor' || $field === 'content') {
                                    // Store the raw HTML content directly
                                    $el['settings'][$field] = $content;
                                    // Also ensure the widget knows it contains HTML
                                    $el['settings']['text_editor'] = $content;
                                } else {
                                    $el['settings'][$field] = $content;
                                }
                            }
                        }
                    }
                }
            }
            if (isset($el['elements']) && is_array($el['elements'])) {
                $update_widgets($el['elements']);
            }
        }
    };
    $update_widgets($data);

    // Use Elementor's API to update the data and trigger all hooks
    if (class_exists('Elementor\\Plugin')) {
        try {
            $document = \Elementor\Plugin::instance()->documents->get($page_id);
            if ($document) {
                // Ensure we have the correct data structure
                $elements = $data;
                if (isset($data[0]['elType']) && $data[0]['elType'] === 'container' && isset($data[0]['elements'])) {
                    $elements = $data;
                } elseif (isset($data['elements'])) {
                    $elements = $data['elements'];
                }

                // First, update the document settings
                $document->save([
                    'settings' => $document->get_settings()
                ]);

                // Then update the elements
                $document->save([
                    'elements' => $elements
                ]);

                // Force Elementor to regenerate CSS
                if (method_exists($document, 'get_css_wrapper_selector')) {
                    $css_file = \Elementor\Core\Files\CSS\Post::create($page_id);
                    if ($css_file) {
                        $css_file->update();
                    }
                }

                // Clear all caches
                wp_cache_delete($page_id, 'post_meta');
                clean_post_cache($page_id);
                
                if (method_exists(\Elementor\Plugin::instance()->files_manager, 'clear_cache')) {
                    \Elementor\Plugin::instance()->files_manager->clear_cache();
                }

                // Trigger Elementor's save hooks
                do_action('elementor/document/after_save', $document, [
                    'elements' => $elements,
                    'settings' => $document->get_settings()
                ]);

                // Force Elementor to rebuild the page
                if (method_exists($document, 'get_elements_data')) {
                    $document->get_elements_data();
                }
            } else {
                error_log('Elementor document not found for page_id: ' . $page_id);
                return false;
            }
        } catch (Throwable $e) {
            error_log('Elementor save error: ' . $e->getMessage());
            return false;
        }
    } else {
        // Fallback if Elementor is not active
        update_post_meta($page_id, '_elementor_data', wp_json_encode($data));
    }
    return true;
} 