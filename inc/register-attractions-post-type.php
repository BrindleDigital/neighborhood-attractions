<?php

/**
 * Register the content stypes
 */
add_action( 'init', 'na_register_cpt' );
function na_register_cpt() {

	//* NAME
	$name_plural = 'Attractions';
	$name_singular = 'Attraction';
	$post_type = 'attractions';
	$slug = 'attactions';
	$icon = 'location'; //* https://developer.wordpress.org/resource/dashicons/
	$supports = array( 'title', 'thumbnail' );

	$labels = array(
		'name' => $name_plural,
		'singular_name' => $name_singular,
		'add_new' => 'Add new',
		'add_new_item' => 'Add new ' . $name_singular,
		'edit_item' => 'Edit ' . $name_singular,
		'new_item' => 'New ' . $name_singular,
		'view_item' => 'View ' . $name_singular,
		'search_items' => 'Search ' . $name_plural,
		'not_found' =>  'No ' . $name_plural . ' found',
		'not_found_in_trash' => 'No ' . $name_plural . ' found in trash',
		'parent_item_colon' => '',
		'menu_name' => $name_plural,
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => $slug ),
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon' => 'dashicons-' . $icon,
		'supports' => $supports,
	);

	register_post_type( $post_type, $args );

}