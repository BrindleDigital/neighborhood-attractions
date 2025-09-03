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

	// Add an Imports page directly beneath the Settings page
	add_submenu_page(
		'edit.php?post_type=attractions',   // The parent page's menu slug
		'Attraction Imports',              // Page title
		'Attraction Imports',              // Menu title
		'manage_options',                   // Capability required to access the page
		'attractions-imports',              // Menu slug
		'na_render_attractions_imports'     // Callback function to render the imports page
	);
}

// Export via admin-post so headers can be sent before admin page HTML
add_action( 'admin_post_na_export_attractions', 'na_handle_export_attractions' );
function na_handle_export_attractions() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Permission denied.' );
	}

	if ( ! check_admin_referer( 'na_export_attractions' ) ) {
		wp_die( 'Invalid export request.' );
	}

	$expected_headers = array( 'title', 'address', 'type', 'url', 'description', 'latitude', 'longitude' );

	$args = array(
		'post_type' => 'attractions',
		'posts_per_page' => -1,
		'post_status' => 'any',
	);
	$posts = get_posts( $args );

	// Send CSV headers and output
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=attractions-export.csv' );
	$output = fopen( 'php://output', 'w' );
	if ( $output ) {
		fputcsv( $output, $expected_headers );
		foreach ( $posts as $p ) {
			$post_id = $p->ID;
			$title = html_entity_decode( get_the_title( $post_id ) );
			$address = get_post_meta( $post_id, 'na_attractions_address', true );
			$lat = get_post_meta( $post_id, 'na_latitude', true );
			$lng = get_post_meta( $post_id, 'na_longitude', true );
			$url = get_post_meta( $post_id, 'na_attractions_url', true );
			$description = get_post_meta( $post_id, 'na_attractions_description', true );

			$terms = get_the_terms( $post_id, 'attractiontypes' );
			$type_names = array();
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $t ) {
					$type_names[] = $t->name;
				}
			}

			$row = array(
				$title,
				$address,
				implode( ',', $type_names ),
				$url,
				$description,
				$lat,
				$lng,
			);

			fputcsv( $output, $row );
		}
		fclose( $output );
	}
	exit;
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
	add_settings_field( 'max_initial_attractions', 'Max attractions', 'render_max_initial_attractions', 'na-admin-options', 'section_google_maps' );
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

/**
 * Render the Attractions Imports admin page and handle CSV uploads
 */
function na_render_attractions_imports() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Export existing attractions to CSV when requested (nonce-protected)
	if ( isset( $_GET['na_export_attractions'] ) ) {
		if ( ! check_admin_referer( 'na_export_attractions' ) ) {
			wp_die( 'Invalid export request.' );
		}

		$expected_headers = array( 'title', 'address', 'type', 'url', 'description', 'latitude', 'longitude' );

		$args = array(
			'post_type' => 'attractions',
			'posts_per_page' => -1,
			'post_status' => 'any',
		);
		$posts = get_posts( $args );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=attractions-export.csv' );
		$output = fopen( 'php://output', 'w' );
		if ( $output ) {
			// write header
			fputcsv( $output, $expected_headers );

			foreach ( $posts as $p ) {
				$post_id = $p->ID;
				$title = html_entity_decode( get_the_title( $post_id ) );
				$address = get_post_meta( $post_id, 'na_attractions_address', true );
				$lat = get_post_meta( $post_id, 'na_latitude', true );
				$lng = get_post_meta( $post_id, 'na_longitude', true );
				$url = get_post_meta( $post_id, 'na_attractions_url', true );
				$description = get_post_meta( $post_id, 'na_attractions_description', true );

				$terms = get_the_terms( $post_id, 'attractiontypes' );
				$type_names = array();
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $t ) {
						$type_names[] = $t->name;
					}
				}

				$row = array(
					$title,
					$address,
					implode( ',', $type_names ),
					$url,
					$description,
					$lat,
					$lng,
				);

				fputcsv( $output, $row );
			}
			fclose( $output );
		}
		exit;
	}

	// Handle form submission
	if ( isset( $_POST['na_import_csv_submit'] ) ) {
		if ( ! isset( $_POST['na_imports_nonce_field'] ) || ! wp_verify_nonce( $_POST['na_imports_nonce_field'], 'na_imports_nonce' ) ) {
			echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
		} else {
			if ( empty( $_FILES['na_import_csv'] ) || UPLOAD_ERR_OK !== $_FILES['na_import_csv']['error'] ) {
				echo '<div class="notice notice-error"><p>No file uploaded or upload error.</p></div>';
			} else {
				$tmp_name = $_FILES['na_import_csv']['tmp_name'];
				$handle = fopen( $tmp_name, 'r' );
				if ( false === $handle ) {
					echo '<div class="notice notice-error"><p>Unable to open uploaded file.</p></div>';
				} else {
					$expected_headers = array( 'title', 'address', 'type', 'url', 'description', 'latitude', 'longitude' );
					$raw_header = fgetcsv( $handle );
					$header = array_map( 'trim', array_map( 'strtolower', (array) $raw_header ) );

					if ( $header !== $expected_headers ) {
						echo '<div class="notice notice-error"><p>CSV header does not match the expected format. Please use the sample CSV.</p></div>';
						fclose( $handle );
					} else {
						$processed = 0;
						$created = 0;
						$updated = 0;
						$skipped = 0;
						$errors = array();

						// determine behavior for existing titles: skip (default) or update
						$existing_behavior = isset( $_POST['na_import_existing'] ) && 'update' === $_POST['na_import_existing'] ? 'update' : 'skip';

						while ( ( $row = fgetcsv( $handle ) ) !== false ) {
							// ensure row has at least as many columns as headers
							if ( count( $row ) < count( $expected_headers ) ) {
								continue;
							}

							$assoc = array_combine( $expected_headers, $row );

							$title = sanitize_text_field( $assoc['title'] );
							if ( '' === $title ) {
								$skipped++;
								continue;
							}

							// Check for existing post with the same title
							$existing_post = get_page_by_title( $title, OBJECT, 'attractions' );

							if ( $existing_post ) {
								if ( 'skip' === $existing_behavior ) {
									$skipped++;
									continue;
								}

								// update existing post
								$post_id = $existing_post->ID;
								$update_post = array(
									'ID' => $post_id,
									// keep post_content empty; plugin uses meta for description
									'post_title' => $title,
								);
								$res = wp_update_post( $update_post, true );
								if ( is_wp_error( $res ) ) {
									$errors[] = "Failed to update post for title: {$title}";
									continue;
								}
								$updated++;
								$processed++;
							} else {
								// create new post
								$postarr = array(
									'post_title'   => $title,
									// plugin expects description in post meta 'na_attractions_description', keep post_content empty
									'post_content' => '',
									'post_status'  => 'publish',
									'post_type'    => 'attractions',
								);

								$post_id = wp_insert_post( $postarr );
								if ( is_wp_error( $post_id ) || ! $post_id ) {
									$errors[] = "Failed to create post for title: {$title}";
									continue;
								}

								$created++;
								$processed++;
							}

							// Determine if this row was an update vs create
							$is_update = ( $existing_post && 'update' === $existing_behavior );

							// Save meta fields. For updates, clear meta when CSV provides an empty value.
							if ( isset( $assoc['address'] ) ) {
								$val = trim( $assoc['address'] );
								if ( $val !== '' ) {
									update_post_meta( $post_id, 'na_attractions_address', sanitize_text_field( $val ) );
								} elseif ( $is_update ) {
									delete_post_meta( $post_id, 'na_attractions_address' );
								}
							}

							if ( isset( $assoc['latitude'] ) ) {
								$val = trim( $assoc['latitude'] );
								if ( $val !== '' ) {
									update_post_meta( $post_id, 'na_latitude', sanitize_text_field( $val ) );
								} elseif ( $is_update ) {
									delete_post_meta( $post_id, 'na_latitude' );
								}
							}

							if ( isset( $assoc['longitude'] ) ) {
								$val = trim( $assoc['longitude'] );
								if ( $val !== '' ) {
									update_post_meta( $post_id, 'na_longitude', sanitize_text_field( $val ) );
								} elseif ( $is_update ) {
									delete_post_meta( $post_id, 'na_longitude' );
								}
							}

							if ( isset( $assoc['url'] ) ) {
								$val = trim( $assoc['url'] );
								if ( $val !== '' ) {
									update_post_meta( $post_id, 'na_attractions_url', esc_url_raw( $val ) );
								} elseif ( $is_update ) {
									delete_post_meta( $post_id, 'na_attractions_url' );
								}
							}

							// Save description into the plugin meta key (plugin expects this)
							if ( isset( $assoc['description'] ) ) {
								$val = trim( $assoc['description'] );
								if ( $val !== '' ) {
									update_post_meta( $post_id, 'na_attractions_description', wp_kses_post( $val ) );
								} elseif ( $is_update ) {
									delete_post_meta( $post_id, 'na_attractions_description' );
								}
							}

							// Handle types - comma separated list of attractiontypes taxonomy names
							if ( isset( $assoc['type'] ) ) {
								$raw_type = trim( $assoc['type'] );
								if ( $raw_type === '' ) {
									if ( $is_update ) {
										// clear all attractiontypes for this post
										wp_set_post_terms( $post_id, array(), 'attractiontypes', false );
									}
								} else {
									$type_names = array_map( 'trim', explode( ',', $raw_type ) );
									$type_names = array_filter( $type_names );
									$term_ids = array();

									foreach ( $type_names as $type_name ) {
										// check for existing term by name (not slug)
										$existing = get_term_by( 'name', $type_name, 'attractiontypes' );
										if ( $existing && ! is_wp_error( $existing ) ) {
											$term_ids[] = intval( $existing->term_id );
											continue;
										}

										// create term if it doesn't exist
										$new_term = wp_insert_term( $type_name, 'attractiontypes' );
										if ( ! is_wp_error( $new_term ) && isset( $new_term['term_id'] ) ) {
											$term_ids[] = intval( $new_term['term_id'] );
										}
									}

									if ( ! empty( $term_ids ) ) {
										// assign the terms by ID
										wp_set_post_terms( $post_id, $term_ids, 'attractiontypes', false );
									} elseif ( $is_update ) {
										// if parsed names produced no terms, remove all terms
										wp_set_post_terms( $post_id, array(), 'attractiontypes', false );
									}
								}
							}

							// counts handled above per create/update
						}

						fclose( $handle );

						// Display results
						echo '<div class="notice notice-success"><p>Import complete. Processed: ' . intval( $processed ) . '. Created: ' . intval( $created ) . '. Updated: ' . intval( $updated ) . '. Skipped (duplicates/empty title/invalid): ' . intval( $skipped ) . '.</p></div>';
						if ( ! empty( $errors ) ) {
							echo '<div class="notice notice-warning"><p>Some rows failed:</p><ul>'; 
							foreach ( $errors as $e ) {
								echo '<li>' . esc_html( $e ) . '</li>';
							}
							echo '</ul></div>';
						}
					}
				}
			}
		}
	}

	// Render the imports page UI
	?>
	<div class="wrap">
		<h1>Attractions Imports</h1>

		<p>Use the CSV upload to create attractions in bulk. Download a sample CSV and follow the exact column headers:</p>
		<p>
			<a href="<?php echo esc_url( plugins_url( '../assets/csv/sample-upload.csv', __FILE__ ) ); ?>">Download sample-upload.csv</a>
			&nbsp;|&nbsp;
			<?php
				$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=na_export_attractions' ), 'na_export_attractions' );
				$all_attractions = get_posts( array( 'post_type' => 'attractions', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
				$total_attractions = is_array( $all_attractions ) ? count( $all_attractions ) : 0;
			?>
			<a href="<?php echo esc_url( $export_url ); ?>" onclick="return confirm('Export all attractions to CSV?');">Download all <?php echo intval( $total_attractions ); ?> attractions (.csv)</a>
		</p>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'na_imports_nonce', 'na_imports_nonce_field' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="na_import_csv">CSV file</label></th>
					<td>
						<input type="file" id="na_import_csv" name="na_import_csv" accept="text/csv,text/plain,.csv">
						<p class="description">CSV must include these columns (in this order): title,address,type,url,description,latitude,longitude</p>
					</td>
				</tr>
				<tr>
					<th scope="row">If a title already exists</th>
					<td>
						<?php $existing_behavior = isset( $_POST['na_import_existing'] ) ? $_POST['na_import_existing'] : 'update'; ?>
								<fieldset>
									<label><input type="radio" name="na_import_existing" value="skip" <?php checked( $existing_behavior, 'skip' ); ?>> Skip existing attractions</label><br>
									<label><input type="radio" name="na_import_existing" value="update" <?php checked( $existing_behavior, 'update' ); ?>> Update existing attractions with CSV data (default)</label>
								</fieldset>
						<p class="description">Choose whether rows with titles that exactly match an existing attraction should be skipped or used to update the existing attraction.</p>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="na_import_csv_submit" id="submit" class="button button-primary" value="Upload and import"></p>
		</form>
	</div>
	<?php
}