<?php

// do the same thing whether the user puts [neighborhood] or [attractions]
add_shortcode( 'attractions', 'na_attractions_render_shortcode' );
add_shortcode( 'neighborhood', 'na_attractions_render_shortcode' );
function na_attractions_render_shortcode( $atts ) {
    
    ob_start();
    
    do_action( 'na_do_render_attractions_shortcode' );
        
    return ob_get_clean();
}

add_action( 'na_do_render_attractions_shortcode', 'na_map_markup', 10 );
add_action( 'na_do_render_attractions_shortcode', 'na_categories_markup', 15 );
add_action( 'na_do_render_attractions_shortcode', 'na_attractions_markup', 20 );

function na_map_markup() {
    
    // the map itself
    wp_enqueue_script( 'neighborhood-attractions-map' );
    
    $key = 'AIzaSyCWEDSYnjMWFSyqKNomN3DkGGneZ2kHrLM';
    wp_enqueue_script( 'na-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&callback=initMap', array( 'neighborhood-attractions-map'), null, false );
        
    echo '<div class="na-attractions-map" id="na-attractions-map"></div>';
    
}

function na_categories_markup() {
    
    $terms = get_terms( 'attractiontypes' );
    
    // bail if there aren't any terms
    if ( !$terms )
        return;
        
    $count = count( $terms );
            
    // bail if we have less than two active terms
    if ( $count < 2  )
        return;
        
    echo '<div class="na-attractions-categories">';
        echo '<ul class="na-attractions-categories-wrap">';
            foreach ( $terms as $term ) {                        
                printf( '<li><button class="attraction-type-button" data-slug="%s"><span class="attractiontype">%s</span></button></li>', $term->slug, $term->name );
            }
        echo '</ul>';
    echo '</div>';
}

function na_attractions_markup() {
    
    // need this whether we're actually filtering or not, so it goes with the display
    wp_enqueue_script( 'neighborhood-attractions-filter-ajax' );
    
    echo '<div class="na-attractions"><div class="na-attractions-wrap"></div></div>';
    
}

add_action( 'wp_ajax_filter_attractions', 'na_filter_attractions' ); // wp_ajax_{ACTION HERE} 
add_action( 'wp_ajax_nopriv_filter_attractions', 'na_filter_attractions' );
function na_filter_attractions() {
    
    if ( isset(  $_POST['category'] ) ) {
        
        // pass in the clicked category
        $attraction_type_slug = $_POST['category'];    
        
    } else {
        
        $terms = get_terms( 'attractiontypes' );
        $count = count( $terms );
        
        // if we have categories, then we'll use whatever's first
        if ( $count > 1 ) {
            $attraction_type_slug = $terms[0]->slug;
        } else {
            $attraction_type_slug = null;
        }        
    }
    
    $args = array(
        'post_type' => 'attractions',
        'posts_per_page' => '-1',
    );
    
    if ( $attraction_type_slug ) {
                
        $tax_args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'attractiontypes',
                    'field'    => 'slug',
                    'terms'    => $attraction_type_slug,
                ),
            ),  
        );
        
        $args = array_merge( $args, $tax_args );
        
    }
    
    // The Query
    $custom_query = new WP_Query( $args );

    // The Loop
    if ( $custom_query->have_posts() ) {
        
        $count = 0;

        while ( $custom_query->have_posts() ) {
            
            $custom_query->the_post();
            
            $na_latitude = get_post_meta( get_the_ID(), 'na_latitude', true );
            $na_longitude = get_post_meta( get_the_ID(), 'na_longitude', true );
            $na_attractions_marker = get_post_meta( get_the_ID(), 'na_attractions_marker', true );
            
            $class = implode( ' ', get_post_class() );
            
            printf( '<div class="%s" data-latitude="%s" data-longitude="%s" data-marker="%s" data-id="%s" data-marker-id="%s">', $class, $na_latitude, $na_longitude, $na_attractions_marker, get_the_ID(), $count );

                do_action( 'na_do_attractions_each_map' );
                do_action( 'na_do_attractions_each_list' );
            
            echo '</div>';
            
            $count++;

        }
        
        // Restore postdata
        wp_reset_postdata();

    } else {
        echo '<p>None found!</p>';
    }
    
    wp_die();
}
