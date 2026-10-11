<?php

if (!defined('ABSPATH')) {
    exit;
}

class Circular_Slider_Elementor_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'codifykit_circular_slider';
    }

    public function get_title() {
        return __('CircleFlow Slider', 'circular-slider');
    }

    public function get_icon() {
        return 'eicon-slider-album';
    }

    public function get_categories() {
        return array('general');
    }

    public function get_keywords() {
        return array('slider', 'circular', 'circle', 'gallery', 'codifykit');
    }

    protected function register_controls() {
        $this->start_controls_section('section_slider_select', array(
            'label' => __('CircleFlow Slider', 'circular-slider'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ));

        $sliders = get_posts(array(
            'post_type'      => 'circular_slider',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));

        $options = array('' => __('Select a slider', 'circular-slider'));
        foreach ($sliders as $slider) {
            $options[$slider->ID] = $slider->post_title;
        }

        $this->add_control('slider_id', array(
            'label'   => __('Slider', 'circular-slider'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => $options,
            'default' => '',
        ));

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $slider_id = absint($settings['slider_id'] ?? 0);

        if (!$slider_id) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:24px;border:1px dashed #cbd5e1;border-radius:12px;text-align:center;color:#64748b;background:#f8fafc">Select a CircleFlow Slider from the widget settings.</div>';
            }
            return;
        }

        $slider = new Circular_Slider();
        echo $slider->render_slider($slider_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
