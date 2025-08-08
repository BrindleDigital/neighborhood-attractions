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
		'na_render_attractions_settings'     // Callback function to render the page
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
	add_settings_field( 'max_initial_attractions', 'Max attractions before Load More', 'render_max_initial_attractions', 'na-admin-options', 'section_google_maps' );
	add_settings_section( 'section_geolocation', 'Geocoding options', 'render_section_geolocation', 'na-admin-options' );
	add_settings_field( 'positionstack_api_key', 'Positionstack API key', 'render_positionstack_api_key', 'na-admin-options', 'section_geolocation' );
}

function render_section_google_maps() {
	echo 'Settings that relate to Google Maps';
}

function render_google_api_key() {
	$options = get_option( 'attractions_settings' );
	$api_key = isset( $options['google_api_key'] ) ? $options['google_api_key'] : '';
	
	?>
	<input type="text" style="width: 500px" name="attractions_settings[google_api_key]" value="<?php echo $api_key; ?>">
	<p class="description">At minimum, this must have access to the Maps javascript API.</p>
	<?php
}

function render_google_map_style() {
	$options = get_option( 'attractions_settings' );
	
	?>
	<input type="text" style="width: 500px" name="attractions_settings[google_map_style]" value="<?php echo esc_attr( $options['google_map_style'] ); ?>">
	<p class="description">You can generate this via <a href="https://snazzymaps.com/" target="_blank">SnazzyMaps</a> or <a href="https://mapstyle.withgoogle.com/" target="_blank">Google's legacy JSON style generator</a>. Just paste the json directly into here!</p>
	<?php
}

function render_max_initial_attractions() {
	$options = get_option( 'attractions_settings' );
	$val = isset( $options['max_initial_attractions'] ) ? intval( $options['max_initial_attractions'] ) : '';
	?>
	<input type="number" min="0" style="width: 120px" name="attractions_settings[max_initial_attractions]" value="<?php echo esc_attr( $val ); ?>">
	<p class="description">Show this many attractions in the grid before a "Load more" button appears. Leave blank or 0 to show all.</p>
	<?php
}

function render_section_geolocation() {
	// silence is golden
}

function render_positionstack_api_key() {
	$options = get_option( 'attractions_settings' );
	?>
	<input type="text" style="width: 500px" name="attractions_settings[positionstack_api_key]" value="<?php echo esc_attr( $options['positionstack_api_key'] ); ?>">
	<p class="description">Google's API is difficult to work with for geocoding (it requires a separate setup from the Maps API, as they have different technical requirements). Positionstack is easier, and you can set up an account <a href="https://positionstack.com/" target="_blank">here</a>.</p>
	<?php
}