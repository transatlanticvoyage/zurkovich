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
                $output_fields = ['title', 'description'];
                break;
            case 'button':
            case 'button.default':
                $output_fields = ['text', 'button_text'];
                break;
            default:
                // For other widgets, try common output fields
                $output_fields = ['title', 'heading', 'editor', 'content', 'text', 'description', 'button_text'];
                break;
        }
        foreach ($output_fields as $field) {
            if (isset($settings[$field]) && is_string($settings[$field]) && trim($settings[$field]) !== '') {
                $lines[] = wp_strip_all_tags($settings[$field]);
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