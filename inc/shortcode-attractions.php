<?php
/**
 * This file outputs the shortcode for [neightbood] and [attractions].
 *
 * @package rentfetch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Render the main shortcode for the Neighborhood Attractions plugin.
 *
 * @param   array $atts the shortcode atts.
 *
 * @return  string the markup.
 */
function na_attractions_render_shortcode( $atts ) {

	ob_start();

	echo '<div class="na-attractions-shortcode-wrap">';

	do_action( 'na_do_render_attractions_shortcode' );

	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'attractions', 'na_attractions_render_shortcode' );
add_shortcode( 'neighborhood', 'na_attractions_render_shortcode' );

add_action( 'na_do_render_attractions_shortcode', 'na_map_markup', 10 );
add_action( 'na_do_render_attractions_shortcode', 'na_categories_markup', 15 );
add_action( 'na_do_render_attractions_shortcode', 'na_attractions_markup', 20 );

/**
 * Render the map markup.
 *
 * @return void
 */
function na_map_markup() {

	$options = get_option( 'attractions_settings' );

	// bail if we don't have the google api key.
	if ( ! isset( $options['google_api_key'] ) ) {
		echo 'NOTE: No Google API key has been set in the Neighborhood Attractions plugin settings. <a href="/wp-admin/edit.php?post_type=attractions&page=attractions-settings">Go set that up</a>';
		return;
	}

	$key = esc_attr( $options['google_api_key'] );

	wp_enqueue_script( 'neighborhood-attractions-map' );

	wp_enqueue_script( 'na-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&callback=initMap', array( 'neighborhood-attractions-map' ), null, false );  // phpcs:ignore

	echo '<div class="na-attractions-map" id="na-attractions-map"></div>';
}

/**
 * Render the categories markup.
 *
 * @return void
 */
function na_categories_markup() {

	$terms = get_terms( 'attractiontypes' );

	// bail if there aren't any terms.
	if ( ! $terms ) {
		return;
	}

	$count = count( $terms );

	// bail if we have less than two active terms.
	if ( $count < 2 ) {
		return;
	}

	echo '<div class="na-attractions-categories">';
		echo '<ul class="na-attractions-categories-wrap">';
	foreach ( $terms as $term ) {
		printf( '<li><button class="attraction-type-button" data-slug="%s"><span class="attractiontype">%s</span></button></li>', esc_attr( $term->slug ), esc_html( $term->name ) );
	}
		echo '</ul>';
	echo '</div>';
}

/**
 * Render the attractions markup.
 *
 * @return void
 */
function na_attractions_markup() {

	// need this whether we're actually filtering or not, so it goes with the display.
	wp_enqueue_script( 'neighborhood-attractions-filter-ajax' );

	// add a class that hides items until JS applies the limit to prevent flash
	echo '<div class="na-attractions"><div class="na-attractions-wrap initial-hide"></div></div>';
}

/**
 * Filter the attractions.
 *
 * @return void
 */
function na_filter_attractions() {

	if ( isset( $_POST['category'] ) ) {

		// pass in the clicked category.
		$attraction_type_slug = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : null;

	} else {

		$terms = get_terms( 'attractiontypes' );
		$count = count( $terms );

		// if we have categories, then we'll use whatever's first.
		if ( $count > 1 ) {
			$attraction_type_slug = $terms[0]->slug;
		} else {
			$attraction_type_slug = null;
		}
	}

	$args = array(
		'post_type'      => 'attractions',
		'posts_per_page' => '-1',
		'meta_query'     => array( // phpcs:ignore
			'relation' => 'AND',
			array(
				'key'     => 'na_latitude',
				'compare' => '!=',
				'value'   => array( '' ),
			),
			array(
				'key'     => 'na_longitude',
				'compare' => '!=',
				'value'   => array( '' ),
			),
			array(
				'key'     => 'na_attractions_always_show',
				'compare' => 'NOT EXISTS',
			),
		),
	);

	if ( $attraction_type_slug ) {

		$tax_args = array(
			'tax_query' => array( // phpcs:ignore
				array(
					'taxonomy' => 'attractiontypes',
					'field'    => 'slug',
					'terms'    => $attraction_type_slug,
				),
			),
		);

		$args = array_merge( $args, $tax_args );

	}

	// The Query.
	$custom_query = new WP_Query( $args );

	// The Loop.
	if ( $custom_query->have_posts() ) {

		$count = 0;

		while ( $custom_query->have_posts() ) {

			$custom_query->the_post();

			$na_latitude  = esc_attr( get_post_meta( get_the_ID(), 'na_latitude', true ) );
			$na_longitude = esc_attr( get_post_meta( get_the_ID(), 'na_longitude', true ) );

			// Prefer post-specific marker. If none, fall back to the first assigned attractiontypes term marker (if any).
			$post_marker_id = get_post_meta( get_the_ID(), 'na_attractions_marker_id', true );
			$post_marker_url = $post_marker_id ? wp_get_attachment_url( $post_marker_id, 'full' ) : '';
			$post_marker_height = get_post_meta( get_the_ID(), 'na_attractions_marker_height', true );

			$na_attractions_marker_id = '';
			$na_attractions_marker_height = '';

			if ( $post_marker_url ) {
				$na_attractions_marker_id = $post_marker_url;
				if ( $post_marker_height ) {
					$na_attractions_marker_height = esc_attr( $post_marker_height );
				}
			} else {
				$terms = get_the_terms( get_the_ID(), 'attractiontypes' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term = reset( $terms );
					$term_marker_id = get_term_meta( $term->term_id, 'na_attractiontype_marker_id', true );
					$term_marker_url = $term_marker_id ? wp_get_attachment_url( $term_marker_id, 'full' ) : '';
					if ( $term_marker_url ) {
						$na_attractions_marker_id = $term_marker_url;
						$term_marker_height = get_term_meta( $term->term_id, 'na_attractiontype_marker_height', true );
						if ( $term_marker_height ) {
							$na_attractions_marker_height = esc_attr( $term_marker_height );
						}
					}
				}
			}

			$class = implode( ' ', get_post_class() );

			printf( '<div class="%s" data-latitude="%s" data-longitude="%s" data-marker="%s" data-marker-height="%s" data-id="%s" data-marker-id="%s">', esc_attr( $class ), esc_attr( $na_latitude ), esc_attr( $na_longitude ), esc_attr( $na_attractions_marker_id ), esc_attr( $na_attractions_marker_height ), (int) get_the_ID(), esc_attr( $count ) );

				do_action( 'na_do_attractions_each_map' );
				do_action( 'na_do_attractions_each_list' );

			echo '</div>';

			++$count;

		}

		// Restore postdata.
		wp_reset_postdata();

	}

	$always_show_args = array(
		'post_type'      => 'attractions',
		'posts_per_page' => '-1',
		'meta_query'     => array( // phpcs:ignore
			array(
				'key'   => 'na_attractions_always_show',
				'value' => true,
			),
		),
	);

	// The Query.
	$always_show_attractions = new WP_Query( $always_show_args );

	// The Loop.
	if ( $always_show_attractions->have_posts() ) {

		$count = 0;

		while ( $always_show_attractions->have_posts() ) {

			$na_always_show = get_post_meta( get_the_ID(), 'na_always_show', true );
			console_log( $na_always_show );

			$always_show_attractions->the_post();

			$na_latitude  = get_post_meta( get_the_ID(), 'na_latitude', true );
			$na_longitude = get_post_meta( get_the_ID(), 'na_longitude', true );

			// Prefer post-specific marker. If none, fall back to the first assigned attractiontypes term marker (if any).
			$post_marker_id = get_post_meta( get_the_ID(), 'na_attractions_marker_id', true );
			$post_marker_url = $post_marker_id ? wp_get_attachment_url( $post_marker_id, 'full' ) : '';
			$post_marker_height = get_post_meta( get_the_ID(), 'na_attractions_marker_height', true );

			$na_attractions_marker_id = '';
			$na_attractions_marker_height = '';

			if ( $post_marker_url ) {
				$na_attractions_marker_id = $post_marker_url;
				if ( $post_marker_height ) {
					$na_attractions_marker_height = esc_attr( $post_marker_height );
				}
			} else {
				$terms = get_the_terms( get_the_ID(), 'attractiontypes' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term = reset( $terms );
					$term_marker_id = get_term_meta( $term->term_id, 'na_attractiontype_marker_id', true );
					$term_marker_url = $term_marker_id ? wp_get_attachment_url( $term_marker_id, 'full' ) : '';
					if ( $term_marker_url ) {
						$na_attractions_marker_id = $term_marker_url;
						$term_marker_height = get_term_meta( $term->term_id, 'na_attractiontype_marker_height', true );
						if ( $term_marker_height ) {
							$na_attractions_marker_height = esc_attr( $term_marker_height );
						}
					}
				}
			}

			$class = implode( ' ', get_post_class() );

			// Add a flag class so JS and CSS can treat these as map-only items
			$class .= ' na-map-only';

			printf( '<div style="display: none;" aria-hidden="true" class="%s" data-latitude="%s" data-longitude="%s" data-marker="%s" data-marker-height="%s" data-id="%s" data-marker-id="%s">', esc_attr( $class ), esc_attr( $na_latitude ), esc_attr( $na_longitude ), esc_attr( $na_attractions_marker_id ), esc_attr( $na_attractions_marker_height ), (int) get_the_ID(), esc_attr( $count ) );

				// Only render map markup so these never appear in results
				do_action( 'na_do_attractions_each_map' );

			echo '</div>';

			++$count;

		}

		// Restore postdata.
		wp_reset_postdata();

	}

	wp_die();
}
add_action( 'wp_ajax_filter_attractions', 'na_filter_attractions' ); // wp_ajax_{ACTION HERE}.
add_action( 'wp_ajax_nopriv_filter_attractions', 'na_filter_attractions' ); // wp_ajax_nopriv_{ACTION HERE}.
