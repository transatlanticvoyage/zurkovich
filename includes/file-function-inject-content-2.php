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
        error_log('Empty zeeprex content');
        return false;
    }
    
    // Decode HTML entities in the content
    $zeeprex_content = html_entity_decode($zeeprex_content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    error_log('Decoded content: ' . substr($zeeprex_content, 0, 200) . '...');
    
    // Parse mappings
    $map = array();
    $lines = preg_split('/\r\n|\r|\n/', $zeeprex_content);
    $key = '';
    
    error_log('Processing ' . count($lines) . ' lines of content');
    
    foreach ($lines as $line) {
        $line = trim($line);
        error_log('Processing line: ' . $line);
        
        if (preg_match('/^>y_([^\s]+)/', $line, $m)) {
            $key = 'y_' . $m[1];
            $map[$key] = '';
            error_log('Found y_ code: ' . $key);
        } elseif (preg_match('/^>Y_([^\s]+)/', $line, $m)) {
            $key = 'Y_' . $m[1];
            $map[$key] = '';
            error_log('Found Y_ code: ' . $key);
        } elseif (preg_match('/^>/', $line)) {
            $key = '';
            error_log('Ignoring non-y code: ' . $line);
        } elseif ($key !== '') {
            $map[$key] .= ($map[$key] === '' ? '' : "\n") . $line;
            error_log('Added content for ' . $key . ': ' . substr($line, 0, 50) . '...');
        }
    }
    
    error_log('Found ' . count($map) . ' codes to process');
    error_log('Codes: ' . implode(', ', array_keys($map)));
    
    if (empty($map)) {
        error_log('No codes found in content');
        return false;
    }

    // Save mapping meta
    update_post_meta($page_id, 'zeeprex_map', $map);
    error_log('Saved mapping to zeeprex_map');

    // Fetch and update Elementor JSON data
    $data = get_post_meta($page_id, '_elementor_data', true);
    if ($data) {
        $elements = is_string($data) ? json_decode($data, true) : $data;
        if (is_array($elements)) {
            error_log('Processing Elementor data');
            $new = process_elements($elements, $map);
            update_post_meta($page_id, '_elementor_data', wp_json_encode($new));
            error_log('Updated Elementor data');
            return true;
        } else {
            error_log('Failed to decode Elementor data');
        }
    } else {
        error_log('No Elementor data found for page_id: ' . $page_id);
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
                        if (strpos($sval, $key) !== false) {
                            error_log('Found match in ' . $skey . ': ' . $key);
                            $el['settings'][$skey] = str_replace($key, $val, $sval);
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