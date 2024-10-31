<?php
/*
Plugin Name: Quick Edit ACF Content
Plugin URI: https://quick-acf.com
Description: High speed ACF content editor for quickly editing and updating ACF fields in posts.
Author: MJ/LS / Quick ACF
Version: 1.1
Author URI: https://www.quick-acf.com
License: GPLv3 or later
*/

// Security: Abort if this file is called directly
if (!defined('ABSPATH')) {
    die('Direct access not allowed.');
}

// Add form head for ACF forms
function qacf_add_form_head() {
    acf_form_head();
}
add_action('wp_head', 'qacf_add_form_head');

// Custom HTML for Quick ACF Content
function qacf_custom_head_html() {
    if (!is_admin() && function_exists('get_field') && is_user_logged_in()) {


$qacf_liveedit = '<a href="#" ref="Off" id="qacf_liveedit_swift">Live edit: Off</a>';
        if (isset($_COOKIE['qacfLiveEdit']))
        {
$qacf_liveedit = '<a href="#" ref="'.$_COOKIE['qacfLiveEdit'].'" id="qacf_liveedit_swift">Live edit: '.$_COOKIE['qacfLiveEdit'].'</a>';
        }



        $current_post_id = get_the_ID();
        $atts = [
            'post_id' => $current_post_id,
            'post_type' => get_post_type($current_post_id),
            'form_id' => false,
            'html_before_fields' => '<div id="quick-acf-top-submit" class="acf-form-submit">'.$qacf_liveedit.'<input type="submit" class="acf-button button primary-button" value="' . esc_attr__('Update', 'quick-acf') . '"><span class="acf-spinner"></span></div>',
            'html_after_fields' => '',
            'submit_value' => __("Update", 'quick-acf'),
            'html_submit_spinner' => '<span class="acf-spinner"></span>',
            'updated_message' => __('Fields successfully updated.', 'quick-acf')
        ];

        echo '<div id="quick-acf-panel"><div class="quick-acf-header"><a href="#submit" class="header-submit-btn">' . esc_html__('Update', 'quick-acf') . '</a>Quick Edit ACF<a href="#close-quick-acf-panel" id="close-quick-acf-panel"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" fill="#ffffff"></path></svg></a></div><div id="quick-acf-panel-content">';
        echo esc_html(acf_form($atts));
        echo '</div></div>';
    }
}
add_action('wp_head', 'qacf_custom_head_html');

// Enqueue necessary scripts and styles
function qacf_enqueue_scripts() {
    if (is_user_logged_in()) {
        wp_enqueue_script('qacf-script', plugins_url('js/quick-acf.js?pp', __FILE__), ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], '1.0.0', true);
        wp_register_style('qacf-style', plugins_url('css/quick-acf.css?pp', __FILE__), [], '1.0.0');
        wp_enqueue_style('qacf-style');
        wp_register_style('jquery-ui-css', plugins_url('css/jquery-ui.css', __FILE__), [], '1.0.0');
        wp_enqueue_style('jquery-ui-css');
    }
}
add_action('wp_enqueue_scripts', 'qacf_enqueue_scripts');

// Admin bar button for Quick ACF
function qacf_admin_bar_button() {
    global $wp_admin_bar;
    if (!is_admin() && function_exists('get_field')) {
        $current_post_id = get_the_ID();
        $fields = get_field_objects($current_post_id);
        if (is_array($fields)) {
            $wp_admin_bar->add_menu([
                'id' => 'qacf-admin-bar-button',
                'title' => "Quick ACF",
                'href' => '#quick-acf-toggle',
                'meta' => [
                    'class' => 'qacf-admin-bar-button',
                    'title' => 'Quick ACF'
                ]
            ]);
        }
    }
}
add_action('wp_before_admin_bar_render', 'qacf_admin_bar_button');

// Check for ACF dependency on activation
function qacf_check_acf_dependency() {
    if (!class_exists('ACF')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires Advanced Custom Fields (ACF) to be installed and activated. Therefore, the plugin has been deactivated.');
    }
}
register_activation_hook(__FILE__, 'qacf_check_acf_dependency');

// Format ACF value by wrapping it in a span
function qacf_wrap_acf_data_in_span($value, $post_id, $field) {


if (isset($_COOKIE['qacfLiveEdit']) && $_COOKIE['qacfLiveEdit'] == "Off") {
    return $value;
}
else if(!isset($_COOKIE['qacfLiveEdit'])){

    return $value;
}



    if (is_user_logged_in() && !empty($value) && in_array($field['type'], ['text', 'wysiwyg', 'textarea'])) {
        if (is_a($value, 'WP_Post')) {
            $value = $value->post_title;
        }
        $value = '<span class="quick-acf-hotspot ' . esc_attr($field['key']) . '" title="#acf-' . esc_attr($field['key']) . '">' . $value . '</span>';
    }
    return $value;
}
add_filter('acf/format_value', 'qacf_wrap_acf_data_in_span', 10, 3);
