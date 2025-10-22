<?php 
add_action('wp_enqueue_scripts', 'simagar_enqueue_scripts');
function simagar_enqueue_scripts(){

    $theme_obj = wp_get_theme();
    $theme_version = $theme_obj->get('Version');

     // style 
    wp_enqueue_style('simagar-bootstrap', SIMAGAR_THEME_URL . "assets/css/bootstrap.min.css");
    wp_enqueue_style('simagar-fontawwsome', SIMAGAR_THEME_URL . "assets/css/fontawesome.css");
    wp_enqueue_style('simagar-fontawwsome-light', SIMAGAR_THEME_URL . "assets/css/light.css");
    wp_enqueue_style('simagar-main-style', SIMAGAR_THEME_URL . "assets/css/main.css");
    wp_enqueue_style('simagar-navigation-style', SIMAGAR_THEME_URL . "assets/css/navigation.css");
    wp_enqueue_style('simagar-style', get_stylesheet());

    $font_family = simagar_setting('font-family');
    wp_enqueue_style('simagar-font-family', SIMAGAR_THEME_URL . "assets/fonts/" . $font_family . ".css", null, $theme_version);

    // js
    wp_enqueue_script('simagar-jquery', SIMAGAR_THEME_URL . "assets/js/jquery-3.7.1.min.js", null, true);
    wp_enqueue_script('simagar-popper', SIMAGAR_THEME_URL . "assets/js/popper.min.js", null, $theme_version, true);
    wp_enqueue_script('simagar-bootstrap', SIMAGAR_THEME_URL . "assets/js/bootstrap.min.js", null, $theme_version, true);
    wp_enqueue_script('simagar-app', SIMAGAR_THEME_URL . "assets/js/app.js", null, $theme_version, true);
}