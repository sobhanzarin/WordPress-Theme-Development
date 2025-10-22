<?php
add_action('after_setup_theme', 'simagar_after_setup_theme');
function simagar_after_setup_theme(){
    add_theme_support('title-tag');

    register_nav_menus(
        array(
            'main-menu' => 'منو اصلی',
            'phone-menu' => 'منو موبایل'
        )
    );
} 