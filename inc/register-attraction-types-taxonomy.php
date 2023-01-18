<?php

add_action( 'init', 'na_register_tax' );
function na_register_tax() {
	register_taxonomy(
		'attractiontype',
		'attractions',
		array(
			'label' => __( 'Attraction types' ),
			'rewrite' => array( 'slug' => 'attractiontype' ),
			'hierarchical' => true,
		)
	);
}