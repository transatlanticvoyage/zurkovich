<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scrape Elementor front-end content for a given page and save as custom field.
 *
 * @param int $page_id The ID of the page to scrape.
 * @return bool True on success, false on failure.
 */
function function_scrape_temprex_1($page_id) {
    // Get the front-end URL for the page
    $url = get_permalink($page_id);
    if (!$url) {
        return false;
    }

    // Fetch the front-end HTML
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return false;
    }
    $html = wp_remote_retrieve_body($response);
    if (empty($html)) {
        return false;
    }

    // Extract text content from Elementor output (placeholder logic)
    // TODO: Replace this with more specific extraction logic as needed
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);
    $elements = $xpath->query('//body//*[not(self::script or self::style)]');
    $lines = [];
    foreach ($elements as $el) {
        $text = trim($el->textContent);
        if ($text !== '') {
            $lines[] = $text;
        }
    }
    $result = implode("\n", $lines);

    // Save to custom field
    update_post_meta($page_id, 'temprex_1_scraped', $result);
    return true;
} 