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
    if (empty($zeeprex_content)) {
        return false;
    }
    
    // Decode HTML entities in the content
    $zeeprex_content = html_entity_decode($zeeprex_content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Get Elementor data
    $elementor_data = get_post_meta($page_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        return false;
    }
    
    $data = json_decode($elementor_data, true);
    if (!is_array($data)) {
        return false;
    }

    // Parse zeeprex_content into code => content
    $lines = preg_split('/\r?\n/', $zeeprex_content);
    $map = [];
    $current_code = null;
    
    foreach ($lines as $line) {
        $line = rtrim($line);
        // Look for lines starting with >y_ or >Y_
        if (preg_match('/^>y_([^\s]+)/', $line, $m)) {
            $current_code = 'y_' . $m[1];
            $map[$current_code] = '';
        } elseif (preg_match('/^>Y_([^\s]+)/', $line, $m)) {
            $current_code = 'Y_' . $m[1];
            $map[$current_code] = '';
        } elseif (preg_match('/^>/', $line)) {
            // Ignore other codes starting with >
            $current_code = null;
        } elseif ($current_code !== null) {
            $map[$current_code] .= ($map[$current_code] === '' ? '' : "\n") . $line;
        }
    }
    
    if (empty($map)) {
        return false;
    }

    // Helper to recursively update widgets
    $update_widgets = function (&$elements) use (&$update_widgets, $map) {
        foreach ($elements as &$el) {
            if (isset($el['settings']) && isset($el['widgetType'])) {
                // Handle different widget types
                switch ($el['widgetType']) {
                    case 'heading':
                        if (isset($el['settings']['title'])) {
                            foreach ($map as $code => $content) {
                                if ($el['settings']['title'] === $code) {
                                    $el['settings']['title'] = $content;
                                }
                            }
                        }
                        break;
                        
                    case 'text-editor':
                        if (isset($el['settings']['editor'])) {
                            foreach ($map as $code => $content) {
                                if (strpos($el['settings']['editor'], $code) !== false) {
                                    $el['settings']['editor'] = str_replace($code, $content, $el['settings']['editor']);
                                }
                            }
                        }
                        break;
                        
                    case 'image-box':
                        if (isset($el['settings']['title_text'])) {
                            foreach ($map as $code => $content) {
                                if ($el['settings']['title_text'] === $code) {
                                    $el['settings']['title_text'] = $content;
                                }
                            }
                        }
                        if (isset($el['settings']['description_text'])) {
                            foreach ($map as $code => $content) {
                                if ($el['settings']['description_text'] === $code) {
                                    $el['settings']['description_text'] = $content;
                                }
                            }
                        }
                        break;
                }
            }
            if (isset($el['elements']) && is_array($el['elements'])) {
                $update_widgets($el['elements']);
            }
        }
    };
    
    $update_widgets($data);

    // Use Elementor's API to save the data
    if (class_exists('\Elementor\Plugin')) {
        $document = \Elementor\Plugin::$instance->documents->get($page_id);
        if ($document) {
            $document->save([
                'elements' => $data,
                'settings' => [
                    'post_status' => 'publish',
                ],
            ]);
            return true;
        }
    }
    
    return false;
} 