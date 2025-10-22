<?php

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'wss_simagar';
  
  CSF::createOptions( $prefix, array(
    'menu_title' => ' تنظیمات پنل سیماگر',
    'menu_slug'  => 'wss_setting_simagar',
    'menu_hidden' => false,
    'framework_title' => 'پنل پیامک سیماگر'
  ) );

  CSF::createSection( $prefix, array(
    'title'  => 'تنظیمات سامانه',
    'fields' => array(

    array(
    'id'          => 'phone-keys',
    'type'        => 'select',
    'title'       => 'کلید شماره موبایل',
    'placeholder' => 'یک کلید انتخاب کنید',
    'options'     => 'wss_usermeta_keys'
    ),

    array(
      'id'          => 'setting-sms-portal',
      'type'        => 'select',
      'title'       => 'سامانه های پیامک',
      'placeholder' => 'یک سامانه رو انتخاب کنید',
      'options'     => array(
        'melipayamak'  => 'melipayamak',
        'SMSir'  => 'SMSir',
      )
    ),

    array(
    'id'      => 'sms-username',
    'type'    => 'text',
    'title'   => 'نام کاربری سامانه'),
    array(
    'id'      => 'sms-password',
    'type'    => 'text',
    'title'   => 'رمز عبور سامانه') 
    )
  ) );

  CSF::createSection( $prefix, array(
    'title'  => 'ارسال پیام',
    'fields' => array(
    
  array(
    'id'    => 'active-sms-order',
    'type'  => 'switcher',
    'title' => 'فعالسازی'),    
  array(
    'id'    => 'user-sms-parent-code',
    'type'  => 'text',
    'title' => 'کد الگو'),

     array(
    'id'    => 'user-sms-parent',
    'type'  => 'textarea',
    'title' => 'الگو'),

    array(
    'type'  => 'content',
    'content' => '<p>شناسه اشتراک: {{subscription_id}}</p>'.
    '<p>ایدی محصول: {{item_id}}</p>'. 
    '<p>سفارشات: {{item_product}}</p>'. 
    '<p>نام کاربر : {{name}}</p>'.
    '<p>وضعیت : {{status}}</p>'.
    '<p>قیمت : {{price}}</p>'),
  )
  ));  

  CSF::createSection( $prefix, array(
    'title'  => 'ارسال پیام اشتراک',
    'fields' => array(
    
  array(
    'id'    => 'active-sms-sub',
    'type'  => 'switcher',
    'title' => 'فعالسازی'),    
  array(
    'id'    => 'user-sms-parent-code-sub',
    'type'  => 'text',
    'title' => 'کد الگو اشتراک'),

     array(
    'id'    => 'user-sms-parent-sub',
    'type'  => 'textarea',
    'title' => 'الگو اشتراک'),

    array(
    'type'  => 'content',
    'content' => '<p>سفارشات: {{item_product}}</p>'. 
    '<p>تاریخ شروع اشتراک : {{start_date}}</p>'.
    
    '<p>تاریخ پایان اشتراک : {{end_date}}</p>'.
    '<p>وضعیت اشتراک : {{subscription_status}}</p>'.
    '<p>دوره‌ی پرداخت : {{billing_period}}</p>'.
    '<p> فاصله‌ی پرداخت: {{billing_interval}}</p>'.
    '<p>تاریخ پرداخت بعدی : {{next_payment_date}}</p>'),
  )
  ));  
  
}

function wss_simagar($key = "")
{
  $options = get_option('wss_simagar');
  return isset($options[$key]) ? $options[$key] : null;
}