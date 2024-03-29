<?php

add_action( 'init', 'na_register_tax' );
function na_register_tax() {
	register_taxonomy(
		'attractiontypes',
		'attractions',
		array(
			'label' 					=> __( 'Attraction Types' ),
			'rewrite' 					=> array( 'slug' => 'attractiontypes' ),
			'hierarchical' 				=> true,
			'show_admin_column' 		=> true,
			'public'                    => true,
			'show_ui'                   => true,
			'show_admin_column'         => true,
		)
	);
}