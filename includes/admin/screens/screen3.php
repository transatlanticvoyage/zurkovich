<?php
if (!defined('ABSPATH')) {
    exit;
}

// Dynamically get all published pages
$pages = get_pages(array('post_status' => 'publish'));

// Determine selected page - now using GET instead of POST
$selected_page_id = isset($_GET['balarfi_page_id']) ? intval($_GET['balarfi_page_id']) : '';

// Handle scrape action
if (isset($_POST['scrape_temprex_fresh']) && $selected_page_id) {
    function_scrape_temprex_1($selected_page_id);
}

// Handle cache action for temprex_2
if (isset($_POST['cache_temprex_2']) && $selected_page_id) {
    $cached_content = isset($_POST['temprex_2_cached_by_hand']) ? sanitize_textarea_field($_POST['temprex_2_cached_by_hand']) : '';
    update_post_meta($selected_page_id, 'temprex_2_cached_by_hand', $cached_content);
}

// Handle cache action for temprex_3
if (isset($_POST['cache_temprex_3']) && $selected_page_id) {
    $cached_content = isset($_POST['temprex_3_cached_by_hand']) ? sanitize_textarea_field($_POST['temprex_3_cached_by_hand']) : '';
    update_post_meta($selected_page_id, 'temprex_3_cached_by_hand', $cached_content);
}

// Handle cache action for prexchor_rubrickey
if (isset($_POST['cache_prexchor_rubrickey']) && $selected_page_id) {
    $cached_content = isset($_POST['prexchor_rubrickey']) ? sanitize_textarea_field($_POST['prexchor_rubrickey']) : '';
    update_post_meta($selected_page_id, 'prexchor_rubrickey', $cached_content);
}

// Get the temprex_1_scraped value for the selected page
$temprex_1_scraped = '';
if ($selected_page_id) {
    $temprex_1_scraped = get_post_meta($selected_page_id, 'temprex_1_scraped', true);
}

// Get the temprex_2_cached_by_hand value for the selected page
$temprex_2_cached = '';
if ($selected_page_id) {
    $temprex_2_cached = get_post_meta($selected_page_id, 'temprex_2_cached_by_hand', true);
}

// Get the temprex_3_cached_by_hand value for the selected page
$temprex_3_cached = '';
if ($selected_page_id) {
    $temprex_3_cached = get_post_meta($selected_page_id, 'temprex_3_cached_by_hand', true);
}

// Get the prexchor_rubrickey value for the selected page
$prexchor_rubrickey = '';
if ($selected_page_id) {
    $prexchor_rubrickey = get_post_meta($selected_page_id, 'prexchor_rubrickey', true);
}
?>
<div class="wrap">
    <div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 3 - Prex Extract</div>
    <h1>Screen 3 - Prex Extract</h1>
    <form method="post" id="balarfi-form">
        <input type="hidden" name="page" value="zurkoscreen3" />
        <hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">
        <div style="width:100%;background:#d6ecff;color:#1a2333;font-weight:bold;font-size:1.1em;padding:8px 0 8px 12px;margin-bottom:10px;">Select A Page To Extract Codes From</div>
        <table class="form-table"><tbody>
            <tr>
                <th><label for="balarfi_page_id">Select a page</label></th>
                <td style="display:flex;align-items:center;">
                    <select name="balarfi_page_id" id="balarfi_page_id" style="margin-right:12px; min-width: 200px;" onchange="window.location.href='?page=zurkoscreen3&balarfi_page_id=' + this.value;">
                        <option value="">Select a page...</option>
                        <?php foreach ($pages as $page): ?>
                            <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_page_id, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="radio" name="kardwaj_radio" value="select" style="margin-left:8px;" checked>
                </td>
            </tr>
            <tr>
                <th><label for="kardwaj_default">Use default kardwaj page</label></th>
                <td style="display:flex;align-items:center;">
                    <input type="text" id="kardwaj_default" value="(default)" style="width:180px; margin-right:12px; background:#eee; color:#888; border:1px solid #ccc;" readonly />
                    <input type="radio" name="kardwaj_radio" value="default" style="margin-left:8px;">
                </td>
            </tr>
            <tr>
                <th><label for="manual_post_id">Type in a wp post id</label></th>
                <td style="display:flex;align-items:center;">
                    <input type="text" name="manual_post_id" id="manual_post_id" value="" style="width:120px; margin-right:12px;" />
                    <input type="radio" name="kardwaj_radio" value="manual" style="margin-left:8px;">
                </td>
            </tr>
        </tbody></table>
        <hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">
        <table class="form-table"><tbody>
            <tr>
                <th><label for="temprex_1_scraped">temprex_1_scraped</label><br />
                    <button type="submit" name="scrape_temprex_fresh" style="background:#111;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">scrape temprex fresh</button>
                    <div style="margin-top:8px; font-size:11px; font-weight:normal;">function_scrape_temprex_1</div>
                </th>
                <td colspan="2">
                    <div style="display:flex;gap:18px;">
                        <textarea id="temprex_1_scraped" name="temprex_1_scraped" style="width: 400px; height: 250px;" readonly><?php echo esc_textarea($temprex_1_scraped); ?></textarea>
                        <textarea id="temprex_1_scraped_bracketed" style="width: 400px; height: 250px;" readonly></textarea>
                    </div>
                </td>
            </tr>
            <tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>
            <tr>
                <th><label for="temprex_2_cached_by_hand">temprex_2_cached_by_hand</label><br />
                    <button type="submit" name="cache_temprex_2" style="background:#4a2c2a;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">cache now</button>
                </th>
                <td colspan="2">
                    <div style="display:flex;gap:18px;">
                        <textarea id="temprex_2_cached_by_hand" name="temprex_2_cached_by_hand" style="width: 400px; height: 250px;"><?php echo esc_textarea($temprex_2_cached); ?></textarea>
                        <textarea id="temprex_2_cached_by_hand_bracketed" style="width: 400px; height: 250px;" readonly></textarea>
                    </div>
                </td>
            </tr>
            <tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>
            <tr>
                <th><label for="temprex_3_cached_by_hand">temprex_3_cached_by_hand</label><br />
                    <button type="submit" name="cache_temprex_3" style="background:#4a2c2a;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">cache now</button>
                </th>
                <td colspan="2">
                    <div style="display:flex;gap:18px;">
                        <textarea id="temprex_3_cached_by_hand" name="temprex_3_cached_by_hand" style="width: 400px; height: 250px;"><?php echo esc_textarea($temprex_3_cached); ?></textarea>
                        <textarea id="temprex_3_cached_by_hand_bracketed" style="width: 400px; height: 250px;" readonly></textarea>
                    </div>
                </td>
            </tr>
            <tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>
            <tr>
                <th>
                    <button type="submit" name="cache_prexchor_rubrickey" style="background:#2c4a2a;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">cache now</button>
                </th>
                <td colspan="2">
                    <div style="display:flex;gap:18px;">
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            <div style="font-weight:bold;">ante_prexchor_rubrickey</div>
                            <textarea id="prexchor_rubrickey" name="prexchor_rubrickey" style="width: 400px; height: 250px;"><?php echo esc_textarea($prexchor_rubrickey); ?></textarea>
                            <button type="submit" name="generate_rubrickey" style="background:#111;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;">generate rubrickey from field above</button>
                            <div style="font-weight:bold;">prexchor_rubrickey</div>
                            <textarea id="prexchor_rubrickey_output" style="width: 400px; height: 250px;" readonly></textarea>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            <div style="height:21px;"></div>
                            <textarea id="prexchor_rubrickey_bracketed" style="width: 400px; height: 250px;" readonly></textarea>
                            <div style="height:42px;"></div>
                            <div style="height:21px;"></div>
                            <textarea id="prexchor_rubrickey_output_bracketed" style="width: 400px; height: 250px;" readonly></textarea>
                        </div>
                    </div>
                </td>
            </tr>
            <tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>
            <tr>
                <th><label for="zeeprex_submit">zeeprex_submit</label></th>
                <td colspan="2"></td>
            </tr>
        </tbody></table>
    </form>
</div> 