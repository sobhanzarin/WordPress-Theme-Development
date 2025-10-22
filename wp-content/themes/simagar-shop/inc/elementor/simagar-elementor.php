<?php

function simagr_add_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'simagar-widgets',
		[
			'title' => 'المان اصلی سیماگرشاپ',
			'icon' => 'fa fa-plug',
		]
	);
	$elements_manager->add_category(
		'simagar-header-widget',
		[
			'title' => 'المان هدر سیماگرشاپ',
			'icon' => 'fa fa-plug',
		]
	);
    $elements_manager->add_category(
		'simagar-footer-widget',
		[
			'title' => 'المان هدر سیماگرشاپ',
			'icon' => 'fa fa-plug',
		]
	);


}
add_action( 'elementor/elements/categories_registered', 'simagr_add_elementor_widget_categories' );

function simagar_register_new_widgets( $widgets_manager ) {

	require_once( SIMAGAR_THEME_DIR . 'inc/elementor/widget/widget-auth-btn.php' );
	require_once( SIMAGAR_THEME_DIR . 'inc/elementor/widget/widget-cart-btn.php' );
	require_once( SIMAGAR_THEME_DIR . 'inc/elementor/widget/widget-search.php' );
	require_once( SIMAGAR_THEME_DIR . 'inc/elementor/widget/widget-menu.php' );

	$widgets_manager->register( new Simagar_Widget_Auth_Btn());
	$widgets_manager->register( new Simagar_Widget_Cart_Btn());
	$widgets_manager->register( new Simagar_Widget_Search());
	$widgets_manager->register( new Simagar_Widget_Menu());

}
add_action( 'elementor/widgets/register', 'simagar_register_new_widgets');