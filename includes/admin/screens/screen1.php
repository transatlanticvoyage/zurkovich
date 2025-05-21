<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit_api_key'])) {
    if (check_admin_referer('zurkovich_api_key_nonce')) {
        $api_key = sanitize_text_field($_POST['openai_api_key']);
        zurkovich_save_api_key($api_key);
        echo '<div class="notice notice-success"><p>API key saved successfully!</p></div>';
    }
}

// Get current API key
$current_api_key = zurkovich_get_api_key();
?>
<div class="wrap">
    <h1>Screen 1 - API Keys</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('zurkovich_api_key_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="openai_api_key">OpenAI API Key</label>
                </th>
                <td>
                    <input type="password" 
                           name="openai_api_key" 
                           id="openai_api_key" 
                           value="<?php echo esc_attr($current_api_key); ?>" 
                           class="regular-text"
                           placeholder="Enter your OpenAI API key">
                    <p class="description">Enter your OpenAI API key here. It will be stored securely.</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" 
                   name="submit_api_key" 
                   class="button button-primary" 
                   value="Save API Key">
        </p>
    </form>
</div> 