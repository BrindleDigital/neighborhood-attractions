<?php

add_filter( 'manage_attractions_posts_columns', 'na_default_admin_columns' );
function na_default_admin_columns( $columns ) {
	
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'image' => __( 'Image' ),
		'na_attractions_marker_id' => __( 'Marker' ),
		'title' => __( 'Title', 'na' ),
		'na_attractions_address' => __( 'Address', 'na' ),
		'na_attractions_description' => __( 'Description', 'na' ),
		'na_latitude' => __( 'Latitude', 'na' ),
		'na_longitude' => __( 'Longitude', 'na' ),
		'attraction_type' => __( 'Attraction Type', 'na' ),
		'na_attractions_url' => __( 'URL', 'na' ),
		'na_attractions_always_show' => __( 'Always Show on map?', 'na' ),
	);
	
	return $columns;
	
}

add_action( 'manage_attractions_posts_custom_column', 'na_attractions_default_column_content', 10, 2);
function na_attractions_default_column_content( $column, $post_id ) {
	
	?>
	<style>
		th#title {
			width: 250px;
		} 
		
		th#image {
			width: 70px;
		}
		
		th#na_attractions_marker_id {
			width: 56px;
		}
		
		th#na_attractions_address {
			width: 300px;
		}
		
		th#na_latitude,
		th#na_longitude {
			width: 100px;
		}
	</style>
	
	<?php

	// Image column
	if ( 'image' === $column )
		echo get_the_post_thumbnail( $post_id, array(60, 60) );
		
	if ( 'na_attractions_marker_id' === $column )        
		echo wp_get_attachment_image( get_post_meta( $post_id, 'na_attractions_marker_id', true ), array( 36, 36 ) );
	
	if ( 'title' === $column )
		echo esc_attr( get_the_title( $post_id ) );
		
	if ( 'na_attractions_address' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_attractions_address', true ) );
		
	if ( 'na_latitude' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_latitude', true ) );
		
	if ( 'na_longitude' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_longitude', true ) );
		
	if ( 'na_attractions_url' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_attractions_url', true ) );
		
	if ( 'na_attractions_description' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_attractions_description', true ) );
	
	if ( 'attraction_type' === $column ) {
		$terms = get_the_terms( $post_id, 'attractiontypes' );
		$count = 0;
		
		if ( $terms ) {
			foreach( $terms as $term ) {
				if ( $count != 0 )
					echo ', ';
					
				echo $term->name;
				$count++;
			}
		}            
	}
		
	if ( 'na_attractions_always_show' === $column ) {
		$always_show = get_post_meta( $post_id, 'na_attractions_always_show', true );
		
		if ( $always_show ) {
			echo 'Yes';
		} else {
			echo 'No';
		}
	}
	
}
