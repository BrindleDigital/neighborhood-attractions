<?php
/**
 * Add default admin columns for attractiontypes taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter( 'manage_edit-attractiontypes_columns', 'na_attractiontypes_columns' );
function na_attractiontypes_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'name' === $key ) {
            // after the Name column, insert our two columns
            $new['na_attractiontype_marker'] = __( 'Marker', 'na' );
            $new['na_attractiontype_marker_height'] = __( 'Marker Height', 'na' );
        }
    }
    return $new;
}

add_filter( 'manage_attractiontypes_custom_column', 'na_attractiontypes_custom_column', 10, 3 );
function na_attractiontypes_custom_column( $out, $column, $term_id ) {
    if ( 'na_attractiontype_marker' === $column ) {
        $marker_id = get_term_meta( $term_id, 'na_attractiontype_marker_id', true );
        if ( $marker_id ) {
            $url = wp_get_attachment_url( $marker_id );
            if ( $url ) {
                $out = sprintf( '<img src="%s" style="max-width:48px;height:auto;">', esc_url( $url ) );
            }
        }
    }

    if ( 'na_attractiontype_marker_height' === $column ) {
        $h = get_term_meta( $term_id, 'na_attractiontype_marker_height', true );
        if ( $h ) {
            $out = esc_html( intval( $h ) ) . 'px';
        } else {
            $out = '—';
        }
    }

    return $out;
}

// Add a little inline CSS for header widths when the taxonomy table is present
add_action( 'admin_head-edit-tags.php', 'na_attractiontypes_admin_head' );
function na_attractiontypes_admin_head() {
    $screen = get_current_screen();
    if ( ! $screen || 'edit-attractiontypes' !== $screen->id ) {
        return;
    }
    ?>
    <style>
        th.column-na_attractiontype_marker { width: 70px; }
        th.column-na_attractiontype_marker_height { width: 120px; }
    </style>
    <?php
}

?>
