<?php

add_action( 'na_do_attractions_each_map', 'na_attractions_each_map' );
function na_attractions_each_map() {
    
    $title = get_the_title();
    $title_processed = str_replace('&#038;', 'and', $title);
    $na_attractions_address = get_post_meta( get_the_ID(), 'na_attractions_address', true );
    $na_latitude = get_post_meta( get_the_ID(), 'na_latitude', true );
    $na_longitude = get_post_meta( get_the_ID(), 'na_longitude', true );
    $na_attractions_marker_id = get_post_meta( get_the_ID(), 'na_attractions_marker_id', true );
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
                
            echo '<p class="buttons">';
                
                if ( $na_attractions_url )
                    printf( '<a class="url" href="%s" target="_blank">View online</a>', $na_attractions_url );
                    
                printf( '<a class="directions" target="_blank" href="https://www.google.com/maps?q=%s %s">Get directions</a>', $na_attractions_address, $title_processed );
            
            echo '</p>';
                
            edit_post_link( 'Edit attraction', '<small>', '</small>' );
        
        echo '</div>';
        
    echo '</div>';
}
