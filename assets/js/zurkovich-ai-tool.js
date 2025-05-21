jQuery(document).ready(function($) {
    $('#ai-tool-form').on('submit', function(e) {
        e.preventDefault();
        var prompt = $('#ai_prompt').val();
        $('#ai-tool-loading').show();
        $('#ai_output').val('');
        $.ajax({
            url: zurkovichAiTool.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'zurkovich_ai_tool_prompt',
                nonce: zurkovichAiTool.nonce,
                prompt: prompt
            },
            success: function(response) {
                $('#ai-tool-loading').hide();
                if (response.success) {
                    $('#ai_output').val(response.data.output);
                } else {
                    $('#ai_output').val(response.data ? response.data : 'Error: No response from server.');
                }
            },
            error: function(xhr, status, error) {
                $('#ai-tool-loading').hide();
                $('#ai_output').val('AJAX error: ' + error);
            }
        });
        return false;
    });
}); 