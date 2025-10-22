<?php 
    $header_type = simagar_setting('header-type');
    $logo = simagar_setting('logo-website');
    $logo_width = simagar_setting('logo-width');
    $auth_btn_type = simagar_setting('auth-btn-type');
    $auth_btn_text = simagar_setting('auth-btn-text');
    $auth_btn_link = simagar_setting('auth-btn-link');
    $account_link = get_permalink(get_option("woocommerce_myaccount_page_id"));
    $phone_header = simagar_setting('phone-number-header');
    $header_ele = simagar_setting('header-elementor');
?>
<!DOCTYPE html>
<html <?php language_attributes() ?> > 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<?php echo get_template_part("templates/phone-nav") ?>
<body <?php body_class(); ?> >
    <?php if($header_type === 'elementor') : ?> 
    <div>
        <?php
        if($header_ele) {
            $post = get_post($header_ele);
            if(get_post_status($header_ele) and get_post_type($header_ele) === "simagarheader"){
            setup_postdata($post);
            }
            the_content();
        } 

        wp_reset_postdata();
        ?>
    </div>
    <?php else: ?>
        
        <header class="site=header">

        <div class="header-main">
            <div class="container d-flex align-items-center justify-content-between p-3">
              <div class="d-flex align-items-center" >
                  <a class="ms-4 logo-header" href="<?php echo esc_url(home_url())?>">
                   <img width="<?php echo esc_attr($logo_width)?>px" src="<?php echo esc_url($logo['url']) ?>" alt="<?php echo esc_attr(get_bloginfo('name')) ?>">
                </a>
                 <div class="search-holder">
                        <form action="" id="form-search">
                            <input class="form-control header-search-box" type="text" placeholder="دنبال چی میگردید؟">
                            <button class="btn-search-header" type="submit"><i class="icon-header fa-solid fa-magnifying-glass"></i></button>
                        </form>
                </div> 

              </div>
                <div class="d-flex content-item align-items-center ms-4 content-header">
                    <span>سفارش خود را پیگیری کنید</span>
                    <div class="me-3" >
                        <span class="phone-header" ><?php echo esc_html($phone_header); ?></span>
                        <i class="icon-header fa-solid fa-headset"></i>
                    </div>
                </div>
                <div class="menu-mobile">
                        <span class="phone-nav-toggle me-3" ><i class="fa-sharp fa-solid fa-bars"></i></span>
                    </div>
            </div> 

        </div>
        <div class="simagar-navigation p-4">
            <div class="container d-flex justify-content-between">
                <div class="align-center">
                <?php 
                    wp_nav_menu(
                        [
                        'theme_location' => 'main-menu',
                        'walker' => new simagar_mega_menu_walker(),
                        ]
                    )
                ?>
                </div>
            <div>
                    <div class="d-flex align-items-center justify-content-center"> 
                          <div class="d-flex align-items-center">
                            <?php if($auth_btn_type == 'link'): ?>
                                  <!-- check login user  -->
                                <?php if(is_user_logged_in()): ?>
                                    <a class="auth-holder" href="<?php echo esc_url($account_link);?>">
                                        <i class="icon-header fa-regular fa-user"></i>
                                       حساب کاربری
                                    </a>
                                <?php else: ?>
                                    <a class="auth-holder" href="<?php echo esc_attr($auth_btn_link); ?>">
                                        <i class="icon-header fa-regular fa-user"></i>
                                        <?php echo esc_html($auth_btn_text); ?>
                                    </a>
                                <?php endif; ?>
                           
                            <?php else: ?>
                                <!-- check login user  -->
                                <?php if(is_user_logged_in()): ?>
                                    <a class="auth-holder" href="<?php echo esc_url($account_link); ?>">
                                        <i class="icon-header fa-regular fa-user"></i>
                                       حساب کاربری
                                    </a>
                                <?php else: ?>
                                  <a class="auth-holder" id="btn-auth" href="">
                                    <i class="icon-header fa-regular fa-user"></i>
                                    ورود | ثبت نام
                                </a>
                                <?php endif; ?>
                            
                            <?php endif; ?>
                            <a class="me-2 cart-holder" href="">
                                <i class="icon-header fa-regular fa-cart-shopping"></i>
                            </a>
                          </div>
                    </div>
            </div>
        </div>

        </div>
    </header>
    <?php endif;?>

    </div>