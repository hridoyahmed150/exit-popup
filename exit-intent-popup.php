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

    wp_enqueue_style('exit-intent-popup-css', plugin_dir_url(__FILE__) . 'assets/css/popup.css');

    wp_enqueue_script('exit-intent-popup-js', plugin_dir_url(__FILE__) . 'assets/js/popup.js', array(), null, true);

    $redirect_url = get_option('exit_intent_redirect_url', '/contact-us/');
    wp_localize_script('exit-intent-popup-js', 'exitIntentPopupSettings', array(
        'enablePopup' => get_option('exit_intent_enable_popup', '1'),
        'redirect_url' => esc_url($redirect_url),
    ));

}
add_action('wp_enqueue_scripts', 'exit_intent_popup_enqueue_scripts');

function exit_intent_popup_display()
{
    $enable_popup = get_option('exit_intent_enable_popup', '1');
    $custom_content = get_option('exit_intent_custom_content');
    $parentclass = get_option('exit_intent_parent_class');

    if ($enable_popup !== '1') {
        return;
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
                    } ?>

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
    register_setting('exit_intent_popup_settings', 'exit_intent_enable_popup');


    register_setting('exit_intent_popup_settings', 'exit_intent_title', 'wp_kses_post');
    register_setting('exit_intent_popup_settings', 'exit_intent_custom_content', 'sanitize_textarea_field');

}
add_action('admin_init', 'exit_intent_popup_settings_init');





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
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

