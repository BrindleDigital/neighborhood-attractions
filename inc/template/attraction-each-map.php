<?php

add_action( 'na_do_attractions_each_map', 'na_attractions_each_map' );
function na_attractions_each_map() {
    
    $title = get_the_title();
    $na_attractions_address = get_post_meta( get_the_ID(), 'na_attractions_address', true );
    $na_latitude = get_post_meta( get_the_ID(), 'na_latitude', true );
    $na_longitude = get_post_meta( get_the_ID(), 'na_longitude', true );
    $na_attractions_marker = get_post_meta( get_the_ID(), 'na_attractions_marker', true );
    $na_attractions_url = get_post_meta( get_the_ID(), 'na_attractions_url', true );
    $na_attractions_description = get_post_meta( get_the_ID(), 'na_attractions_description', true );
    $background = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	    
    //! add return if there's a setting to not show the map
    
    echo '<div class="map-markup" style="display:none;">';
    
        if ( $background ) 
            printf( '<div class="featured" style="background-image:url( %s )"></div>', $background );
    
        echo '<div class="attractions-content">';
                
            if ( $title )
                printf( '<h3>%s</h3>', $title );
                
            if ( $na_attractions_address )
                printf( '<p class="address">%s</p>', $na_attractions_address );
                
            if ( $na_attractions_description )
                printf( '<p class="description">%s</p>', $na_attractions_description );
                
            if ( $na_attractions_url )
                printf( '<p class="url"><a href="%s" target="_blank">View online</a></p>', $na_attractions_url );
                
            printf( '<p class="directions"><a target="_blank" href="https://www.google.com/maps?q=%s%20%s">Get directions</p>', $title, $na_attractions_address );
                
            edit_post_link( 'Edit attraction', '<small>', '</small>' );
        
        echo '</div>';
        
    echo '</div>';
}