<?php

add_action( 'wp_enqueue_scripts', 'na_enqueue' );
function na_enqueue() {
	
	// Plugin styles
    wp_enqueue_style( 'neighborhood-attractions-style', NEIGHBORHOOD_ATTRACTIONS_DIR . 'assets/css/neighborhood-attractions-style.css', array(), NEIGHBORHOOD_ATTRACTIONS_VERSION, 'screen' );
    
}