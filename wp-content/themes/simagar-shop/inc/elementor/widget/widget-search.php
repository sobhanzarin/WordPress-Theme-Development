<?php 
class Simagar_Widget_Search extends \Elementor\Widget_Base {
    public function get_name(){
        return 'search-box';
    }
    public function get_title(){
        return 'جستجو';
    }
    public function get_icon(){
        return 'eicon-search-bold';
    }
    public function get_categories(){
        return ['simagar-header-widget'] ;
    }
    protected function register_controls() {

        $this->start_controls_section(
            'search_section',
        [
            'label' => 'تنظیمات باکس جستجو',
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
        );
        $this->add_control(
            'input-bg',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => "پس از زمینه"
            ]
            );
        $this->add_control(
            'border',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "گوشه های گرد به پیکسل",
                'placeholder' => 'به پیکسل وراد شود'
            ]
            );
        $this->add_control(
            'placeholder',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "متن باکس جستجو",
                'default' => "دنبال چه می گردید؟"
            ]
            );
      $this->add_control(
            'height',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "ارتفاع",
            ]
            );  
    $this->add_control(
            'width',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "عرض",
            ]
            );
    $this->add_control(
            'paddong',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => "پدینگ دکمه به پیکسل",
            ]
            );
        $this->add_control(
            'icon-color',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => "رنگ آیکون"
            ]
            );
       $this->add_control(
            'icon-size',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => "اندازه آیکون"
            ]
            );
     $this->add_control(
            'icon-bg',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => "رنگ پس زمینه آیکون"
            ]
            );
    
        $this->end_controls_section();
    }

	protected function render() {
        $setting = $this->get_settings_for_display();
        ?>
               <div class="search-holder">
                        <form action="" id="form-search" style="width: <?php echo $setting['width']?>px !important;">
                            <input class="form-control header-search-box" type="text" placeholder="<?php echo esc_html($setting['placeholder']) ?>" style="padding: <?php echo $setting['paddong'] ?>px ;height: <?php echo $setting['height']?>px ; border-radius: <?php echo $setting['border'] ?> ;background: <?php echo $setting['input-bg'] ?>" >
                            <button class="btn-search-header" type="submit" style="background: <?php echo $setting['icon-bg'] ?> ;" ><i class="icon-header fa-solid fa-magnifying-glass" style="color: <?php echo $setting['icon-color'] ?> ; font-size: <?php echo $setting['icon-size'] ?>" ></i></button>
                        </form>
                </div> 
    <?php }
}