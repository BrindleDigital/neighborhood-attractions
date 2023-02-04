<?php

/**
 * Admin page addition
 */
add_action( 'admin_menu', 'na_add_attractions_settings_page' );
function na_add_attractions_settings_page() {    
    add_submenu_page(
        'edit.php?post_type=attractions',   // The parent page's menu slug
        'Attractions Settings',             // Page title
        'Attractions Settings',             // Menu title
        'manage_options',                   // Capability required to access the page
        'attractions-settings',             // Menu slug
        'na_render_attractions_settings'           // Callback function to render the page
    );
}


function na_render_attractions_settings() {
    ?>
    <div class="wrap">
        <h1>Attractions Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields( 'na_options_group' );
                do_settings_sections( 'na-admin-options' );
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Regiser the admin options
 */
add_action( 'admin_init', 'na_register_attractions_settings' );
function na_register_attractions_settings() {
    register_setting( 'na_options_group', 'attractions_settings' );
    add_settings_section( 'section_google_maps', 'Google Maps options', 'render_section_google_maps', 'na-admin-options' );
    add_settings_field( 'google_api_key', 'Google Maps API key', 'render_google_api_key', 'na-admin-options', 'section_google_maps' );
    add_settings_field( 'google_map_style', 'Google Maps style', 'render_google_map_style', 'na-admin-options', 'section_google_maps' );
}

function render_section_google_maps() {
    echo 'Settings that relate to Google Maps';
}

function render_google_api_key() {
    $options = get_option( 'attractions_settings' );
    ?>
    <input type="text" width="100%" name="attractions_settings[field_1]" value="<?php echo esc_attr( $options['field_1'] ); ?>">
    <p class="description">At minimum, this must have access to the Maps javascript API.</p>
    <?php
}

function render_google_map_style() {
    $options = get_option( 'attractions_settings' );
    ?>
    <input type="text" name="attractions_settings[google_map_style]" value="<?php echo esc_attr( $options['google_map_style'] ); ?>">
    <p class="description">You can generate this via <a href="https://snazzymaps.com/" target="_blank">SnazzyMaps</a> or <a href="https://mapstyle.withgoogle.com/" target="_blank">Google's legacy JSON style generator</a>. Just paste the json directly into here!</p>
    <?php
}
