<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class Simagar_Widget_Footer_Menu extends \Elementor\Widget_Base {
    public function get_name(){
        return 'footer-menu';
    }
    public function get_title(){
        return 'منوی فوتر';
    }
    public function get_icon(){
        return 'eicon-editor-list-ul';
    }
    public function get_categories(){
        return ['simagar-footer-widget'] ;
    }
    protected function register_controls() {

        $this->start_controls_section(
            'footer_menu_section',
        [
            'label' => 'تنظیمات  منوی فوتر',
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
        );
        $repeater = new Repeater();
        $repeater->add_control(
            'title',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "عنوان",
            ]
            );
         $repeater->add_control(
            'url',
            [
                'type' => \Elementor\Controls_Manager::URL,
                'label' => "لینک",
            ]
            );
            $this->add_control(
                'links',
                [
                    'type' => \Elementor\Controls_Manager::REPEATER,
                    'label' => 'آیتم های منو',
                    'fields' => $repeater->get_controls()
                ]
                );

        $this->end_controls_section();

        $this->start_controls_section(
            'footer-style-menu',
            [
                'label' => 'استایل آیتم منو',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label' => 'رنگ متن',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-menu .footer-menu-item li a' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'footer-menu-spacing',
            [
                'label' => 'فاصله بین منو',
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .simagar-footer-menu .footer-menu-item li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
            );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => 'تایپوگرافی',
                'selector' => '{{WRAPPER}} .simagar-footer-menu .footer-menu-item li a',
            ]
            );
            $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display(); ?>

        <div class="simagar-footer-menu">
            <ul class="footer-menu-item">
                <?php foreach ($settings['links'] as $item ) : ?>
                    <li><a href="<?php echo esc_url($item['url']['url']) ?>"><?php echo $item['title'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>




    <?php
    }

}