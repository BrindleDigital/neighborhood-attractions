<?php

add_filter( 'manage_attractions_posts_columns', 'na_default_admin_columns' );
function na_default_admin_columns( $columns ) {
	
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'image' => __( 'Image' ),
		'na_attractions_marker_id' => __( 'Marker' ),
		'na_attractions_marker_height' => __( 'Height', 'na' ),
		'title' => __( 'Title', 'na' ),
		'na_attractions_address' => __( 'Address', 'na' ),
		'attraction_type' => __( 'Type', 'na' ),
		'na_attractions_url' => __( 'URL', 'na' ),
		'na_attractions_description' => __( 'Description', 'na' ),
		'na_latitude' => __( 'Latitude', 'na' ),
		'na_longitude' => __( 'Longitude', 'na' ),
		'na_attractions_always_show' => __( 'Always show on map?', 'na' ),
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
			width: 50px;
		}
		
		td.column-na_attractions_marker_id {
			width: 50px;
		}
		
		td.column-na_attractions_marker_id img {
			height: 20px !important;
			width: auto !important;
		}
		
		th#na_attractions_marker_height {
			width: 50px;
		}
		
		th#na_attractions_address {
			width: 170px;
		}

		th#na_attractions_url {
			width: 150px;
		}

		/* truncate long URLs to one line with ellipsis */
		td.column-na_attractions_url,
		td.column-na_attractions_url a {
			display: inline-block;
			max-width: 100%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}
		
		th#na_latitude,
		th#na_longitude {
			width: 100px;
		}

		th#na_attractions_always_show,
		td.column-na_attractions_always_show {
			min-width: 200px;
		}
	</style>
	
	<?php

	// Image column
	if ( 'image' === $column )
		echo get_the_post_thumbnail( $post_id, array(60, 60) );
		
	if ( 'na_attractions_marker_id' === $column ) {
		$post_marker_id = get_post_meta( $post_id, 'na_attractions_marker_id', true );
		if ( $post_marker_id ) {
			echo wp_get_attachment_image( $post_marker_id, array( 36, 36 ) );
		} else {
			// No post-level marker; try to show a term-level marker (ghosted)
			$terms = get_the_terms( $post_id, 'attractiontypes' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_marker_id = get_term_meta( $term->term_id, 'na_attractiontype_marker_id', true );
					if ( $term_marker_id ) {
						// show ghosted term marker
						echo wp_get_attachment_image( $term_marker_id, array( 36, 36 ), false, array( 'style' => 'opacity:0.5;' ) );
						break;
					}
				}
			}
		}
	}

	if ( 'na_attractions_marker_height' === $column ) {
		$height = get_post_meta( $post_id, 'na_attractions_marker_height', true );
		if ( $height ) {
			echo esc_html( intval( $height ) ) . 'px';
		} else {
			// no post-level height — if there's no post-level marker, show term-level height ghosted
			$post_marker_id = get_post_meta( $post_id, 'na_attractions_marker_id', true );
			if ( ! $post_marker_id ) {
				$terms = get_the_terms( $post_id, 'attractiontypes' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$term_marker_id = get_term_meta( $term->term_id, 'na_attractiontype_marker_id', true );
						$term_height = get_term_meta( $term->term_id, 'na_attractiontype_marker_height', true );
						if ( $term_marker_id && $term_height ) {
							echo '<span style="opacity:0.5;">' . esc_html( intval( $term_height ) ) . 'px</span>';
							break;
						}
					}
				}
			} else {
				echo '—';
			}
		}
	}
	
	if ( 'title' === $column )
		echo esc_attr( get_the_title( $post_id ) );
		
	if ( 'na_attractions_address' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_attractions_address', true ) );
		
	if ( 'na_latitude' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_latitude', true ) );
		
	if ( 'na_longitude' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_longitude', true ) );
		
	if ( 'na_attractions_url' === $column ) {
		$raw_url = get_post_meta( $post_id, 'na_attractions_url', true );
		if ( $raw_url ) {
			$url = esc_url( $raw_url );
			// show the URL as a truncated link (CSS above handles truncation)
			echo '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . esc_html( $raw_url ) . '</a>';
		}
	}
		
	if ( 'na_attractions_description' === $column )
		echo esc_attr( get_post_meta( $post_id, 'na_attractions_description', true ) );
	
	if ( 'attraction_type' === $column ) {
		$terms = get_the_terms( $post_id, 'attractiontypes' );
		$count = 0;
		
		if ( $terms ) {
			foreach( $terms as $term ) {
				if ( $count != 0 )
					echo ', ';
					// link to term edit page in admin
					$edit_link = get_edit_term_link( $term->term_id, 'attractiontypes' );
					if ( $edit_link ) {
						echo '<a href="' . esc_url( $edit_link ) . '">' . esc_html( $term->name ) . '</a>';
					} else {
						echo esc_html( $term->name );
					}
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
