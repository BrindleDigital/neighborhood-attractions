<?php

function na_acp_editing_change_date_format_for_marker( $value, AC\Column $column ) {
    
	if ( $column instanceof ACP\Column\CustomField && 'na_attractions_marker' === $column->get_meta_key() && $value ) {

		// Convert submitted value to a unix timestamp
        $value = wp_get_attachment_image_url( $value );
		// $value = strtotime( $value );
	}

	return $value;
}

// add_filter( 'acp/editing/save_value', 'na_acp_editing_change_date_format_for_marker', 10, 2 );