<?php 

/**
 * Check if there's anything needing geocoded
 */
add_action( 'init', 'na_geocode' );
function na_geocode() {
    
    $options = get_option( 'attractions_settings' );
    $positionstack_api_key = $options['positionstack_api_key'];
            
    // bail if we don't have an API key, because then we won't be able to geocode anyway
    if ( !$positionstack_api_key )
        return;
    
    $args = array(
        'post_type' => 'attractions',
        'posts_per_page' => '-1',
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'na_latitude',
                'compare' => 'NOT EXISTS'
            ),
            array(
                'key' => 'na_longitude',
                'compare' => 'NOT EXISTS'
            ),
        ),
    );
    
    $posts = get_posts( $args );

    
    // bail if we don't have anything needing geocoded
    if ( !$posts )
        return;
                
    foreach( $posts as $post_id ) {
        console_log( $post_id );
        
        do_action( 'na_geocoding_do_get_lat_long', $post_id );        
    }
    
}

add_action( 'na_geocoding_do_get_lat_long', 'na_geocoding_get_lat_long', 10, 1 );
function na_geocoding_get_lat_long( $post_id ) {
    
    $options = get_option( 'attractions_settings' );
    $positionstack_api_key = $options['positionstack_api_key'];
    
    // get the address from the post
    $na_attractions_address = get_post_meta( $post_id, 'na_attractions_address', true );
    $na_latitude = get_post_meta( $post_id, 'na_latitude', true );
    $na_longitude = get_post_meta( $post_id, 'na_longitude', true );
                    
    // bail if there's no maps api key set
    if ( !$positionstack_api_key )
        return;
           
    // bail if there's no address set to geocode
    if ( !$na_attractions_address )
        return;
        
    // var_dump( $na_latitude );
    // var_dump( $na_longitude );
            
    // bail if we already have a lat or long (we only geocode if we need it)
    if ( $na_latitude || $na_longitude )
        return;
                    
    // url encode the address
    $na_attractions_address = urlencode( $na_attractions_address );
      
    // google map geocode api url
    $url = sprintf( 'http://api.positionstack.com/v1/forward?access_key=%s&query=%s', $positionstack_api_key, $na_attractions_address );
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    
    curl_close($curl);
    
    $response_php_object = json_decode( $response );
    $data = $response_php_object->data;
    
    $lat = esc_html( $data[0]->latitude );
    $long = esc_html( $data[0]->longitude);
    
    if ( $lat && $long ) {
        $success = update_post_meta( $post_id, 'na_latitude', $lat );
        $success = update_post_meta( $post_id, 'na_longitude', $long );
    } else {
        $success = update_post_meta( $post_id, 'na_latitude', 'Geocoding failed. Please delete this and try again.' );
        $success = update_post_meta( $post_id, 'na_longitude', 'Geocoding failed. Please delete this and try again.' );
    }
}
