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
                // For image-box widgets, we need to check both title and description
                if (isset($settings['title_text']) && is_string($settings['title_text'])) {
                    $text = wp_strip_all_tags($settings['title_text']);
                    if (preg_match('/^(y_|k_)/i', $text)) {
                        $lines[] = $text;
                    }
                }
                if (isset($settings['description_text']) && is_string($settings['description_text'])) {
                    $text = wp_strip_all_tags($settings['description_text']);
                    if (preg_match('/^(y_|k_)/i', $text)) {
                        $lines[] = $text;
                    }
                }
                return; // Skip the general field processing for image-box
            case 'button':
            case 'button.default':
                $output_fields = ['text', 'button_text'];
                break;
            default:
                // For other widgets, try common output fields
                $output_fields = ['title', 'heading', 'editor', 'content', 'text', 'description', 'button_text'];
                break;
        }
        
        // Always check for 'title' and 'description' as a fallback
        $output_fields = array_unique(array_merge($output_fields, ['title', 'description']));
        foreach ($output_fields as $field) {
            if (isset($settings[$field]) && is_string($settings[$field]) && trim($settings[$field]) !== '') {
                $text = wp_strip_all_tags($settings[$field]);
                // Split the text into words and check each word
                $words = preg_split('/\s+/', $text);
                foreach ($words as $word) {
                    if (preg_match('/^(y_|k_)/i', $word)) {
                        $lines[] = $word;
                    }
                }
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

/**
 * Send a prompt to the OpenAI GPT API and return the output.
 *
 * @param string $prompt The prompt to send to the AI tool.
 * @return string The AI's response, or an error message.
 */
function function_prompt_ai_tool_and_receive_output_1($prompt) {
    $api_key = zurkovich_get_api_key();
    if (empty($api_key)) {
        return 'No API key found.';
    }
    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'body' => json_encode(array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => 1024,
        )),
        'timeout' => 30,
    );
    $response = wp_remote_post($endpoint, $args);
    if (is_wp_error($response)) {
        return 'Request error: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (isset($data['choices'][0]['message']['content'])) {
        return trim($data['choices'][0]['message']['content']);
    } elseif (isset($data['error']['message'])) {
        return 'API error: ' . $data['error']['message'];
    }
    return 'Unknown error or no response from AI tool.';
} 