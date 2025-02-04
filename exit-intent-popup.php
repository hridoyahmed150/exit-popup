<?php
/**
 * Plugin Name: EMG Exit Intent Popup
 * Description: A plugin to show an exit-intent popup with an address form.
 * Version: 1.0.3
 * Author: Hridoy Ahmed
 * License: GPL2
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

function exit_intent_popup_enqueue_scripts()
{
    // $custom_content = get_option('exit_intent_custom_content');
    $exclude_page = get_option('exclude_page');
    $current_page_id = get_the_ID();


    if ($exclude_page != $current_page_id) {
        wp_enqueue_style('exit-intent-popup-css', plugin_dir_url(__FILE__) . 'assets/css/popup.css');


        wp_enqueue_script('exit-intent-popup-js', plugin_dir_url(__FILE__) . 'assets/js/popup.js', array(), null, true);
    }

    $redirect_url = get_option('exit_intent_redirect_url', '/contact-us/');
    wp_localize_script('exit-intent-popup-js', 'exitIntentPopupSettings', array(
        'enablePopup' => get_option('exit_intent_enable_popup', '1'),
        'redirect_url' => esc_url($redirect_url),
    ));



    // if ($exclude_page != $current_page_id) {
    //     $google_maps_api_key = get_option('exit_intent_google_maps_api_key');
    //     $general_api_key = function_exists('emg_cmb2_get_general') ? emg_cmb2_get_general('general_api_key') : null;

    //     if ($google_maps_api_key || $general_api_key) {
    //         wp_enqueue_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '&libraries=places', [], null, true);
    //     }
    // }
}
add_action('wp_enqueue_scripts', 'exit_intent_popup_enqueue_scripts');

function exit_intent_popup_display()
{
    $exclude_page = get_option('exclude_page');

    if ($exclude_page == get_the_ID()) {
        return;
    }

    $enable_popup = get_option('exit_intent_enable_popup', '1');
    $custom_content = get_option('exit_intent_custom_content');
    $parentclass = get_option('exit_intent_parent_class');

    if ($enable_popup !== '1') {
        return;
    }
    // Get button background and text color options, checking if they are set.
    $button_text = get_option('exit_intent_button_text');

    $button_bg_color = get_option('exit_intent_button_bg_color');
    $button_text_color = get_option('exit_intent_button_text_color');
    $button_class = get_option('exit_intent_button_class');

    // Start building the inline style for the button.
    $button_style = '';
    if ($button_bg_color) {
        $button_style .= 'background-color: ' . esc_attr($button_bg_color) . ' !important; ';
    }
    if ($button_text_color) {
        $button_style .= 'color: ' . esc_attr($button_text_color) . ' !important; ';
    }

    $title = get_option('exit_intent_title');



    $allowed_html = [
        'h1' => [
            'class' => [],
        ],
        'h2' => [
            'class' => [],
        ],
        'h3' => [
            'class' => [],
        ],
        'h4' => [
            'class' => [],
        ],
        'h5' => [
            'class' => [],
        ],
        'h6' => [
            'class' => [],
        ],
        'p' => [
            'class' => [],
        ],
        'div' => [
            'class' => [],
            'id' => [],
        ],
        'span' => [
            'class' => [],
            'style' => [],
        ],
        'a' => [
            'href' => [],
            'title' => [],
            'class' => [],
        ],
        'strong' => [],
        'i' => [],
        'br' => [],
    ];

    ?>
    <div class="modal exit-intent-pop-up <?php echo esc_attr($parentclass); ?>" id="exitIntentPopup">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="modal-chose-button" data-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <?php
                    if (!empty($custom_content)) {
                        ?>
                        <div class="emg-modal-for-custom-content">
                            <?php
                            echo wp_kses($title, $allowed_html);

                            echo do_shortcode($custom_content);
                            ?>
                        </div>
                        <?php
                    } else { ?>


                        <?php echo wp_kses($title, $allowed_html); ?>

                        <form class="emg-popup-form d-flex flex-column align-item-center">
                            <input id="offer_autocomplete_popup" class="form-control search_input" type="text"
                                placeholder="Enter your address here..." name="address" required />
                            <!-- <span class="validation_error">This field is required.</span> -->
                            <a type="submit"
                                class="btn trigger-emg-popup-plugin mx-auto mt-4 <?php echo esc_attr($button_class); ?>"
                                id="emg-exist-location-btn" style="<?php echo $button_style; ?>">
                                <?php echo esc_html($button_text); ?>
                            </a>
                        </form>

                        <!-- shortcode support  -->
                        <!-- <?php echo do_shortcode('[contact-form-7 id="5" title="Contact form 1"]'); ?> -->
                        <div style="display:none">
                            <form id="address_placeholder_popup" class="form autofill" method="GET"
                                action="<?php echo esc_url(get_option('exit_intent_redirect_url', '/contact-us/')); ?>">
                                <table id="address">
                                    <tbody>
                                        <tr>
                                            <td class="label">Street address</td>
                                            <td class="slimField"><input id="street_number" disabled="true"
                                                    name="street_number" /></td>
                                            <td class="wideField" colspan="2"><input id="route" disabled="true" name="route" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">City</td>
                                            <td class="wideField" colspan="3"><input id="locality" disabled="true"
                                                    name="locality" /></td>
                                        </tr>
                                        <tr>
                                            <td class="label">State</td>
                                            <td class="slimField"><input id="administrative_area_level_1" disabled="true"
                                                    name="administrative_area_level_1" /></td>
                                            <td class="label">Zip code</td>
                                            <td class="wideField"><input id="postal_code" disabled="true" name="postal_code" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">Country</td>
                                            <td class="wideField" colspan="3"><input id="country" disabled="true"
                                                    name="country" /></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'exit_intent_popup_display');

// Initialize the plugin settings
function exit_intent_popup_settings_init()
{
    register_setting('exit_intent_popup_settings', 'exit_intent_google_maps_api_key');
    register_setting('exit_intent_popup_settings', 'exit_intent_enable_popup');
    register_setting('exit_intent_popup_settings', 'exit_intent_parent_class');
    register_setting('exit_intent_popup_settings', 'exit_intent_redirect_url');
    register_setting('exit_intent_popup_settings', 'exit_intent_title', 'wp_kses_post');
    register_setting('exit_intent_popup_settings', 'exit_intent_custom_content', 'sanitize_textarea_field');
    register_setting('exit_intent_popup_settings', 'exclude_page');

    // Register color fields
    register_setting('exit_intent_popup_settings', 'exit_intent_button_bg_color');
    register_setting('exit_intent_popup_settings', 'exit_intent_button_text_color');
    register_setting('exit_intent_popup_settings', 'exit_intent_button_class');
    register_setting('exit_intent_popup_settings', 'exit_intent_button_text');

    // Add settings fields
    add_settings_section('exit_intent_button_settings', 'Popup Button Settings', null, 'exit-intent-settings');
    add_settings_field('exit_intent_button_bg_color', 'Button Background Color', 'exit_intent_button_bg_color_field', 'exit-intent-settings', 'exit_intent_button_settings');
    add_settings_field('exit_intent_button_text_color', 'Button Text Color', 'exit_intent_button_text_color_field', 'exit-intent-settings', 'exit_intent_button_settings');
    add_settings_field('exit_intent_button_class', 'Button Class', 'exit_intent_button_class_field', 'exit-intent-settings', 'exit_intent_button_settings');
}
add_action('admin_init', 'exit_intent_popup_settings_init');

function exit_intent_button_bg_color_field()
{
    $bg_color = get_option('exit_intent_button_bg_color', '#ffffff');  // Default color is white
    echo '<input type="color" name="exit_intent_button_bg_color" value="' . esc_attr($bg_color) . '" />';
}

// Display the button text color field
function exit_intent_button_text_color_field()
{
    $text_color = get_option('exit_intent_button_text_color', '#000000');  // Default color is black
    echo '<input type="color" name="exit_intent_button_text_color" value="' . esc_attr($text_color) . '" />';
}

// Display the button class field (optional)
function exit_intent_button_class_field()
{
    $button_class = get_option('exit_intent_button_class', ''); // Default empty class
    echo '<input type="text" name="exit_intent_button_class" value="' . esc_attr($button_class) . '" />';
}

// Add Settings Page
function exit_intent_popup_add_admin_menu()
{
    add_options_page(
        'Exit Intent Popup Settings',
        'Exit Intent Popup',
        'manage_options',
        'exit_intent_popup',
        'exit_intent_popup_settings_page'
    );
}
add_action('admin_menu', 'exit_intent_popup_add_admin_menu');

// Display Settings Page
function exit_intent_popup_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Exit Intent Popup Settings</h1>
        <form action="options.php" method="POST">
            <?php
            settings_fields('exit_intent_popup_settings');
            do_settings_sections('exit_intent_popup_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Popup</th>
                    <td>
                        <input type="checkbox" id="exit_intent_enable_popup" name="exit_intent_enable_popup" value="1" <?php
                        echo esc_attr(checked(get_option('exit_intent_enable_popup'), '1', false));
                        ?> />
                        <label for="exit_intent_enable_popup">Show Exit Intent Popup</label>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Google Maps API Key</th>
                    <td><input type="text" name="exit_intent_google_maps_api_key"
                            value="<?php echo esc_attr(get_option('exit_intent_google_maps_api_key')); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Parent Class</th>
                    <td>
                        <input type="text" name="exit_intent_parent_class"
                            value="<?php echo esc_attr(get_option('exit_intent_parent_class')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Popup Title</th>
                    <td>
                        <?php
                        $content = get_option('exit_intent_title');
                        $editor_id = 'exit_intent_title_editor';
                        wp_editor($content, $editor_id, array(
                            'textarea_name' => 'exit_intent_title',
                            'textarea_rows' => 5,
                            'media_buttons' => false,
                        ));
                        ?>
                        <p class="description">This title will appear at the top of the exit intent popup.</p>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Button Text</th>
                    <td>
                        <input type="text" name="exit_intent_button_text"
                            value="<?php echo esc_attr(get_option('exit_intent_button_text')); ?>" class="regular-text" />
                        <p class="description">Add custom text for the button if needed.</p>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Button Background Color</th>
                    <td>
                        <input type="text" name="exit_intent_button_bg_color"
                            value="<?php echo esc_attr(get_option('exit_intent_button_bg_color')); ?>"
                            class="regular-text wp-color-picker-field" data-default-color="#ff4500" />
                        <p class="description">Select the background color for the button (leave blank to use default).</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Button Text Color</th>
                    <td>
                        <input type="text" name="exit_intent_button_text_color"
                            value="<?php echo esc_attr(get_option('exit_intent_button_text_color')); ?>"
                            class="regular-text wp-color-picker-field" data-default-color="#ffffff" />
                        <p class="description">Select the text color for the button (leave blank to use default).</p>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Button Class</th>
                    <td>
                        <input type="text" name="exit_intent_button_class"
                            value="<?php echo esc_attr(get_option('exit_intent_button_class', )); ?>"
                            class="regular-text" />
                        <p class="description">Add custom CSS class for the button if needed.</p>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Exclude Page</th>
                    <td>
                        <!-- Single select page option for exclude page -->
                        <select name="exclude_page" id="exclude_page">
                            <option value="">Select a page to exclude</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                echo '<option value="' . $page->ID . '" ' . selected(get_option('exclude_page'), $page->ID, false) . '>' . $page->post_title . '</option>';
                            }
                            ?>
                        </select>
                        <p class="description">Select a page to exclude the Google Maps API script from loading on that
                            page.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Custom Content</th>
                    <td>
                        <?php
                        $content = get_option('exit_intent_custom_content');
                        $editor_id = 'exit_intent_custom_content_editor';
                        wp_editor($content, $editor_id, array(
                            'textarea_name' => 'exit_intent_custom_content',
                            'textarea_rows' => 5,
                            'media_buttons' => false,
                        ));
                        ?>
                        <p class="description">This custom content will be used for the popup. You can use rich text
                            formatting and shortcodes.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Redirect URL After Form Submission</th>
                    <td>
                        <input type="text" name="exit_intent_redirect_url"
                            value="<?php echo esc_attr(get_option('exit_intent_redirect_url')); ?>"
                            placeholder="Enter the redirect URL after form submission" />
                        <p class="description">This URL will be used for redirection after form submission.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

