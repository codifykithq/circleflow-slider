<?php

if (!defined('ABSPATH')) {
    exit;
}

class Circular_Slider_Admin_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_filter('manage_circular_slider_posts_columns', array($this, 'add_custom_columns'));
        add_action('manage_circular_slider_posts_custom_column', array($this, 'render_custom_columns'), 10, 2);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=circular_slider',
            __('CircleFlow Slider Help', 'circular-slider'),
            __('Help', 'circular-slider'),
            'manage_options',
            'circular-slider-guide',
            array($this, 'render_guide_page')
        );
    }

    public function add_custom_columns($columns) {
        $new = array();
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ('title' === $key) {
                $new['shortcode'] = __('Shortcode', 'circular-slider');
                $new['slides_count'] = __('Slides', 'circular-slider');
                $new['preview'] = __('Preview', 'circular-slider');
            }
        }
        return $new;
    }

    public function render_custom_columns($column, $post_id) {
        if ('shortcode' === $column) {
            printf('<button type="button" class="ckcs-copy-shortcode" data-copy="%1$s"><code>%1$s</code><span>Copy</span></button>', esc_attr('[circular_slider id="' . absint($post_id) . '"]'));
            return;
        }

        if ('slides_count' === $column) {
            $slides = get_post_meta($post_id, '_circular_slider_slides', true);
            printf('<span class="ckcs-count">%d</span>', is_array($slides) ? count($slides) : 0);
            return;
        }

        if ('preview' === $column) {
            $slides = get_post_meta($post_id, '_circular_slider_slides', true);
            if (is_array($slides) && !empty($slides[0]['image_id'])) {
                $url = wp_get_attachment_image_url(absint($slides[0]['image_id']), 'thumbnail');
                if ($url) {
                    printf('<img class="ckcs-list-preview" src="%s" alt="">', esc_url($url));
                }
            }
        }
    }

    public function render_guide_page() {
        $counts = wp_count_posts('circular_slider');
        $published = isset($counts->publish) ? (int) $counts->publish : 0;
        ?>
        <div class="wrap ckcs-help-wrap">
            <section class="ckcs-help-hero">
                <div>
                    <span class="ckcs-kicker">CODIFYKIT</span>
                    <h1>CircleFlow Slider</h1>
                    <p>Create responsive circular image sliders with animated labels, blurred backgrounds, autoplay and Elementor support.</p>
                </div>
                <div class="ckcs-help-stat"><strong><?php echo esc_html($published); ?></strong><span>Published sliders</span></div>
            </section>

            <div class="ckcs-help-grid">
                <section class="ckcs-help-card">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <h2>1. Create a slider</h2>
                    <p>Go to <strong>CircleFlow Slider → Add New Slider</strong>, add a title, configure the Slider Studio, and save.</p>
                </section>
                <section class="ckcs-help-card">
                    <span class="dashicons dashicons-format-gallery"></span>
                    <h2>2. Add slides</h2>
                    <p>Each slide needs a circular label and a center image. A separate background image is optional.</p>
                </section>
                <section class="ckcs-help-card">
                    <span class="dashicons dashicons-shortcode"></span>
                    <h2>3. Display it anywhere</h2>
                    <p>Use <code>[circular_slider id="123"]</code> in WordPress, or select the slider from the CircleFlow Slider Elementor widget.</p>
                </section>
                <section class="ckcs-help-card">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <h2>4. Fine-tune the design</h2>
                    <p>Use responsive circle sizes, image focus controls, active arc styling and customizable navigation buttons.</p>
                </section>
            </div>

            <section class="ckcs-help-wide">
                <div>
                    <h2>Autoplay that behaves correctly</h2>
                    <p>When the browser tab becomes hidden, autoplay pauses. Returning to the page starts a fresh delay instead of trying to catch up on missed transitions.</p>
                </div>
                <div>
                    <h2>Image recommendations</h2>
                    <p>Use high-resolution images. The default <strong>Cover</strong> mode fills the entire center circle; Horizontal Focus and Vertical Focus let you control the crop.</p>
                </div>
                <div>
                    <h2>Responsive setup</h2>
                    <p>Configure independent desktop, tablet and mobile circle sizes and section heights from the Responsive tab.</p>
                </div>
            </section>

            <section class="ckcs-help-footer">
                <div><h2>Need the latest release?</h2><p>CodifyKit builds practical WordPress tools with clean interfaces and predictable behavior.</p></div>
                <a class="button button-primary" href="https://codifykit.com/" target="_blank" rel="noopener noreferrer">Visit CodifyKit</a>
            </section>
        </div>
        <?php
    }
}

new Circular_Slider_Admin_Settings();
