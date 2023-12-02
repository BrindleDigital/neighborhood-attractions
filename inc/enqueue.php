<?php

add_action( 'wp_enqueue_scripts', 'na_enqueue' );
function na_enqueue() {
	
	// Plugin styles
    wp_enqueue_style( 'neighborhood-attractions-style', NEIGHBORHOOD_ATTRACTIONS_URL . 'assets/css/neighborhood-attractions.css', array(), NEIGHBORHOOD_ATTRACTIONS_VERSION, 'screen' );
    
    // Script
    wp_register_script( 'neighborhood-attractions-filter-ajax', NEIGHBORHOOD_ATTRACTIONS_URL . 'assets/js/neighborhood-attractions-filter-ajax.js', array( 'jquery' ), NEIGHBORHOOD_ATTRACTIONS_VERSION, true );
    wp_register_script( 'neighborhood-attractions-map', NEIGHBORHOOD_ATTRACTIONS_URL . 'assets/js/neighborhood-attractions-map.js', array( 'jquery' ), NEIGHBORHOOD_ATTRACTIONS_VERSION, true );
	
	
	$options = get_option( 'attractions_settings' );
	if ( isset( $options['google_map_style'] ) ) {
		$map_styles = $options['google_map_style'];
	} else {
		$map_styles = null;
	}
		
	// Localize the google maps script, then enqueue that
	$maps_options = array(
		'json_style' => json_decode( $map_styles ),
		// 'marker_url' => get_field( 'google_map_marker', 'option' ),
	);
	
	// Localize and load the map itself
	wp_localize_script( 'neighborhood-attractions-map', 'options', $maps_options );
    
}