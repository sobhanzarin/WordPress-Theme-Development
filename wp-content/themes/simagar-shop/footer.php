    <?php 
    $logo = simagar_setting("logo-website");
    $account_link = get_permalink(get_option("woocommerce_myaccount_page_id"));
    add_filter('woocommerce_login_redirect', 'redirectLogin', 10, 2);
    function redirectLogin($redirect, $user ){
    $account_link = get_permalink(get_option('woocommerce_myaccount_page_id'));
    return $account_link;
    }
    $footer_ele = simagar_setting('footer-elementor');
    ?>
    <footer>
         <?php
        if($footer_ele) {
            $post = get_post($footer_ele);
            if(get_post_status($footer_ele) and get_post_type($footer_ele) === "simagarfooter"){
            setup_postdata($post);
            }
            the_content();
        } 

        wp_reset_postdata();
        ?>
    </footer>
    <div class="login-modal">
        <div class="body p-4">
            <i class="close-modal d-flex align-items-center justify-content-center p-3 fa-solid fa-xmark" id="close-modal"></i>
            <div>
                <img src="<?php echo esc_url($logo['url']) ?>" alt="<?php echo esc_attr(get_bloginfo('name')) ?>">
            </div>
            <p class="title-login-modal mt-3">ورود به سایت</p>
            <form action="<?php echo esc_url($account_link); ?>" class="mt-3" method="post">
                <div class="mt-3">
                    <input class="form-control" type="text" placeholder="نام کاربری یا ایمیل" name="username" >
                </div>
                <div class="mt-3">
                    <input class="form-control" type="password" placeholder="رمز عبور" name="password">
                </div>
                <div class="mt-3">
                    <a href="" class="d-flex lost-password">رمز عبور خود را فراموش کردید؟</a>
                </div>
                <div class="mt-3" >
                   <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                    <input class="btn-submit form-control" type="submit" value="ورود به سایت" name="login"></button>
                </div>
                <div class="mt-3" >
                    <a href="<?php echo esc_url($account_link); ?>" class="signup">ثبت نام در سایت</a>
                </div>
            </form>
        </div>  
    </div>
    <?php wp_footer(); ?>
</body>
</html>