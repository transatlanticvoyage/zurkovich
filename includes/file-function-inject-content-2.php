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

    // Fetch and update Elementor JSON data
    $data = get_post_meta($page_id, '_elementor_data', true);
    if ($data) {
        $elements = is_string($data) ? json_decode($data, true) : $data;
        if (is_array($elements)) {
            $new = process_elements($elements, $map);
            update_post_meta($page_id, '_elementor_data', wp_json_encode($new));
            return true;
        }
    }
    
    return false;
}

/**
 * Process Elementor elements recursively
 */
function process_elements($elements, $map) {
    foreach ($elements as &$el) {
        if (isset($el['settings']) && is_array($el['settings'])) {
            foreach ($el['settings'] as $skey => $sval) {
                if (is_string($sval)) {
                    foreach ($map as $key => $val) {
                        if ($sval === $key) {
                            $el['settings'][$skey] = $val;
                        }
                    }
                }
            }
        }
        if (isset($el['elements']) && is_array($el['elements'])) {
            $el['elements'] = process_elements($el['elements'], $map);
        }
    }
    return $elements;
} 