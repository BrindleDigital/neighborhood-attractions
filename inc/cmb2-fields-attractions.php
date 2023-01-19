<?php

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( NEIGHBORHOOD_ATTRACTIONS_DIR . 'vendor/cmb2/init.php' ) ) {
	require_once NEIGHBORHOOD_ATTRACTIONS_DIR . 'vendor/cmb2/init.php';
} elseif ( file_exists( NEIGHBORHOOD_ATTRACTIONS_DIR . 'vendor/CMB2/init.php' ) ) {
	require_once NEIGHBORHOOD_ATTRACTIONS_DIR . 'vendor/CMB2/init.php';
}

add_action( 'cmb2_admin_init', 'na_register_attractions_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function na_register_attractions_metabox() {
	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$attractions_details = new_cmb2_box( array(
		'id'            => 'na_attractions_metabox',
		'title'         => esc_html__( 'Attraction details', 'na' ),
		'object_types'  => array( 'attractions' ), // Post type
		// 'show_on_cb' => 'na_show_if_front_page', // function should return a bool value
		// 'context'    => 'normal',
		// 'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		'classes'    => 'na-attractions-fields', // Extra cmb2-wrap classes
		// 'classes_cb' => 'na_add_some_classes', // Add classes through a callback.

		/*
		 * The following parameter is any additional arguments passed as $callback_args
		 * to add_meta_box, if/when applicable.
		 *
		 * CMB2 does not use these arguments in the add_meta_box callback, however, these args
		 * are parsed for certain special properties, like determining Gutenberg/block-editor
		 * compatibility.
		 *
		 * Examples:
		 *
		 * - Make sure default editor is used as metabox is not compatible with block editor
		 *      [ '__block_editor_compatible_meta_box' => false/true ]
		 *
		 * - Or declare this box exists for backwards compatibility
		 *      [ '__back_compat_meta_box' => false ]
		 *
		 * More: https://wordpress.org/gutenberg/handbook/extensibility/meta-box/
		 */
		// 'mb_callback_args' => array( '__block_editor_compatible_meta_box' => false ),
	) );
	
	$attractions_details->add_field( array(
		'name' => esc_html__( 'Address', 'na' ),
		// 'desc' => esc_html__( 'field description (optional)', 'na' ),
		'id'   => 'na_attractions_address',
		'type' => 'text',
	) );
	
	$attractions_details->add_field( array(
		'name' => esc_html__( 'Latitude', 'na'),
		// 'description' => esc_html__( 'Latitude', 'na' ),
		'id'   => 'na_latitude',
		'type' => 'text',
		// 'attributes' => array(
		// 	'type' => 'number',
		// ),
	) );
	
	$attractions_details->add_field( array(
		'name' => esc_html__( 'Longitude', 'na'),
		'description' => esc_html__( 'You can get this information from <a href="https://www.gps-coordinates.net/" target="_blank">here</a>', 'na' ),
		'id'   => 'na_longitude',
		'type' => 'text',
		// 'attributes' => array(
		// 	'type' => 'number',
		// ),
	) );
	
	$attractions_details->add_field( array(
		'name' => esc_html__( 'Website URL', 'na' ),
		// 'desc' => esc_html__( 'field description (optional)', 'na' ),
		'id'   => 'na_attractions_url',
		'type' => 'text_url',
		// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
		// 'repeatable' => true,
	) );
	
	$attractions_details->add_field( array(
		'name'    => 'Marker',
		'desc'    => 'Upload a custom marker for this attraction. Suggested size: 36x36 pixels (but it will be rendered at full size)',
		'id'      => 'na_attractions_marker',
		'type'    => 'file',
		// Optional:
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
		'text'    => array(
			'add_upload_file_text' => 'Add marker' // Change upload button text. Default: "Add or Upload File"
		),
		// query_args are passed to wp.media's library query.
		'query_args' => array(
			// 'type' => 'application/pdf', // Make library only display PDFs.
			// Or only allow gif, jpg, or png images
			'type' => array(
			    // 'image/gif',
			    'image/jpeg',
			    'image/png',
			    'image/svg',
			),
		),
		'preview_size' => 'medium', // Image size to use when previewing in the admin.
	) );
	
	$attractions_details->add_field( array(
		'name' => esc_html__( 'Description', 'na' ),
		'desc' => esc_html__( 'A short description of the attraction (optional)', 'na' ),
		'id'   => 'na_attractions_description',
		'type' => 'textarea_small',
	) );

}
