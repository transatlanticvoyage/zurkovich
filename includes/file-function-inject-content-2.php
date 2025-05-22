<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inject content into Elementor widgets based on user input codes.
 *
 * @param int $page_id The ID of the page to update.
 * @param string $zeeprex_content The user-submitted content.
 * @return bool True on success, false on failure.
 */
function function_inject_content_2($page_id, $zeeprex_content) {
    error_log('Starting function_inject_content_2 for page_id: ' . $page_id);
    
    if (empty($zeeprex_content)) {
        error_log('Empty zeeprex_content');
        return false;
    }

    // Decode HTML entities in the content
    $zeeprex_content = html_entity_decode($zeeprex_content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    error_log('Decoded content: ' . substr($zeeprex_content, 0, 200));
    
    // Parse mappings
    $map = array();
    $lines = preg_split('/\r\n|\r|\n/', $zeeprex_content);
    $key = '';
    
    foreach ($lines as $line_num => $line) {
        $line = trim($line);
        error_log('Processing line ' . ($line_num + 1) . ': ' . $line);
        
        if (preg_match('/^#y_([^\s]+)/', $line, $m)) {
            $key = 'y_' . $m[1];
            $map[$key] = '';
            error_log('Found y_ code: ' . $key);
        } elseif (preg_match('/^#Y_([^\s]+)/', $line, $m)) {
            $key = 'Y_' . $m[1];
            $map[$key] = '';
            error_log('Found Y_ code: ' . $key);
        } elseif (preg_match('/^#/', $line)) {
            $key = '';
            error_log('Ignoring non-y code: ' . $line);
        } elseif ($key !== '') {
            $map[$key] .= ($map[$key] === '' ? '' : "\n") . $line;
            error_log('Added content for ' . $key . ': ' . substr($line, 0, 50) . '...');
        }
    }
    
    if (empty($map)) {
        error_log('No codes found in content');
        return false;
    }

    error_log('Found ' . count($map) . ' codes to process');
    error_log('Codes: ' . implode(', ', array_keys($map)));

    // Save mapping meta
    update_post_meta($page_id, 'zeeprex_map', $map);
    error_log('Saved mapping to zeeprex_map');

    // Get Elementor data
    $data = get_post_meta($page_id, '_elementor_data', true);
    if (!$data) {
        error_log('No Elementor data found for page');
        return false;
    }

    error_log('Raw Elementor data: ' . substr($data, 0, 200) . '...');

    // Decode the data
    $elements = json_decode($data, true);
    if (!is_array($elements)) {
        error_log('Failed to decode Elementor data');
        return false;
    }

    // Process the elements
    $elements = process_elements($elements, $map);

    // Save the updated data
    $updated_data = wp_json_encode($elements);
    if ($updated_data === false) {
        error_log('Failed to encode updated data');
        return false;
    }

    error_log('Updated Elementor data: ' . substr($updated_data, 0, 200) . '...');

    // Update the post using wp_update_post
    $update_result = wp_update_post(array(
        'ID' => $page_id,
        'post_content' => $updated_data
    ));
    error_log('Update post result: ' . ($update_result ? 'success' : 'failed'));

    // Clear Elementor cache
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
        error_log('Cleared Elementor cache');
    }

    return true;
}

/**
 * Process Elementor elements recursively
 */
function process_elements($elements, $map) {
    foreach ($elements as &$el) {
        if (isset($el['settings']) && is_array($el['settings'])) {
            // Store original settings
            $original_settings = $el['settings'];
            
            // Update content fields based on widget type
            if (isset($el['widgetType'])) {
                error_log('Processing widget type: ' . $el['widgetType']);
                
                switch ($el['widgetType']) {
                    case 'heading':
                        if (isset($original_settings['title'])) {
                            foreach ($map as $key => $val) {
                                if ($original_settings['title'] === $key) {
                                    error_log('Found match in heading title: ' . $key);
                                    $el['settings']['title'] = $val;
                                    // Preserve typography settings
                                    if (isset($original_settings['typography_typography'])) {
                                        $el['settings']['typography_typography'] = $original_settings['typography_typography'];
                                    }
                                }
                            }
                        }
                        break;
                        
                    case 'text-editor':
                        if (isset($original_settings['editor'])) {
                            foreach ($map as $key => $val) {
                                if ($original_settings['editor'] === $key) {
                                    error_log('Found match in text-editor editor: ' . $key);
                                    $el['settings']['editor'] = $val;
                                    // Preserve typography settings
                                    if (isset($original_settings['typography_typography'])) {
                                        $el['settings']['typography_typography'] = $original_settings['typography_typography'];
                                    }
                                }
                            }
                        }
                        break;
                        
                    case 'image-box':
                        if (isset($original_settings['title_text'])) {
                            foreach ($map as $key => $val) {
                                if ($original_settings['title_text'] === $key) {
                                    error_log('Found match in image-box title_text: ' . $key);
                                    $el['settings']['title_text'] = $val;
                                    // Preserve title typography
                                    if (isset($original_settings['title_typography_typography'])) {
                                        $el['settings']['title_typography_typography'] = $original_settings['title_typography_typography'];
                                    }
                                }
                            }
                        }
                        if (isset($original_settings['description_text'])) {
                            foreach ($map as $key => $val) {
                                if ($original_settings['description_text'] === $key) {
                                    error_log('Found match in image-box description_text: ' . $key);
                                    $el['settings']['description_text'] = $val;
                                    // Preserve description typography
                                    if (isset($original_settings['description_typography_typography'])) {
                                        $el['settings']['description_typography_typography'] = $original_settings['description_typography_typography'];
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }
        
        // Process child elements
        if (isset($el['elements']) && is_array($el['elements'])) {
            $el['elements'] = process_elements($el['elements'], $map);
        }
    }
    return $elements;
} 