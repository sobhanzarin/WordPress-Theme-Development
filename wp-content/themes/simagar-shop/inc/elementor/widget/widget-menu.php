<?php 
class Simagar_Widget_Menu extends \Elementor\Widget_Base {
    public function get_name(){
        return 'menu';
    }
    public function get_title(){
        return 'منو اصلی';
    }
    public function get_icon(){
        return 'eicon-menu-bar';
    }
    public function get_categories(){
        return ['simagar-header-widget'] ;
    }
    protected function register_controls() {

        $this->start_controls_section(
            'menu_section',
        [
            'label' => 'تنظیمات منو اصلی ',
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
        );    
        $this->end_controls_section();
    }

	protected function render() {
        $setting = $this->get_settings_for_display();
        ?>
        <div class="simagar-navigation p-4">
            <div class="container align-center d-flex justify-content-center">
            <?php 
                wp_nav_menu(
                    [
                    'theme_location' => 'main-menu',
                    'walker' => new simagar_mega_menu_walker(),
                    ]
                )
            ?>
            </div>
        </div>
    <?php }
}