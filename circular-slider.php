<?php
/**
 * Plugin Name: CircleFlow Slider – Circular Image Slider for WordPress
 * Plugin URI: https://codifykit.com/
 * Description: A modern circular image slider for WordPress with Elementor support, responsive controls, circular text, autoplay, and customizable navigation.
 * Version: 1.3.1
 * Author: Ali Vanaei
 * Author URI: https://codifykit.com/
 * Text Domain: circular-slider
 * Domain Path: /languages
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CIRCULAR_SLIDER_VERSION', '1.3.1');
define('CIRCULAR_SLIDER_PATH', plugin_dir_path(__FILE__));
define('CIRCULAR_SLIDER_URL', plugin_dir_url(__FILE__));

final class Circular_Slider_Plugin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        require_once CIRCULAR_SLIDER_PATH . 'includes/class-circular-slider.php';
        require_once CIRCULAR_SLIDER_PATH . 'includes/class-admin-settings.php';
    }

    private function init_hooks() {
        add_action('init', array($this, 'register_post_type'));
        add_action('wp_enqueue_scripts', array($this, 'register_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
    }

    public function register_frontend_assets() {
        wp_register_style(
            'circular-slider-css',
            CIRCULAR_SLIDER_URL . 'assets/css/circular-slider.css',
            array(),
            CIRCULAR_SLIDER_VERSION
        );

        wp_register_script(
            'circular-slider-js',
            CIRCULAR_SLIDER_URL . 'assets/js/circular-slider.js',
            array('jquery'),
            CIRCULAR_SLIDER_VERSION,
            true
        );
    }

    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        if (!$screen || 'circular_slider' !== $screen->post_type) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        wp_enqueue_style(
            'circular-slider-admin',
            CIRCULAR_SLIDER_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            CIRCULAR_SLIDER_VERSION
        );
        wp_enqueue_script(
            'circular-slider-admin',
            CIRCULAR_SLIDER_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable', 'wp-color-picker'),
            CIRCULAR_SLIDER_VERSION,
            true
        );
    }

    public function register_post_type() {
        $labels = array(
            'name'               => __('CircleFlow Sliders', 'circular-slider'),
            'singular_name'      => __('CircleFlow Slider', 'circular-slider'),
            'add_new'            => __('Add New Slider', 'circular-slider'),
            'add_new_item'       => __('Add New Slider', 'circular-slider'),
            'edit_item'          => __('Edit Slider', 'circular-slider'),
            'new_item'           => __('New Slider', 'circular-slider'),
            'view_item'          => __('View Slider', 'circular-slider'),
            'search_items'       => __('Search Sliders', 'circular-slider'),
            'not_found'          => __('No sliders found.', 'circular-slider'),
            'not_found_in_trash' => __('No sliders found in Trash.', 'circular-slider'),
            'menu_name'          => __('CircleFlow Slider', 'circular-slider'),
        );

        register_post_type('circular_slider', array(
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-images-alt2',
            'supports'     => array('title'),
            'has_archive'  => false,
            'rewrite'      => false,
            'show_in_rest' => false,
        ));
    }

    public function register_elementor_widget($widgets_manager) {
        if (!did_action('elementor/loaded')) {
            return;
        }
        require_once CIRCULAR_SLIDER_PATH . 'includes/class-elementor-widget.php';
        if (class_exists('Circular_Slider_Elementor_Widget')) {
            $widgets_manager->register(new Circular_Slider_Elementor_Widget());
        }
    }
}

Circular_Slider_Plugin::get_instance();

register_activation_hook(__FILE__, static function () {
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, static function () {
    flush_rewrite_rules();
});
