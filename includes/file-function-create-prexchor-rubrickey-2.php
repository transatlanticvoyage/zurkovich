<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create a mapping of content identifiers to their Elementor element locations
 *
 * @param int $page_id The ID of the page to process
 * @return bool True on success, false on failure
 */
function function_create_prexchor_rubrickey_2($page_id) {
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
            if (!isset($widget['settings']) || !is_array($widget['settings'])) {
                return;
            }
            
            foreach ($widget['settings'] as $key => $value) {
                if (is_string($value) && strpos($value, $line) !== false) {
                    $mapping[$line] = array(
                        'widget_type' => $widget['widgetType'],
                        'setting_key' => $key
                    );
                    $found = true;
                    error_log('Found match in widget: ' . $widget['widgetType'] . ' setting: ' . $key);
                    break;
                }
            }
        };
        
        // Function to recursively search through elements
        $search_elements = function($elements) use (&$search_elements, $check_widget, $line, &$found) {
            if (!is_array($elements)) return;
            
            foreach ($elements as $element) {
                if (isset($element['widgetType'])) {
                    $check_widget($element, $line);
                }
                
                if (isset($element['elements'])) {
                    $search_elements($element['elements']);
                }
            }
        };
        
        $search_elements($elementor_data);
        
        if (!$found) {
            error_log('No match found for line: ' . $line);
        }
    }
    
    // Save the mapping
    update_post_meta($page_id, 'prexchor_rubrickey', json_encode($mapping));
    error_log('Saved mapping: ' . print_r($mapping, true));
} 