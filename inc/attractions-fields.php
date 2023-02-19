<?php

add_action( 'add_meta_boxes', 'na_register_attractions_details_metabox' );
function na_register_attractions_details_metabox() {
    add_meta_box(
        'na_attractions_details', // ID of the metabox
        'Attraction Details', // Title of the metabox
        'na_attractions_details_metabox_callback', // Callback function to render the metabox
        'attractions', // Post type to add the metabox to
        'normal', // Priority of the metabox
        'default' // Context of the metabox
    );
}

function na_attractions_details_metabox_callback( $post ) {
    wp_nonce_field( 'custom_metabox_nonce_action', 'custom_metabox_nonce_field' );
    
    ?>
    <div class="na-metabox">
        <style scoped>
            .na-metabox {
                padding: 30px 10px;
            }
            
            .na-metabox .na-columns-2 {
                grid-template-columns: 1fr 1fr;
            }
            
            .na-metabox .na-columns {
                display: grid;
                gap: 30px;
                border-bottom: 1px solid rgba( 0,0,0,0.15 );
                padding: 0 0 30px;
                margin: 0 0 30px;
            }
            
            
            .na-metabox .na-columns:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border: none;
            }
            
            .na-metabox .na-columns .na-meta-option {
                margin: 0;
                padding: 0;
                border: none;
                display: block;
            }
            
            .na-metabox .na-meta-option {
                display: grid;
                grid-template-columns: 250px 1fr;
                border-bottom: 1px solid rgba( 0,0,0,0.15 );
                padding: 0 0 30px;
                margin: 0 0 30px;
            }
            
            .na-metabox .na-meta-option:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            
            .na-metabox .na-meta-option input[type="text"],
            .na-metabox .na-meta-option input[type="number"],
            .na-metabox .na-meta-option textarea {
                display: block;
                width: 100%;
            }
            
            .na-metabox .na-meta-option textarea {
                
            }
            
            .na-metabox .na-meta-option .column {
                display: block;
            }
            
            .na-metabox .na-meta-option label {
                font-weight: 600;
                color: black;
                width: 100%;
                padding: 6px 0;
                line-height: 1.4;
                display: block;
            }
            
            .na-metabox .na-meta-option p.description {
                padding: 5px 0 0;
            }
            
            .na-metabox ul.checkboxes {
                margin: 0;
                padding: 0;
            }
            
            .na-metabox ul.checkboxes li.checkbox {
                display: grid;
                grid-template-columns: auto 1fr;
                margin: 0;
                padding: 0;
                gap: 10px;
            }
            
            .na-metabox ul.checkboxes li.checkbox label {
                font-weight: normal;
                padding: 0;
                margin: 0;
                color: inherit;
            }
            
            .na-metabox ul.checkboxes li.checkbox input {
                display: block;
                margin: 3px 0 0;
            }
            
            @media( max-width: 768px ) {
                .na-metabox .na-meta-option {
                    grid-template-columns: 1fr;
                }
            }
            
        </style>
        
        <?php $na_attractions_address = get_post_meta( $post->ID, 'na_attractions_address', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_attractions_address">Address</label>
            </div>
            <div class="column">                
                <input type="text" id="na_attractions_address" name="na_attractions_address" value="<?php echo esc_attr( $na_attractions_address ); ?>">
                <p class="description">Please only include an address here. Be as complete as possible, and do not include the location name (that should be the title).</p>
            </div>
        </div>
        
        <?php $na_latitude = get_post_meta( $post->ID, 'na_latitude', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_latitude">Latitude</label>
            </div>
            <div class="column">                
                <input type="number" step="0.00000000001" id="na_latitude" name="na_latitude" value="<?php echo esc_attr( $na_latitude ); ?>">
                <p class="description">You can get this information from <a href="https://www.latlong.net/convert-address-to-lat-long.html" target="_blank">here</a>, or add Positionstack API information (free) in the <a href="/wp-admin/edit.php?post_type=attractions&page=attractions-settings">plugin settings.</a></p>
            </div>
        </div>
        
        <?php $na_longitude = get_post_meta( $post->ID, 'na_longitude', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_longitude">Longitude</label>
            </div>
            <div class="column">                
                <input type="number" step="0.00000000001" id="na_longitude" name="na_longitude" value="<?php echo esc_attr( $na_longitude ); ?>">
            </div>
        </div>
        
        <?php $na_attractions_url = get_post_meta( $post->ID, 'na_attractions_url', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_attractions_url">Website URL</label>
            </div>
            <div class="column">                
                <input type="text" id="na_attractions_url" name="na_attractions_url" value="<?php echo esc_url( $na_attractions_url ); ?>">
            </div>
        </div>
        
        <script>
            jQuery(document).ready(function( $ ) {
	
                jQuery(function($) {
                    var custom_image_frame;
                    $('#na_attractions_marker_button').click(function(e) {
                        e.preventDefault();
                        if (custom_image_frame) {
                            custom_image_frame.open();
                            return;
                        }
                        custom_image_frame = wp.media({
                            title: 'Select Image',
                            button: {
                                text: 'Use this image'
                            },
                            multiple: false
                        });
                        custom_image_frame.on('select', function() {
                            var attachment = custom_image_frame.state().get('selection').first().toJSON();
                            console.log( attachment );
                            $( '#na_attractions_marker_id' ).attr( 'value', attachment.id );
                            $( '#na_attractions_marker_preview' ).attr( 'src', attachment.url );
                        });
                        custom_image_frame.open();
                    });
                
                });
            
            });
        </script>
        
        <?php $na_attractions_marker_id = get_post_meta( $post->ID, 'na_attractions_marker_id', true ); ?>
        <?php $na_attractions_marker_url = wp_get_attachment_url( $na_attractions_marker_id ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_attractions_marker_id">Marker</label>
            </div>
            <div class="column">
                <input type="hidden" name="na_attractions_marker_id" id="na_attractions_marker_id" value="<?php echo esc_attr( $na_attractions_marker_id ); ?>">
                <img id="na_attractions_marker_preview" src="<?php echo esc_url( $na_attractions_marker_url ); ?>" style="max-width:100px;height:auto;display:block;margin-bottom:10px;">
                <button id="na_attractions_marker_button" class="button">Select Image</button>
                <p class="description">Upload a custom marker for this attraction. Suggested size: 36x36 pixels (but it will be rendered at full size).</p>
            </div>
        </div>
        
        <?php $na_attractions_description = get_post_meta( $post->ID, 'na_attractions_description', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_attractions_description">Description</label>
            </div>
            <div class="column">                
                <textarea rows="4" id="na_attractions_description" name="na_attractions_description"><?php echo esc_attr( $na_attractions_description ); ?></textarea>
                <p class="description">(optional)</p>
            </div>
        </div>
        
        <?php $na_attractions_always_show = get_post_meta( $post->ID, 'na_attractions_always_show', true ); ?>
        <div class="na-meta-option">
            <div class="column">
                <label for="na_attractions_always_show">Always show?</label>
            </div>
            <div class="column">
                <ul class="checkboxes">
                    <li class="checkbox">
                        <input type="checkbox" id="na_attractions_always_show" name="na_attractions_always_show" <?php checked( $na_attractions_always_show, '1' ); ?>>
                        <label for="na_attractions_always_show">If you'd like this location to always show, no matter what, toggle this checkbox on (a typical use case would be an apartment complex location, when most of the other locations being shown are nearby attractions).</label>
                    </li>
                </ul>
                
                
            </div>
        </div>
    </div>
    <?php
}

add_action( 'save_post', 'save_custom_metabox' );
function save_custom_metabox( $post_id ) {
    if ( ! isset( $_POST['custom_metabox_nonce_field'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['custom_metabox_nonce_field'], 'custom_metabox_nonce_action' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['na_attractions_address'] ) )
        update_post_meta( $post_id, 'na_attractions_address', sanitize_text_field( $_POST['na_attractions_address'] ) );
        
    if ( isset( $_POST['na_latitude'] ) )
        update_post_meta( $post_id, 'na_latitude', sanitize_text_field( $_POST['na_latitude'] ) );
    
    if ( isset( $_POST['na_longitude'] ) )
        update_post_meta( $post_id, 'na_longitude', sanitize_text_field( $_POST['na_longitude'] ) );
    
    if ( isset( $_POST['na_attractions_url'] ) )
        update_post_meta( $post_id, 'na_attractions_url', sanitize_text_field( $_POST['na_attractions_url'] ) );
        
    if ( isset( $_POST['na_attractions_marker_id'] ) )
        update_post_meta( $post_id, 'na_attractions_marker_id', $_POST['na_attractions_marker_id'] );
        
    if ( isset( $_POST['na_attractions_description'] ) )
        update_post_meta( $post_id, 'na_attractions_description', sanitize_text_field( $_POST['na_attractions_description'] ) );
        
    if ( isset( $_POST['na_attractions_always_show'] ) ) {
        update_post_meta( $post_id, 'na_attractions_always_show', true );        
    } else {
        delete_post_meta( $post_id, 'na_attractions_always_show' );        
    }
        
    
}


