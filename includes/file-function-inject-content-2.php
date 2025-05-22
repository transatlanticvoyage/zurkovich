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
    if (empty($zeeprex_content)) {
        return false;
    }
    
    // Parse mappings
    $map = array();
    $lines = preg_split('/\r\n|\r|\n/', $zeeprex_content);
    $key = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^>y_([^\s]+)/', $line, $m)) {
            $key = 'y_' . $m[1];
            $map[$key] = '';
        } elseif (preg_match('/^>Y_([^\s]+)/', $line, $m)) {
            $key = 'Y_' . $m[1];
            $map[$key] = '';
        } elseif (preg_match('/^>/', $line)) {
            $key = '';
        } elseif ($key !== '') {
            $map[$key] .= ($map[$key] === '' ? '' : "\n") . $line;
        }
    }
    
    if (empty($map)) {
        return false;
    }

    // Save mapping meta
    update_post_meta($page_id, 'zeeprex_map', $map);

    // Get Elementor data
    $data = get_post_meta($page_id, '_elementor_data', true);
    if (!$data) {
        return false;
    }

    // Decode the data
    $elements = json_decode($data, true);
    if (!is_array($elements)) {
        return false;
    }

    // Process the elements
    $elements = process_elements($elements, $map);

    // Save the updated data
    $updated_data = wp_json_encode($elements);
    if ($updated_data === false) {
        return false;
    }

    // Update the post meta
    update_post_meta($page_id, '_elementor_data', $updated_data);

    // Clear Elementor cache
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
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
                switch ($el['widgetType']) {
                    case 'heading':
                        if (isset($original_settings['title'])) {
                            foreach ($map as $key => $val) {
                                if ($original_settings['title'] === $key) {
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