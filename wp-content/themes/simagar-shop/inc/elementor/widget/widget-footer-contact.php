<?php

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Simagar_Widget_Footer_Contact extends \Elementor\Widget_Base {

    public function get_name(){
        return 'footer-contact';
    }

    public function get_title(){
        return 'منوی اطلاعات تماس';
    }

    public function get_icon(){
        return 'eicon-contact';
    }

    public function get_categories(){
        return ['simagar-footer-widget'];
    }

    protected function register_controls() {

        /**
         * ---------------------
         * تب Content
         * ---------------------
         */
        $this->start_controls_section(
            'footer_contact_section',
            [
                'label' => 'تنظیمات اطلاعات تماس',
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => 'عنوان آیتم',
                'type' => Controls_Manager::TEXT,
                'default' => 'عنوان آیتم',
            ]
        );

        $repeater->add_control(
            'value',
            [
                'label' => 'مقدار (متن اصلی یا شماره)',
                'type' => Controls_Manager::TEXT,
                'default' => 'متن آیتم',
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => 'لینک آیتم',
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://example.com یا mailto:info@site.com',
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );

        $this->add_control(
            'contact_items',
            [
                'label' => 'آیتم‌های تماس',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => 'آدرس',
                        'value' => 'تهران، خیابان مثال...',
                    ],
                    [
                        'title' => 'شماره تماس',
                        'value' => '021-12345678',
                    ],
                    [
                        'title' => 'ایمیل',
                        'value' => 'info@example.com',
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();

        /**
         * ---------------------
         * تب Style
         * ---------------------
         */
        $this->start_controls_section(
            'footer_contact_style_section',
            [
                'label' => 'استایل آیتم‌ها',
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // رنگ متن مقدار
        $this->add_control(
            'contact_text_color',
            [
                'label' => 'رنگ متن مقدار',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-contact .contact-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        // رنگ متن عنوان
        $this->add_control(
            'contact_title_color',
            [
                'label' => 'رنگ عنوان',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-contact .contact-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        // رنگ پس‌زمینه باکس
        $this->add_control(
            'contact_bg_color',
            [
                'label' => 'رنگ پس‌زمینه باکس',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-contact .contact-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // فاصله بین آیتم‌ها
        $this->add_responsive_control(
            'contact_item_spacing',
            [
                'label' => 'فاصله بین آیتم‌ها',
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-contact .contact-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // تایپوگرافی عنوان
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'contact_title_typography',
                'label' => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .simagar-footer-contact .contact-title',
            ]
        );

        // تایپوگرافی مقدار
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'contact_value_typography',
                'label' => 'تایپوگرافی مقدار',
                'selector' => '{{WRAPPER}} .simagar-footer-contact .contact-value',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['contact_items'] ) ) {
            return;
        }

        echo '<div class="simagar-footer-contact">';
        foreach ( $settings['contact_items'] as $item ) {

            $link_open = $link_close = '';

            if ( ! empty( $item['link']['url'] ) ) {
                $this->add_link_attributes( 'item_link_' . $this->get_id_int() . '_' . $item['_id'], $item['link'] );
                $link_open  = '<a ' . $this->get_render_attribute_string( 'item_link_' . $this->get_id_int() . '_' . $item['_id'] ) . '>';
                $link_close = '</a>';
            }

            echo '<div class="contact-item">';
                echo $link_open;
                    echo '<div class="contact-box">';
                        echo '<span class="contact-title">'.esc_html( $item['title'] ).'</span>';
                        echo '<span class="contact-value">'.esc_html( $item['value'] ).'</span>';
                    echo '</div>';
                echo $link_close;
            echo '</div>';
        }
        echo '</div>';
    }
}
