<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add fields to the Add Term screen for attractiontypes
 */
function na_attractiontypes_add_form_fields( $taxonomy ) {
	// ensure media scripts are available
	wp_enqueue_media();
	?>
	<div class="form-field term-na-marker-wrap">
		<label for="na_attractiontype_marker_id"><?php esc_html_e( 'Marker', 'na' ); ?></label>
		<input type="hidden" name="na_attractiontype_marker_id" id="na_attractiontype_marker_id" value="">
		<img id="na_attractiontype_marker_preview" src="" style="max-width:100px;height:auto;display:none;margin-bottom:8px;">
		<p>
			<button id="na_attractiontype_marker_button" class="button"><?php esc_html_e( 'Select Image', 'na' ); ?></button>
			<button id="na_attractiontype_marker_clear_button" class="button" style="margin-left:8px;"><?php esc_html_e( 'Clear image', 'na' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'Optional marker image for this attraction type.', 'na' ); ?></p>
	</div>

	<div class="form-field term-na-marker-height-wrap">
		<label for="na_attractiontype_marker_height"><?php esc_html_e( 'Marker height (px)', 'na' ); ?></label>
		<input type="number" step="1" min="1" id="na_attractiontype_marker_height" name="na_attractiontype_marker_height" value="">
		<p class="description"><?php esc_html_e( 'Optional marker height in pixels.', 'na' ); ?></p>
	</div>

	<script>
	jQuery(function($){
		var custom_image_frame;
		$('#na_attractiontype_marker_button').on('click', function(e){
			e.preventDefault();
			if ( custom_image_frame ) { custom_image_frame.open(); return; }
			custom_image_frame = wp.media({ title: 'Select Image', button: { text: 'Use this image' }, multiple: false });
			custom_image_frame.on('select', function(){
				var attachment = custom_image_frame.state().get('selection').first().toJSON();
				$('#na_attractiontype_marker_id').val( attachment.id );
				$('#na_attractiontype_marker_preview').attr('src', attachment.url ).show();
			});
			custom_image_frame.open();
		});

		$(document).on('click', '#na_attractiontype_marker_clear_button', function(e){
			e.preventDefault();
			$('#na_attractiontype_marker_id').val('');
			$('#na_attractiontype_marker_preview').attr('src','').hide();
		});
	});
	</script>
	<?php
}
add_action( 'attractiontypes_add_form_fields', 'na_attractiontypes_add_form_fields', 10 );


/**
 * Add fields to the Edit Term screen for attractiontypes
 */
function na_attractiontypes_edit_form_fields( $term ) {
	wp_enqueue_media();

	$term_id = $term->term_id;
	$marker_id = get_term_meta( $term_id, 'na_attractiontype_marker_id', true );
	$marker_url = $marker_id ? wp_get_attachment_url( $marker_id ) : '';
	$marker_height = get_term_meta( $term_id, 'na_attractiontype_marker_height', true );
	?>
	<tr class="form-field term-na-marker-wrap">
		<th scope="row"><label for="na_attractiontype_marker_id"><?php esc_html_e( 'Marker', 'na' ); ?></label></th>
		<td>
			<input type="hidden" name="na_attractiontype_marker_id" id="na_attractiontype_marker_id" value="<?php echo esc_attr( $marker_id ); ?>">
			<img id="na_attractiontype_marker_preview" src="<?php echo esc_url( $marker_url ); ?>" style="max-width:100px;height:auto;<?php echo $marker_url ? 'display:block;' : 'display:none;'; ?>margin-bottom:8px;">
			<p>
				<button id="na_attractiontype_marker_button" class="button"><?php esc_html_e( 'Select Image', 'na' ); ?></button>
				<button id="na_attractiontype_marker_clear_button" class="button" style="margin-left:8px;"><?php esc_html_e( 'Clear image', 'na' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Optional marker image for this attraction type.', 'na' ); ?></p>
		</td>
	</tr>

	<tr class="form-field term-na-marker-height-wrap">
		<th scope="row"><label for="na_attractiontype_marker_height"><?php esc_html_e( 'Marker height (px)', 'na' ); ?></label></th>
		<td>
			<input type="number" step="1" min="1" id="na_attractiontype_marker_height" name="na_attractiontype_marker_height" value="<?php echo esc_attr( $marker_height ); ?>">
			<p class="description"><?php esc_html_e( 'Optional marker height in pixels.', 'na' ); ?></p>
		</td>
	</tr>

	<script>
	jQuery(function($){
		var custom_image_frame;
		$('#na_attractiontype_marker_button').on('click', function(e){
			e.preventDefault();
			if ( custom_image_frame ) { custom_image_frame.open(); return; }
			custom_image_frame = wp.media({ title: 'Select Image', button: { text: 'Use this image' }, multiple: false });
			custom_image_frame.on('select', function(){
				var attachment = custom_image_frame.state().get('selection').first().toJSON();
				$('#na_attractiontype_marker_id').val( attachment.id );
				$('#na_attractiontype_marker_preview').attr('src', attachment.url ).show();
			});
			custom_image_frame.open();
		});

		$(document).on('click', '#na_attractiontype_marker_clear_button', function(e){
			e.preventDefault();
			$('#na_attractiontype_marker_id').val('');
			$('#na_attractiontype_marker_preview').attr('src','').hide();
		});
	});
	</script>
	<?php
}
add_action( 'attractiontypes_edit_form_fields', 'na_attractiontypes_edit_form_fields', 10, 2 );


/**
 * Save term meta when a term is created/edited
 */
function na_save_attractiontype_fields( $term_id ) {
	if ( isset( $_POST['na_attractiontype_marker_id'] ) ) {
		$marker_id = sanitize_text_field( wp_unslash( $_POST['na_attractiontype_marker_id'] ) );
		if ( '' === $marker_id ) {
			delete_term_meta( $term_id, 'na_attractiontype_marker_id' );
		} else {
			update_term_meta( $term_id, 'na_attractiontype_marker_id', $marker_id );
		}
	}

	if ( isset( $_POST['na_attractiontype_marker_height'] ) ) {
		$height = intval( wp_unslash( $_POST['na_attractiontype_marker_height'] ) );
		if ( $height > 0 ) {
			update_term_meta( $term_id, 'na_attractiontype_marker_height', $height );
		} else {
			delete_term_meta( $term_id, 'na_attractiontype_marker_height' );
		}
	}
}
add_action( 'created_attractiontypes', 'na_save_attractiontype_fields', 10, 2 );
add_action( 'edited_attractiontypes', 'na_save_attractiontype_fields', 10, 2 );

?>
