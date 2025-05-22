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
    error_log('Starting function_inject_content_2 for page_id: ' . $page_id);
    
    if (empty($zeeprex_content)) {
        error_log('Empty zeeprex content');
        return false;
    }
    
    // Get Elementor data
    $elementor_data = get_post_meta($page_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        error_log('No Elementor data found for page_id: ' . $page_id);
        return false;
    }
    
    error_log('Raw Elementor data: ' . substr($elementor_data, 0, 500) . '...');
    
    $data = json_decode($elementor_data, true);
    if (!is_array($data)) {
        error_log('Failed to decode Elementor data');
        return false;
    }

    // Parse zeeprex_content into code => content
    $lines = preg_split('/\r?\n/', $zeeprex_content);
    $map = [];
    $current_code = null;
    
    error_log('Processing zeeprex content lines: ' . count($lines));
    
    foreach ($lines as $line) {
        $line = rtrim($line);
        // Look for lines starting with >y_ or >Y_
        if (preg_match('/^>y_([^\s]+)/', $line, $m)) {
            $current_code = 'y_' . $m[1];
            $map[$current_code] = '';
            error_log('Found code: ' . $current_code);
        } elseif (preg_match('/^>Y_([^\s]+)/', $line, $m)) {
            $current_code = 'Y_' . $m[1];
            $map[$current_code] = '';
            error_log('Found code: ' . $current_code);
        } elseif (preg_match('/^>/', $line)) {
            // Ignore other codes starting with >
            $current_code = null;
        } elseif ($current_code !== null) {
            $map[$current_code] .= ($map[$current_code] === '' ? '' : "\n") . $line;
        }
    }
    
    error_log('Found ' . count($map) . ' y_ codes to process');
    error_log('Codes found: ' . implode(', ', array_keys($map)));
    
    if (empty($map)) {
        error_log('No y_ codes found in content');
        return false;
    }

    // Helper to recursively update widgets
    $update_widgets = function (&$elements) use (&$update_widgets, $map) {
        foreach ($elements as &$el) {
            if (isset($el['settings']) && isset($el['widgetType'])) {
                error_log('Checking widget: ' . $el['widgetType']);
                // Check all possible text fields
                $fields = ['title', 'title_text', 'description_text', 'editor', 'content', 'text'];
                foreach ($fields as $field) {
                    if (isset($el['settings'][$field]) && is_string($el['settings'][$field])) {
                        error_log('Field ' . $field . ' value: ' . substr($el['settings'][$field], 0, 100));
                        foreach ($map as $code => $content) {
                            if ($el['settings'][$field] === $code || strpos($el['settings'][$field], $code) !== false) {
                                error_log('Found match for code ' . $code . ' in widget ' . $el['widgetType'] . ' field ' . $field);
                                error_log('Replacing with content: ' . substr($content, 0, 100));
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

    // Save the updated data directly to post meta
    $json_data = wp_json_encode($data);
    error_log('Saving updated data length: ' . strlen($json_data));
    $result = update_post_meta($page_id, '_elementor_data', $json_data);
    error_log('Update post meta result: ' . ($result ? 'success' : 'failed'));
    
    // Ensure Elementor meta fields are set
    update_post_meta($page_id, '_elementor_edit_mode', 'builder');
    update_post_meta($page_id, '_elementor_template_type', 'page');

    // Clear caches
    wp_cache_delete($page_id, 'post_meta');
    clean_post_cache($page_id);
    
    error_log('Function completed successfully');
    return true;
} 