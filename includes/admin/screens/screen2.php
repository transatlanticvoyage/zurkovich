<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit_driggs'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zurko_driggs';
    
    $data = array(
        'driggs_domain' => sanitize_text_field($_POST['driggs_domain']),
        'driggs_industry' => sanitize_text_field($_POST['driggs_industry']),
        'driggs_city' => sanitize_text_field($_POST['driggs_city']),
        'driggs_brand_name_1' => sanitize_text_field($_POST['driggs_brand_name_1']),
        'driggs_site_type_or_purpose' => sanitize_textarea_field($_POST['driggs_site_type_or_purpose']),
        'driggs_email_1' => sanitize_email($_POST['driggs_email_1']),
        'driggs_address_1' => sanitize_text_field($_POST['driggs_address_1']),
        'driggs_phone1' => sanitize_text_field($_POST['driggs_phone1'])
    );

    $wpdb->insert($table_name, $data);
}
?>
<div class="wrap">
    <h1>Screen 2 - Driggs</h1>
    <hr>
    
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="driggs_id">driggs_id</label></th>
                <td><input type="text" name="driggs_id" id="driggs_id" class="regular-text" readonly></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_domain">driggs_domain</label></th>
                <td><input type="text" name="driggs_domain" id="driggs_domain" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_industry">driggs_industry</label></th>
                <td><input type="text" name="driggs_industry" id="driggs_industry" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_city">driggs_city</label></th>
                <td><input type="text" name="driggs_city" id="driggs_city" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_brand_name_1">driggs_brand_name_1</label></th>
                <td><input type="text" name="driggs_brand_name_1" id="driggs_brand_name_1" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_site_type_or_purpose">driggs_site_type_or_purpose</label></th>
                <td><textarea name="driggs_site_type_or_purpose" id="driggs_site_type_or_purpose" class="large-text" rows="5"></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_email_1">driggs_email_1</label></th>
                <td><input type="email" name="driggs_email_1" id="driggs_email_1" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_address_1">driggs_address_1</label></th>
                <td><input type="text" name="driggs_address_1" id="driggs_address_1" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="driggs_phone1">driggs_phone1</label></th>
                <td><input type="text" name="driggs_phone1" id="driggs_phone1" class="regular-text"></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="submit_driggs" class="button button-primary" value="Save">
        </p>
    </form>
</div> 