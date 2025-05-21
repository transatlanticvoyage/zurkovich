<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue JS for AJAX
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'toplevel_page_zurkoscreen4' || $hook === 'zurkoscreen4_page_zurkoscreen4') {
        wp_enqueue_script('zurkovich-ai-tool', plugin_dir_url(__FILE__) . '../../../../assets/js/zurkovich-ai-tool.js', array('jquery'), null, true);
        wp_localize_script('zurkovich-ai-tool', 'zurkovichAiTool', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('zurkovich_ai_tool_nonce'),
        ));
    }
});
?>
<div class="wrap">
    <div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 4 - AI Tool Generate Content</div>
    <h1>Screen 4 - AI Tool Generate Content</h1>
    <form id="ai-tool-form">
        <div style="margin-bottom: 18px; font-weight: bold;">input your prompt here for the ai tool</div>
        <textarea name="ai_prompt" id="ai_prompt" style="width:550px; height:500px; margin-bottom:18px;"></textarea>
        <br>
        <button type="submit" class="button button-primary" style="margin-bottom: 24px;">Ping AI Tool With This Prompt</button>
        <div id="ai-tool-loading" style="display:none; margin-bottom:12px; color:#0073aa; font-weight:bold;">Loading...</div>
        <div style="margin-bottom: 18px; font-weight: bold;">received output gotten back from ai tool</div>
        <textarea name="ai_output" id="ai_output" style="width:550px; height:500px;" readonly></textarea>
    </form>
</div> 