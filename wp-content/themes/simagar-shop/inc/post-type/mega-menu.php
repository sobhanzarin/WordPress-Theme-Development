<?php
function simagar_megamenu_post_type() {
	register_post_type('simagarmegamenu',
		array(
			'labels'      => array(
				'name'          => 'مگامنو',
				'singular_name' => 'آیتم مگامنو',
                'add_new_item' => 'افزودن مگامنو'
			),
				'public'      => true,
				'has_archive' => true,
                'rewrite' => array('slug' => 'simagarmegamenu')
		)
	);
}
add_action('init', 'simagar_megamenu_post_type');