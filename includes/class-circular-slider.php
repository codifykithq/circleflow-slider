<?php

if (!defined('ABSPATH')) {
    exit;
}

class Circular_Slider {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_circular_slider', array($this, 'save_meta_boxes'));
        add_shortcode('circular_slider', array($this, 'render_shortcode'));
    }

    public static function defaults() {
        return array(
            'circle_size'            => 500,
            'circle_size_tablet'     => 390,
            'circle_size_mobile'     => 280,
            'section_height'         => 100,
            'section_height_tablet'  => 72,
            'section_height_mobile'  => 62,
            'transition_speed'       => 900,
            'autoplay'               => true,
            'autoplay_delay'         => 5000,
            'pause_on_hover'         => true,
            'bg_opacity'             => 0.82,
            'bg_blur'                => 16,
            'bg_scale'               => 1.08,
            'image_fit'              => 'cover',
            'image_position_x'       => 50,
            'image_position_y'       => 50,
            'image_scale'            => 1,
            'circle_border_width'    => 2,
            'circle_border_color'    => '#ffffff',
            'circle_border_opacity'  => 0.35,
            'text_font'              => 'inherit',
            'text_size'              => 18,
            'text_weight'            => 700,
            'text_letter_spacing'    => 1,
            'text_color'             => '#ffffff',
            'text_bg_color'          => '#000000',
            'text_bg_opacity'        => 0.92,
            'bg_distance'            => 10,
            'bg_thickness'           => 30,
            'arrow_color'            => '#111827',
            'arrow_size'             => 24,
            'arrow_button_size'      => 48,
            'arrow_offset'           => 60,
            'arrow_bg_color'         => '#ffffff',
            'arrow_bg_opacity'       => 0,
            'arrow_border_color'     => '#ffffff',
            'arrow_border_width'     => 0,
            'arrow_radius'           => 12,
            'hide_arrows_mobile'     => false,
            'section_padding_top'    => 0,
            'section_padding_bottom' => 0,
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'circular_slider_settings',
            __('CircleFlow Studio', 'circular-slider'),
            array($this, 'render_settings_meta_box'),
            'circular_slider',
            'normal',
            'high'
        );

        add_meta_box(
            'circular_slider_slides',
            __('Slides', 'circular-slider'),
            array($this, 'render_slides_meta_box'),
            'circular_slider',
            'normal',
            'high'
        );
    }

    private function field_number($key, $label, $settings, $args = array()) {
        $args = wp_parse_args($args, array('min' => 0, 'max' => 9999, 'step' => 1, 'suffix' => '', 'help' => ''));
        ?>
        <label class="ckcs-field">
            <span class="ckcs-field__label"><?php echo esc_html($label); ?></span>
            <span class="ckcs-input-wrap">
                <input type="number" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($settings[$key]); ?>" min="<?php echo esc_attr($args['min']); ?>" max="<?php echo esc_attr($args['max']); ?>" step="<?php echo esc_attr($args['step']); ?>">
                <?php if ($args['suffix']) : ?><span class="ckcs-suffix"><?php echo esc_html($args['suffix']); ?></span><?php endif; ?>
            </span>
            <?php if ($args['help']) : ?><small><?php echo esc_html($args['help']); ?></small><?php endif; ?>
        </label>
        <?php
    }

    private function field_color($key, $label, $settings, $help = '') {
        ?>
        <label class="ckcs-field">
            <span class="ckcs-field__label"><?php echo esc_html($label); ?></span>
            <input type="text" class="ckcs-color-picker" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($settings[$key]); ?>">
            <?php if ($help) : ?><small><?php echo esc_html($help); ?></small><?php endif; ?>
        </label>
        <?php
    }

    private function field_switch($key, $label, $settings, $help = '') {
        ?>
        <label class="ckcs-switch-field">
            <span>
                <strong><?php echo esc_html($label); ?></strong>
                <?php if ($help) : ?><small><?php echo esc_html($help); ?></small><?php endif; ?>
            </span>
            <span class="ckcs-switch">
                <input type="checkbox" name="<?php echo esc_attr($key); ?>" value="1" <?php checked(!empty($settings[$key])); ?>>
                <i></i>
            </span>
        </label>
        <?php
    }

    public function render_settings_meta_box($post) {
        wp_nonce_field('circular_slider_meta_box', 'circular_slider_nonce');
        $settings = wp_parse_args((array) get_post_meta($post->ID, '_circular_slider_settings', true), self::defaults());
        ?>
        <div class="ckcs-studio">
            <div class="ckcs-studio-hero">
                <div>
                    <span class="ckcs-kicker">CODIFYKIT · CIRCULAR SLIDER</span>
                    <h2><?php esc_html_e('Design the slider without touching code.', 'circular-slider'); ?></h2>
                    <p><?php esc_html_e('Responsive sizing, image crop, circular text, background effects and navigation controls in one place.', 'circular-slider'); ?></p>
                </div>
                <div class="ckcs-status-pill"><span></span><?php esc_html_e('Frontend-safe settings', 'circular-slider'); ?></div>
            </div>

            <div class="ckcs-tabs" role="tablist">
                <button type="button" class="ckcs-tab is-active" data-tab="general">General</button>
                <button type="button" class="ckcs-tab" data-tab="visual">Visual</button>
                <button type="button" class="ckcs-tab" data-tab="text">Circular Text</button>
                <button type="button" class="ckcs-tab" data-tab="navigation">Navigation</button>
                <button type="button" class="ckcs-tab" data-tab="responsive">Responsive</button>
            </div>

            <div class="ckcs-panel is-active" data-panel="general">
                <div class="ckcs-card-grid">
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-controls-play"></span><div><h3>Motion</h3><p>Timing and autoplay behavior.</p></div></div>
                        <div class="ckcs-fields two-col">
                            <?php $this->field_number('transition_speed', 'Transition speed', $settings, array('min'=>100,'max'=>5000,'step'=>50,'suffix'=>'ms')); ?>
                            <?php $this->field_number('autoplay_delay', 'Autoplay delay', $settings, array('min'=>1000,'max'=>30000,'step'=>250,'suffix'=>'ms')); ?>
                        </div>
                        <?php $this->field_switch('autoplay', 'Autoplay', $settings, 'Advance slides automatically.'); ?>
                        <?php $this->field_switch('pause_on_hover', 'Pause on hover', $settings, 'Restart with a fresh delay after the pointer leaves.'); ?>
                        <div class="ckcs-note">Autoplay automatically pauses while the browser tab is hidden. Returning to the tab starts a fresh delay, so missed time never causes rapid catch-up transitions.</div>
                    </section>
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-align-center"></span><div><h3>Section</h3><p>Overall viewport footprint.</p></div></div>
                        <div class="ckcs-fields two-col">
                            <?php $this->field_number('section_padding_top', 'Top padding', $settings, array('min'=>0,'max'=>300,'suffix'=>'px')); ?>
                            <?php $this->field_number('section_padding_bottom', 'Bottom padding', $settings, array('min'=>0,'max'=>300,'suffix'=>'px')); ?>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ckcs-panel" data-panel="visual">
                <div class="ckcs-card-grid">
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-format-image"></span><div><h3>Center image</h3><p>Make the image fill the circle exactly.</p></div></div>
                        <label class="ckcs-field"><span class="ckcs-field__label">Image fit</span><select name="image_fit"><option value="cover" <?php selected($settings['image_fit'],'cover'); ?>>Cover (recommended)</option><option value="contain" <?php selected($settings['image_fit'],'contain'); ?>>Contain</option></select><small>Cover fills the entire circle and crops only what is necessary.</small></label>
                        <div class="ckcs-fields two-col">
                            <?php $this->field_number('image_position_x', 'Horizontal focus', $settings, array('min'=>0,'max'=>100,'suffix'=>'%')); ?>
                            <?php $this->field_number('image_position_y', 'Vertical focus', $settings, array('min'=>0,'max'=>100,'suffix'=>'%')); ?>
                            <?php $this->field_number('image_scale', 'Image scale', $settings, array('min'=>1,'max'=>2,'step'=>0.05,'suffix'=>'×')); ?>
                        </div>
                    </section>
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-admin-appearance"></span><div><h3>Circle frame</h3><p>Border around the center image.</p></div></div>
                        <div class="ckcs-fields two-col">
                            <?php $this->field_number('circle_border_width', 'Border width', $settings, array('min'=>0,'max'=>20,'suffix'=>'px')); ?>
                            <?php $this->field_number('circle_border_opacity', 'Border opacity', $settings, array('min'=>0,'max'=>1,'step'=>0.05)); ?>
                        </div>
                        <?php $this->field_color('circle_border_color', 'Border color', $settings); ?>
                    </section>
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-cover-image"></span><div><h3>Background</h3><p>Blurred full-screen background treatment.</p></div></div>
                        <div class="ckcs-fields three-col">
                            <?php $this->field_number('bg_opacity', 'Opacity', $settings, array('min'=>0,'max'=>1,'step'=>0.05)); ?>
                            <?php $this->field_number('bg_blur', 'Blur', $settings, array('min'=>0,'max'=>80,'suffix'=>'px')); ?>
                            <?php $this->field_number('bg_scale', 'Scale', $settings, array('min'=>1,'max'=>1.5,'step'=>0.01,'suffix'=>'×')); ?>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ckcs-panel" data-panel="text">
                <div class="ckcs-card-grid">
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-editor-textcolor"></span><div><h3>Typography</h3><p>Style labels distributed around the circle.</p></div></div>
                        <label class="ckcs-field"><span class="ckcs-field__label">Font family</span><input type="text" name="text_font" value="<?php echo esc_attr($settings['text_font']); ?>" placeholder="inherit"><small>Use inherit to follow your theme, or enter a CSS font-family.</small></label>
                        <div class="ckcs-fields three-col">
                            <?php $this->field_number('text_size', 'Font size', $settings, array('min'=>9,'max'=>48,'suffix'=>'px')); ?>
                            <?php $this->field_number('text_weight', 'Font weight', $settings, array('min'=>100,'max'=>900,'step'=>100)); ?>
                            <?php $this->field_number('text_letter_spacing', 'Letter spacing', $settings, array('min'=>-2,'max'=>10,'step'=>0.1,'suffix'=>'px')); ?>
                        </div>
                        <?php $this->field_color('text_color', 'Text color', $settings); ?>
                    </section>
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-marker"></span><div><h3>Active arc</h3><p>Highlight the currently selected label.</p></div></div>
                        <?php $this->field_color('text_bg_color', 'Arc color', $settings); ?>
                        <div class="ckcs-fields three-col">
                            <?php $this->field_number('text_bg_opacity', 'Opacity', $settings, array('min'=>0,'max'=>1,'step'=>0.05)); ?>
                            <?php $this->field_number('bg_distance', 'Distance', $settings, array('min'=>0,'max'=>80,'suffix'=>'px')); ?>
                            <?php $this->field_number('bg_thickness', 'Thickness', $settings, array('min'=>6,'max'=>100,'suffix'=>'px')); ?>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ckcs-panel" data-panel="navigation">
                <section class="ckcs-card">
                    <div class="ckcs-card__head"><span class="dashicons dashicons-arrow-left-alt2"></span><div><h3>Navigation buttons</h3><p>Size, placement and appearance of previous / next controls.</p></div></div>
                    <div class="ckcs-fields four-col">
                        <?php $this->field_number('arrow_size', 'Icon size', $settings, array('min'=>12,'max'=>64,'suffix'=>'px')); ?>
                        <?php $this->field_number('arrow_button_size', 'Button size', $settings, array('min'=>28,'max'=>96,'suffix'=>'px')); ?>
                        <?php $this->field_number('arrow_offset', 'Edge offset', $settings, array('min'=>0,'max'=>240,'suffix'=>'px')); ?>
                        <?php $this->field_number('arrow_radius', 'Corner radius', $settings, array('min'=>0,'max'=>100,'suffix'=>'px')); ?>
                    </div>
                    <div class="ckcs-fields three-col">
                        <?php $this->field_color('arrow_color', 'Icon color', $settings); ?>
                        <?php $this->field_color('arrow_bg_color', 'Button background', $settings); ?>
                        <?php $this->field_color('arrow_border_color', 'Border color', $settings); ?>
                    </div>
                    <div class="ckcs-fields two-col">
                        <?php $this->field_number('arrow_bg_opacity', 'Background opacity', $settings, array('min'=>0,'max'=>1,'step'=>0.05)); ?>
                        <?php $this->field_number('arrow_border_width', 'Border width', $settings, array('min'=>0,'max'=>8,'suffix'=>'px')); ?>
                    </div>
                    <?php $this->field_switch('hide_arrows_mobile', 'Hide arrows on mobile', $settings, 'Useful when the slider is used as a purely visual hero.'); ?>
                </section>
            </div>

            <div class="ckcs-panel" data-panel="responsive">
                <div class="ckcs-card-grid">
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-smartphone"></span><div><h3>Circle size</h3><p>Independent center-circle diameter per breakpoint.</p></div></div>
                        <div class="ckcs-fields three-col">
                            <?php $this->field_number('circle_size', 'Desktop', $settings, array('min'=>220,'max'=>1000,'suffix'=>'px')); ?>
                            <?php $this->field_number('circle_size_tablet', 'Tablet', $settings, array('min'=>180,'max'=>800,'suffix'=>'px')); ?>
                            <?php $this->field_number('circle_size_mobile', 'Mobile', $settings, array('min'=>160,'max'=>600,'suffix'=>'px')); ?>
                        </div>
                    </section>
                    <section class="ckcs-card">
                        <div class="ckcs-card__head"><span class="dashicons dashicons-editor-expand"></span><div><h3>Section height</h3><p>Uses viewport height for reliable hero layouts.</p></div></div>
                        <div class="ckcs-fields three-col">
                            <?php $this->field_number('section_height', 'Desktop', $settings, array('min'=>30,'max'=>140,'suffix'=>'vh')); ?>
                            <?php $this->field_number('section_height_tablet', 'Tablet', $settings, array('min'=>30,'max'=>120,'suffix'=>'vh')); ?>
                            <?php $this->field_number('section_height_mobile', 'Mobile', $settings, array('min'=>30,'max'=>120,'suffix'=>'vh')); ?>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_slides_meta_box($post) {
        $slides = get_post_meta($post->ID, '_circular_slider_slides', true);
        if (!is_array($slides)) {
            $slides = array();
        }
        ?>
        <div class="ckcs-slides-editor">
            <div class="ckcs-slides-intro">
                <div><h3>Build your sequence</h3><p>Drag slides to reorder them. Each slide can use one center image and an optional separate background image.</p></div>
                <button type="button" class="button button-primary ckcs-add-slide"><span class="dashicons dashicons-plus-alt2"></span> Add slide</button>
            </div>
            <div id="ckcs-slides-container">
                <?php foreach ($slides as $index => $slide) :
                    $this->render_slide_item($index, $slide);
                endforeach; ?>
            </div>
            <template id="ckcs-slide-template">
                <?php $this->render_slide_item('__INDEX__', array()); ?>
            </template>
            <?php if (!$slides) : ?>
                <div class="ckcs-empty-state" id="ckcs-empty-state"><span class="dashicons dashicons-images-alt2"></span><strong>No slides yet</strong><p>Add your first image to start building the circular slider.</p></div>
            <?php endif; ?>
            <div class="ckcs-slides-toolbar">
                <div class="ckcs-slides-toolbar__meta">
                    <strong><span id="ckcs-slide-count"><?php echo esc_html(count($slides)); ?></span> slides</strong>
                    <span>Drag to reorder · Changes are saved with the slider</span>
                </div>
                <div class="ckcs-slides-toolbar__actions">
                    <button type="button" class="button ckcs-save-slider"><span class="dashicons dashicons-saved"></span> Save slider</button>
                    <button type="button" class="button button-primary ckcs-add-slide"><span class="dashicons dashicons-plus-alt2"></span> Add slide</button>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_slide_item($index, $slide) {
        $image_id = isset($slide['image_id']) ? absint($slide['image_id']) : 0;
        $bg_image_id = isset($slide['bg_image_id']) ? absint($slide['bg_image_id']) : 0;
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $bg_url = $bg_image_id ? wp_get_attachment_image_url($bg_image_id, 'medium') : '';
        ?>
        <article class="ckcs-slide-item" data-index="<?php echo esc_attr($index); ?>">
            <header class="ckcs-slide-item__header">
                <div class="ckcs-drag" title="Drag to reorder"><span class="dashicons dashicons-menu"></span><strong>Slide <span class="ckcs-slide-number"><?php echo is_numeric($index) ? esc_html(((int)$index) + 1) : ''; ?></span></strong></div>
                <div class="ckcs-slide-actions">
                    <button type="button" class="ckcs-icon-button ckcs-duplicate-slide" title="Duplicate slide"><span class="dashicons dashicons-admin-page"></span><span>Duplicate</span></button>
                    <button type="button" class="ckcs-icon-button ckcs-icon-button--danger ckcs-remove-slide" title="Remove slide"><span class="dashicons dashicons-trash"></span><span>Remove</span></button>
                </div>
            </header>
            <div class="ckcs-slide-layout">
                <label class="ckcs-field ckcs-slide-copy"><span class="ckcs-field__label">Circular label</span><input type="text" name="slides[<?php echo esc_attr($index); ?>][text]" value="<?php echo esc_attr($slide['text'] ?? ''); ?>" placeholder="e.g. SCHNIDER"><small>This text is distributed around the circle and is clickable.</small></label>
                <div class="ckcs-media-field">
                    <span class="ckcs-field__label">Center image</span>
                    <input type="hidden" class="ckcs-image-id" name="slides[<?php echo esc_attr($index); ?>][image_id]" value="<?php echo esc_attr($image_id); ?>">
                    <button type="button" class="ckcs-media-button ckcs-upload-image" data-target="center">
                        <span class="ckcs-media-preview<?php echo $image_url ? ' has-image' : ''; ?>"<?php echo $image_url ? ' style="background-image:url(' . esc_url($image_url) . ')"' : ''; ?>></span>
                        <span class="ckcs-media-text"><?php echo $image_url ? 'Replace image' : 'Choose image'; ?></span>
                    </button>
                </div>
                <div class="ckcs-media-field">
                    <span class="ckcs-field__label">Background image <em>optional</em></span>
                    <input type="hidden" class="ckcs-bg-image-id" name="slides[<?php echo esc_attr($index); ?>][bg_image_id]" value="<?php echo esc_attr($bg_image_id); ?>">
                    <button type="button" class="ckcs-media-button ckcs-upload-image" data-target="background">
                        <span class="ckcs-media-preview<?php echo $bg_url ? ' has-image' : ''; ?>"<?php echo $bg_url ? ' style="background-image:url(' . esc_url($bg_url) . ')"' : ''; ?>></span>
                        <span class="ckcs-media-text"><?php echo $bg_url ? 'Replace background' : 'Use center image'; ?></span>
                    </button>
                    <?php if ($bg_url) : ?><button type="button" class="button-link ckcs-clear-bg">Clear separate background</button><?php endif; ?>
                </div>
            </div>
        </article>
        <?php
    }

    public function save_meta_boxes($post_id) {
        if (!isset($_POST['circular_slider_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['circular_slider_nonce'])), 'circular_slider_meta_box')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $d = self::defaults();
        $hex = static function ($key, $fallback) {
            $value = isset($_POST[$key]) ? sanitize_hex_color(wp_unslash($_POST[$key])) : '';
            return $value ?: $fallback;
        };
        $num = static function ($key, $fallback, $min, $max) {
            $value = isset($_POST[$key]) ? (float) wp_unslash($_POST[$key]) : (float) $fallback;
            return max($min, min($max, $value));
        };

        $settings = array(
            'circle_size'            => (int) $num('circle_size', $d['circle_size'], 220, 1000),
            'circle_size_tablet'     => (int) $num('circle_size_tablet', $d['circle_size_tablet'], 180, 800),
            'circle_size_mobile'     => (int) $num('circle_size_mobile', $d['circle_size_mobile'], 160, 600),
            'section_height'         => (int) $num('section_height', $d['section_height'], 30, 140),
            'section_height_tablet'  => (int) $num('section_height_tablet', $d['section_height_tablet'], 30, 120),
            'section_height_mobile'  => (int) $num('section_height_mobile', $d['section_height_mobile'], 30, 120),
            'transition_speed'       => (int) $num('transition_speed', $d['transition_speed'], 100, 5000),
            'autoplay'               => isset($_POST['autoplay']),
            'autoplay_delay'         => (int) $num('autoplay_delay', $d['autoplay_delay'], 1000, 30000),
            'pause_on_hover'         => isset($_POST['pause_on_hover']),
            'bg_opacity'             => $num('bg_opacity', $d['bg_opacity'], 0, 1),
            'bg_blur'                => (int) $num('bg_blur', $d['bg_blur'], 0, 80),
            'bg_scale'               => $num('bg_scale', $d['bg_scale'], 1, 1.5),
            'image_fit'              => (isset($_POST['image_fit']) && 'contain' === $_POST['image_fit']) ? 'contain' : 'cover',
            'image_position_x'       => (int) $num('image_position_x', $d['image_position_x'], 0, 100),
            'image_position_y'       => (int) $num('image_position_y', $d['image_position_y'], 0, 100),
            'image_scale'            => $num('image_scale', $d['image_scale'], 1, 2),
            'circle_border_width'    => (int) $num('circle_border_width', $d['circle_border_width'], 0, 20),
            'circle_border_color'    => $hex('circle_border_color', $d['circle_border_color']),
            'circle_border_opacity'  => $num('circle_border_opacity', $d['circle_border_opacity'], 0, 1),
            'text_font'              => sanitize_text_field(wp_unslash($_POST['text_font'] ?? $d['text_font'])),
            'text_size'              => (int) $num('text_size', $d['text_size'], 9, 48),
            'text_weight'            => (int) $num('text_weight', $d['text_weight'], 100, 900),
            'text_letter_spacing'    => $num('text_letter_spacing', $d['text_letter_spacing'], -2, 10),
            'text_color'             => $hex('text_color', $d['text_color']),
            'text_bg_color'          => $hex('text_bg_color', $d['text_bg_color']),
            'text_bg_opacity'        => $num('text_bg_opacity', $d['text_bg_opacity'], 0, 1),
            'bg_distance'            => (int) $num('bg_distance', $d['bg_distance'], 0, 80),
            'bg_thickness'           => (int) $num('bg_thickness', $d['bg_thickness'], 6, 100),
            'arrow_color'            => $hex('arrow_color', $d['arrow_color']),
            'arrow_size'             => (int) $num('arrow_size', $d['arrow_size'], 12, 64),
            'arrow_button_size'      => (int) $num('arrow_button_size', $d['arrow_button_size'], 28, 96),
            'arrow_offset'           => (int) $num('arrow_offset', $d['arrow_offset'], 0, 240),
            'arrow_bg_color'         => $hex('arrow_bg_color', $d['arrow_bg_color']),
            'arrow_bg_opacity'       => $num('arrow_bg_opacity', $d['arrow_bg_opacity'], 0, 1),
            'arrow_border_color'     => $hex('arrow_border_color', $d['arrow_border_color']),
            'arrow_border_width'     => (int) $num('arrow_border_width', $d['arrow_border_width'], 0, 8),
            'arrow_radius'           => (int) $num('arrow_radius', $d['arrow_radius'], 0, 100),
            'hide_arrows_mobile'     => isset($_POST['hide_arrows_mobile']),
            'section_padding_top'    => (int) $num('section_padding_top', $d['section_padding_top'], 0, 300),
            'section_padding_bottom' => (int) $num('section_padding_bottom', $d['section_padding_bottom'], 0, 300),
        );
        update_post_meta($post_id, '_circular_slider_settings', $settings);

        $slides = array();
        if (isset($_POST['slides']) && is_array($_POST['slides'])) {
            foreach (wp_unslash($_POST['slides']) as $slide) {
                $image_id = absint($slide['image_id'] ?? 0);
                $text = sanitize_text_field($slide['text'] ?? '');
                if ($image_id && '' !== $text) {
                    $slides[] = array(
                        'image_id'    => $image_id,
                        'bg_image_id' => absint($slide['bg_image_id'] ?? 0),
                        'text'        => $text,
                    );
                }
            }
        }
        update_post_meta($post_id, '_circular_slider_slides', $slides);
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(array('id' => 0), $atts, 'circular_slider');
        $slider_id = absint($atts['id']);
        if (!$slider_id) {
            return current_user_can('edit_posts') ? '<p class="circular-slider-message">Please specify a slider ID.</p>' : '';
        }
        return $this->render_slider($slider_id);
    }

    public function render_slider($slider_id) {
        $settings = wp_parse_args((array) get_post_meta($slider_id, '_circular_slider_settings', true), self::defaults());
        $slides = get_post_meta($slider_id, '_circular_slider_slides', true);
        if (!is_array($slides) || !$slides) {
            return current_user_can('edit_posts') ? '<p class="circular-slider-message">No slides found.</p>' : '';
        }

        wp_enqueue_style('circular-slider-css');
        wp_enqueue_script('circular-slider-js');

        ob_start();
        include CIRCULAR_SLIDER_PATH . 'templates/slider-template.php';
        return ob_get_clean();
    }
}

new Circular_Slider();
