<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = wp_parse_args($settings, Circular_Slider::defaults());
$unique_id = 'circular-slider-' . wp_unique_id();

$first_bg = '';
if (!empty($slides[0]['bg_image_id'])) {
    $first_bg = wp_get_attachment_image_url(absint($slides[0]['bg_image_id']), 'full');
}
if (!$first_bg && !empty($slides[0]['image_id'])) {
    $first_bg = wp_get_attachment_image_url(absint($slides[0]['image_id']), 'full');
}

$style_vars = array(
    '--ckcs-circle-desktop:' . (int) $settings['circle_size'] . 'px',
    '--ckcs-circle-tablet:' . (int) $settings['circle_size_tablet'] . 'px',
    '--ckcs-circle-mobile:' . (int) $settings['circle_size_mobile'] . 'px',
    '--ckcs-height-desktop:' . (int) $settings['section_height'] . 'vh',
    '--ckcs-height-tablet:' . (int) $settings['section_height_tablet'] . 'vh',
    '--ckcs-height-mobile:' . (int) $settings['section_height_mobile'] . 'vh',
    '--ckcs-transition:' . (int) $settings['transition_speed'] . 'ms',
    '--ckcs-bg-opacity:' . (float) $settings['bg_opacity'],
    '--ckcs-bg-blur:' . (int) $settings['bg_blur'] . 'px',
    '--ckcs-bg-scale:' . (float) $settings['bg_scale'],
    '--ckcs-image-x:' . (int) $settings['image_position_x'] . '%',
    '--ckcs-image-y:' . (int) $settings['image_position_y'] . '%',
    '--ckcs-image-scale:' . (float) $settings['image_scale'],
    '--ckcs-circle-border-width:' . (int) $settings['circle_border_width'] . 'px',
    '--ckcs-circle-border-color:' . esc_attr($settings['circle_border_color']),
    '--ckcs-circle-border-opacity:' . (float) $settings['circle_border_opacity'],
    '--ckcs-text-size:' . (int) $settings['text_size'] . 'px',
    '--ckcs-text-weight:' . (int) $settings['text_weight'],
    '--ckcs-text-letter-spacing:' . (float) $settings['text_letter_spacing'] . 'px',
    '--ckcs-arrow-size:' . (int) $settings['arrow_size'] . 'px',
    '--ckcs-arrow-button-size:' . (int) $settings['arrow_button_size'] . 'px',
    '--ckcs-arrow-offset:' . (int) $settings['arrow_offset'] . 'px',
    '--ckcs-arrow-bg:' . esc_attr($settings['arrow_bg_color']),
    '--ckcs-arrow-bg-opacity:' . (float) $settings['arrow_bg_opacity'],
    '--ckcs-arrow-border:' . esc_attr($settings['arrow_border_color']),
    '--ckcs-arrow-border-width:' . (int) $settings['arrow_border_width'] . 'px',
    '--ckcs-arrow-radius:' . (int) $settings['arrow_radius'] . 'px',
    '--ckcs-pad-top:' . (int) $settings['section_padding_top'] . 'px',
    '--ckcs-pad-bottom:' . (int) $settings['section_padding_bottom'] . 'px',
);
?>
<div
    id="<?php echo esc_attr($unique_id); ?>"
    class="circular-slider-wrapper<?php echo !empty($settings['hide_arrows_mobile']) ? ' ckcs-hide-arrows-mobile' : ''; ?>"
    data-circle-size="<?php echo esc_attr($settings['circle_size']); ?>"
    data-circle-size-tablet="<?php echo esc_attr($settings['circle_size_tablet']); ?>"
    data-circle-size-mobile="<?php echo esc_attr($settings['circle_size_mobile']); ?>"
    data-transition-speed="<?php echo esc_attr($settings['transition_speed']); ?>"
    data-autoplay="<?php echo !empty($settings['autoplay']) ? 'true' : 'false'; ?>"
    data-autoplay-delay="<?php echo esc_attr($settings['autoplay_delay']); ?>"
    data-pause-on-hover="<?php echo !empty($settings['pause_on_hover']) ? 'true' : 'false'; ?>"
    data-text-font="<?php echo esc_attr($settings['text_font']); ?>"
    data-text-size="<?php echo esc_attr($settings['text_size']); ?>"
    data-text-weight="<?php echo esc_attr($settings['text_weight']); ?>"
    data-text-letter-spacing="<?php echo esc_attr($settings['text_letter_spacing']); ?>"
    data-text-color="<?php echo esc_attr($settings['text_color']); ?>"
    data-text-bg-color="<?php echo esc_attr($settings['text_bg_color']); ?>"
    data-text-bg-opacity="<?php echo esc_attr($settings['text_bg_opacity']); ?>"
    data-bg-distance="<?php echo esc_attr($settings['bg_distance']); ?>"
    data-bg-thickness="<?php echo esc_attr($settings['bg_thickness']); ?>"
    style="<?php echo esc_attr(implode(';', $style_vars)); ?>"
>
    <div class="slider-background" aria-hidden="true"<?php echo $first_bg ? ' style="background-image:url(' . esc_url($first_bg) . ')"' : ''; ?>>
        <?php foreach ($slides as $index => $slide) :
            $bg_id = !empty($slide['bg_image_id']) ? absint($slide['bg_image_id']) : absint($slide['image_id'] ?? 0);
            $bg_image = $bg_id ? wp_get_attachment_image_url($bg_id, 'full') : '';
            ?>
            <div class="bg-layer<?php echo 0 === $index ? ' active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>"<?php echo $bg_image ? ' style="background-image:url(' . esc_url($bg_image) . ')"' : ''; ?>></div>
        <?php endforeach; ?>
    </div>

    <div class="circular-slider-stage">
        <div class="circular-slider-circle">
            <div class="circular-slider-slides">
                <?php foreach ($slides as $index => $slide) :
                    $image_id = absint($slide['image_id'] ?? 0);
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
                    if (!$image_url) {
                        continue;
                    }
                    $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    if ('' === trim((string) $alt)) {
                        $alt = $slide['text'] ?? '';
                    }
                    ?>
                    <div class="circular-slide<?php echo 0 === $index ? ' active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="circular-slider-texts" aria-hidden="true">
            <div class="texts-rotating-container">
                <?php foreach ($slides as $index => $slide) : ?>
                    <span class="circular-text-word" data-slide-index="<?php echo esc_attr($index); ?>"><?php echo esc_html($slide['text'] ?? ''); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <button class="circular-slider-nav prev" type="button" aria-label="<?php esc_attr_e('Previous slide', 'circular-slider'); ?>">
        <span class="ckcs-nav-bg"></span>
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9.57 5.93 3.5 12l6.07 6.07M20.5 12H3.67" stroke="<?php echo esc_attr($settings['arrow_color']); ?>" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button class="circular-slider-nav next" type="button" aria-label="<?php esc_attr_e('Next slide', 'circular-slider'); ?>">
        <span class="ckcs-nav-bg"></span>
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14.43 5.93 6.07 6.07-6.07 6.07M3.5 12h16.83" stroke="<?php echo esc_attr($settings['arrow_color']); ?>" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
</div>
<style>
#<?php echo esc_attr($unique_id); ?> .circular-slide img{object-fit:<?php echo esc_attr($settings['image_fit']); ?>;}
#<?php echo esc_attr($unique_id); ?> .circular-letter{color:<?php echo esc_attr($settings['text_color']); ?>;font-family:<?php echo esc_attr($settings['text_font']); ?>;}
</style>
