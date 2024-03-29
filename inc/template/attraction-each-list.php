<?php

add_action( 'na_do_attractions_each_list', 'na_attractions_each_list' );
function na_attractions_each_list() {
    
    //! add a setting to return if we're only showing the map and not the list
    
    $title = get_the_title();
    $na_attractions_address = get_post_meta( get_the_ID(), 'na_attractions_address', true );
    $na_latitude = get_post_meta( get_the_ID(), 'na_latitude', true );
    $na_longitude = get_post_meta( get_the_ID(), 'na_longitude', true );
    $na_attractions_marker_id = get_post_meta( get_the_ID(), 'na_attractions_marker_id', true );
    $na_attractions_url = get_post_meta( get_the_ID(), 'na_attractions_url', true );
    $na_attractions_description = get_post_meta( get_the_ID(), 'na_attractions_description', true );
    $background = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	
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
            
        echo '</p>';
            
        edit_post_link( 'Edit attraction', '<small>', '</small>' );
    
    echo '</div>';
    
}