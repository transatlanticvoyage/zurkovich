<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inject content into Elementor widgets based on user input codes.
 * This is a new implementation focused on preserving HTML content.
 *
 * @param int $page_id The ID of the page to update.
 * @param string $zeeprex_content The user-submitted content.
 * @return bool True on success, false on failure.
 */
function function_inject_content_2($page_id, $zeeprex_content) {
    if (empty($zeeprex_content)) return false;
    
    // Get Elementor data
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
                                // Directly use the content as provided, preserving all HTML
                                $el['settings'][$field] = $content;
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

    // Use Elementor's API to update the data
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

                // Update the document
                $document->save([
                    'elements' => $elements,
                    'settings' => $document->get_settings()
                ]);

                // Force Elementor to regenerate CSS
                if (method_exists($document, 'get_css_wrapper_selector')) {
                    $css_file = \Elementor\Core\Files\CSS\Post::create($page_id);
                    if ($css_file) {
                        $css_file->update();
                    }
                }

                // Clear caches
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