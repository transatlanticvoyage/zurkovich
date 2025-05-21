<?php
if (!defined('ABSPATH')) {
    exit;
}

$ai_prompt = '';
$ai_output = '';
if (isset($_POST['ai_prompt']) && isset($_POST['Ping AI Tool With This Prompt'])) {
    $ai_prompt = sanitize_textarea_field($_POST['ai_prompt']);
    $ai_output = function_prompt_ai_tool_and_receive_output_1($ai_prompt);
}
?>
<div class="wrap">
    <div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 4 - AI Tool Generate Content</div>
    <h1>Screen 4 - AI Tool Generate Content</h1>
    <form method="post" id="ai-tool-form">
        <div style="margin-bottom: 18px; font-weight: bold;">input your prompt here for the ai tool</div>
        <textarea name="ai_prompt" id="ai_prompt" style="width:550px; height:500px; margin-bottom:18px;"><?php echo esc_textarea($ai_prompt); ?></textarea>
        <br>
        <button type="submit" name="Ping AI Tool With This Prompt" class="button button-primary" style="margin-bottom: 24px;">Ping AI Tool With This Prompt</button>
        <div style="margin-bottom: 18px; font-weight: bold;">received output gotten back from ai tool</div>
        <textarea name="ai_output" id="ai_output" style="width:550px; height:500px;" readonly><?php echo esc_textarea($ai_output); ?></textarea>
    </form>
</div> 