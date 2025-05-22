<?php
if (!defined('ABSPATH')) {
    exit;
}

// Dynamically get all published pages
$pages = get_pages(array('post_status' => 'publish'));

// Determine selected page
$selected_page_id = isset($_GET['balarfi_page_id']) ? intval($_GET['balarfi_page_id']) : '';

// Handle inject content action
if (isset($_POST['inject_zeeprex_content_2']) && $selected_page_id) {
    $zeeprex_content = isset($_POST['zeeprex_content']) ? wp_kses_post($_POST['zeeprex_content']) : '';
    update_post_meta($selected_page_id, 'zeeprex_submit', $zeeprex_content);
    
    if (!empty($zeeprex_content)) {
        function_inject_content_2($selected_page_id, $zeeprex_content);
    }
}
?>
<div class="wrap">
    <div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 4 - Inject Content 2</div>
    <h1>Screen 4 - Inject Content 2</h1>
    <form method="post" id="balarfi-form">
        <input type="hidden" name="page" value="zurkoscreen4" />
        <hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">
        <div style="width:100%;background:#d6ecff;color:#1a2333;font-weight:bold;font-size:1.1em;padding:8px 0 8px 12px;margin-bottom:10px;">Select A Page To Inject Content Into</div>
        <table class="form-table"><tbody>
            <tr>
                <th><label for="balarfi_page_id">Select a page</label></th>
                <td style="display:flex;align-items:center;">
                    <select name="balarfi_page_id" id="balarfi_page_id" style="margin-right:12px; min-width: 200px;" onchange="window.location.href='?page=zurkoscreen4&balarfi_page_id=' + this.value;">
                        <option value="">Select a page...</option>
                        <?php foreach ($pages as $page): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_page_id, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php if ($selected_page_id): ?>
            <tr>
                <th></th>
                <td style="padding-top: 8px;">
                    <a href="<?php echo get_edit_post_link($selected_page_id); ?>" target="_blank" style="margin-right: 16px;">editor - regular wordpress</a>
                    <a href="<?php echo get_edit_post_link($selected_page_id); ?>?elementor" target="_blank" style="margin-right: 16px;">editor - elementor</a>
                    <a href="<?php echo get_permalink($selected_page_id); ?>" target="_blank">view frontend of page</a>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="zeeprex_submit">zeeprex_submit</label></th>
                <td>
                    <textarea id="zeeprex_submit" name="zeeprex_content" style="width: 400px; height: 100px;"></textarea>
                    <br>
                    <button type="submit" name="inject_zeeprex_content_2" style="background:#800000;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">run function_inject_content_2</button>
                </td>
            </tr>
        </tbody></table>
    </form>
</div> 