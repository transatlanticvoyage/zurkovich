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
    $cached_content = isset($_POST['ante_prexchor_rubrickey']) ? sanitize_textarea_field($_POST['ante_prexchor_rubrickey']) : '';
    update_post_meta($selected_page_id, 'ante_prexchor_rubrickey', $cached_content);
}

// Handle generate rubrickey action
if (isset($_POST['generate_rubrickey']) && $selected_page_id) {
    // Get Elementor data for the selected page
    $elementor_data = get_post_meta($selected_page_id, '_elementor_data', true);
    if (!empty($elementor_data)) {
        function_create_prexchor_rubrickey_2($selected_page_id);
    }
}

// Handle inject content action for zeeprex_submit
if (isset($_POST['inject_zeeprex_content_2']) && $selected_page_id) {
    $zeeprex_content = isset($_POST['zeeprex_content']) ? wp_kses_post($_POST['zeeprex_content']) : '';
    update_post_meta($selected_page_id, 'zeeprex_submit', $zeeprex_content);
    
    if (!empty($zeeprex_content)) {
        function_inject_content_2($selected_page_id, $zeeprex_content);
    }
}

// Handle prexnar1 and prexnar2 custom fields
if (isset($_POST['save_prexnar_fields']) && $selected_page_id) {
    $prexnar1 = isset($_POST['prexnar1']) ? sanitize_textarea_field($_POST['prexnar1']) : '';
    $prexnar2 = isset($_POST['prexnar2']) ? sanitize_textarea_field($_POST['prexnar2']) : '';
    update_post_meta($selected_page_id, 'prexnar1', $prexnar1);
    update_post_meta($selected_page_id, 'prexnar2', $prexnar2);
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

// Get the ante_prexchor_rubrickey value for the selected page
$ante_prexchor_rubrickey = '';
if ($selected_page_id) {
    $ante_prexchor_rubrickey = get_post_meta($selected_page_id, 'ante_prexchor_rubrickey', true);
}

// Get the prexchor_rubrickey value for the selected page
$prexchor_rubrickey = '';
if ($selected_page_id) {
    $prexchor_rubrickey = get_post_meta($selected_page_id, 'prexchor_rubrickey', true);
}
?>
<div class="wrap">
    <div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 5 - Prex All Encompassing</div>
    <h1>Screen 5 - Prex All Encompassing</h1>
    <form method="post" id="balarfi-form">
        <input type="hidden" name="page" value="zurkoscreen5" />
        <hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">
        <div style="width:100%;background:#d6ecff;color:#1a2333;font-weight:bold;font-size:1.1em;padding:8px 0 8px 12px;margin-bottom:10px;">Select A Page To Extract Codes From</div>
        <table class="form-table"><tbody>
            <tr>
                <th><label for="balarfi_page_id">Select a page</label></th>
                <td style="display:flex;align-items:center;">
                    <select name="balarfi_page_id" id="balarfi_page_id" style="margin-right:12px; min-width: 200px;" onchange="window.location.href='?page=zurkoscreen5&balarfi_page_id=' + this.value;">
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
            <?php if ($selected_page_id): ?>
            <tr>
                <th></th>
                <td style="padding-top: 8px;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="<?php echo get_edit_post_link($selected_page_id); ?>" target="_blank">editor - regular wordpress</a>
                        <a href="<?php echo get_edit_post_link($selected_page_id); ?>&action=elementor" target="_blank">editor - elementor</a>
                        <a href="<?php echo get_permalink($selected_page_id); ?>" target="_blank">view frontend of page</a>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
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
                </th>
                <td colspan="2">
                    <div style="display:flex;gap:18px;">
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            <div style="font-weight:bold;">ante_prexchor_rubrickey</div>
                            <textarea id="ante_prexchor_rubrickey" name="ante_prexchor_rubrickey" style="width: 400px; height: 250px;"><?php echo esc_textarea($ante_prexchor_rubrickey); ?></textarea>
                            <button type="submit" name="cache_prexchor_rubrickey" style="background:#2c4a2a;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;">cache now</button>
                            <button type="submit" name="generate_rubrickey" style="background:#111;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;">generate rubrickey from field above</button>
                            <div style="margin-top:8px; font-size:11px; font-weight:normal;">function_create_prexchor_rubrickey_2</div>
                            <div style="font-weight:bold;">prexchor_rubrickey</div>
                            <textarea id="prexchor_rubrickey_output" name="prexchor_rubrickey_output" style="width: 400px; height: 250px;" readonly><?php echo esc_textarea($prexchor_rubrickey); ?></textarea>
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
                <td colspan="2">
                    <div style="margin-bottom: 8px; color: #666;">Make sure your codes are preceded by a "#" symbol</div>
                    <textarea id="zeeprex_submit" name="zeeprex_content" style="width: 400px; height: 100px;"></textarea>
                    <br>
                    <button type="submit" name="inject_zeeprex_content_2" style="background:#800000;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">run function_inject_content_2</button>
                </td>
            </tr>
            <tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>
            <tr>
                <th><label for="prexnar1">prexnar1</label></th>
                <td colspan="2">
                    <textarea id="prexnar1" name="prexnar1" style="width: 400px; height: 60px;"></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="prexnar2">prexnar2</label></th>
                <td colspan="2">
                    <textarea id="prexnar2" name="prexnar2" style="width: 400px; height: 60px;"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <button type="submit" name="save_prexnar_fields" style="background:#222;color:#fff;font-weight:bold;padding:6px 16px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">Save prexnar fields</button>
                </td>
            </tr>
        </tbody></table>
    </form>
</div> 