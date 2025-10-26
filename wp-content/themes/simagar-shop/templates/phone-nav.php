<?php
    $menu = wp_nav_menu([
        'theme_location' => 'phone-menu',
        'echo' => false,
    ]);
    $logo = simagar_setting('logo-website');
    $logo_width_mobile = simagar_setting('logo-width-mobile');
    
?>
<div class="phone-menu"> 
    <!-- <span class="close-menu-mobile" ><i class="fa-regular fa-xmark"></i></span> -->
    <div class="search-mobile mt-2">
        <form action="" id="form-search-mobile">
            <button class="btn-search-header" type="submit"><i class="icon-header fa-solid fa-magnifying-glass"></i></button>
            <input class="form-control header-search-box" type="text" placeholder="دنبال چی میگردید؟">
        </form>
    </div>
    <hr style="height:1px;border-width:0;color:#0000005c;background-color:#0000005c">
    <div class="body-menu d-flex justify-content-between flex-column mt-4">
        <?php echo $menu ?>
        <div class="account-menu">
            <hr style="height:1px;border-width:0;color:#0000005c;background-color:#0000005c">
            <i class="fa-regular fa-user"></i>
            <span>حساب کاربری</span>
        </div>
    </div>
</div>

<div class="mobile-navigation">
    <div class="box-navigation d-flex justify-content-between align-items-center">
        <div class="mobile-bottom-navitem">
            <a class="d-flex flex-column align-items-center gap-2" href="">
                <i class="fa-regular fa-shop"></i>
                <span>فروشگاه</span>
            </a>
        </div>
                <div class="mobile-bottom-navitem">
            <a class="d-flex flex-column align-items-center gap-2" href="">
                <i class="fa-regular fa-shop"></i>
                <span>فروشگاه</span>
            </a>
        </div>
                <div class="mobile-bottom-navitem">
            <a class="d-flex flex-column align-items-center gap-2" href="">
                <i class="fa-regular fa-shop"></i>
                <span>فروشگاه</span>
            </a>
        </div>
    </div>

</div>
<div class="phone-overlay"></div>