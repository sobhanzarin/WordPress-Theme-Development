<?php 

$header_post = get_posts([
  'post_type' => 'simagarheader',
  'post_status' => 'publish',
  'numberposts'=> -1
]);
$headers_elementor = array();
foreach ($header_post as $post) {
  $headers_elementor[$post->ID] = $post->post_title;
}

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'simagar_setting';

  //
  // Create options
  CSF::createOptions( $prefix, array(
    'menu_title' => 'تنظیمات سیماگر شاپ',
    'menu_slug'  => 'simagar_shop_setting',
    'menu_hidden' => false,
    'framework_title' => 'سیماگر شاپ'
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'title'  => 'هدر',
    'fields' => array(

      array(
      'id'          => 'header-type',
      'type'        => 'select',
      'title'       => 'نوع هدر',
      'placeholder' => 'نوع هدر را انتخاب کنید',
      'options'     => array(
        'default'  => 'پیش فرض',
        'elementor'  => 'المنتور',
      ),
      'default'     => 'default'
    ),

      array(
      'id'          => 'header-elementor',
      'type'        => 'select',
      'title'       => 'انتخاب هدر',
      'placeholder' => 'هدر خود را انتخاب کنید',
      'options'     => $headers_elementor,
      'dependency' => array('header-type', '==', 'elementor')
    ),

    array(
    'id'    => 'logo-website',
    'type'  => 'media',
    'title' => 'انتخاب لوگو',
    'dependency' => array('header-type', '==', 'default')
    ),
    array(
      'id'      => 'logo-width',
      'type'    => 'text',
      'title'   => 'عرض لوگو رو به PX وارد نمایید.',
      'default' => '130',
      'dependency' => array('header-type', '==', 'default')
    ),
    
     array(
      'id'          => 'auth-btn-type',
      'type'        => 'select',
      'title'       => 'نوع دکمه حساب کاربری',
      'placeholder' => 'نوع دکمه را انتخاب کنید',
      'options'     => array(
        'modal'  => 'مدال بازشونده',
        'link'  => 'لینک سفارشی',
      ),
      'default'     => 'modal',
      'dependency' => array('header-type', '==', 'default')
    ),
    array(
      'id'    => 'auth-btn-text',
      'type'  => 'text',
      'title' => 'متن دکمه',
      'dependency' => array('auth-btn-type', '==', 'link')
    ),
    array(
      'id'    => 'auth-btn-link',
      'type'  => 'text',
      'title' => 'لینک دکمه',
      'dependency' => array('auth-btn-type', '==', 'link')
    ),
    array(
      'id'    => 'phone-number-header',
      'type'  => 'text',
      'title' => 'شماره تماس',
      'dependency' => array('header-type', '==', 'default')
    ),
  ),
  ));
  CSF::createSection( $prefix, array(
    'title'  => 'استایل',
    'fields' => array(

      array(
      'id'          => 'font-family',
      'type'        => 'select',
      'title'       => 'انتخاب فونت',
      'placeholder' => 'فونت را انتخاب کنید',
      'options'     => array(
        'iransans'  => 'iransans',
        'dana'  => 'dana',
      ),
      'default'     => 'iransans'
    ),
    array(
    'id'    => 'main-coloer-group',
    'type'  => 'color',
    'title' => 'رنگ اصلی سایت',
    'default' => '#008ECC',
    'output' => array(
      'color' => '.icon-header, .phone-header',
      'background-color' => '.auth-holder',
    )
    ),
      ),
  ) );
} 

function simagar_setting($key = "")
{
  $options = get_option('simagar_setting');
  return isset($options[$key]) ? $options[$key] : null;
}