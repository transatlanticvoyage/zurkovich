<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recursively extract all text content from Elementor data.
 *
 * @param array $element Elementor element array.
 * @param array $lines Accumulator for text lines.
 * @return void
 */
function zurko_extract_elementor_text($element, &$lines) {
    if (isset($element['elements']) && is_array($element['elements'])) {
        foreach ($element['elements'] as $child) {
            zurko_extract_elementor_text($child, $lines);
        }
    }
    // Check for widget content
    if (isset($element['widgetType']) && isset($element['settings'])) {
        foreach ($element['settings'] as $setting) {
            if (is_string($setting) && trim($setting) !== '') {
                $lines[] = wp_strip_all_tags($setting);
            }
        }
    }
    // Also check for section/column titles or other text fields
    if (isset($element['settings']) && is_array($element['settings'])) {
        foreach ($element['settings'] as $setting) {
            if (is_string($setting) && trim($setting) !== '') {
                $lines[] = wp_strip_all_tags($setting);
            }
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