<?php
/*
	Plugin Name: Neighborhood Attractions
	Plugin URI: https://elod.in
    Description: Just another attractions map plugin
	Version: 0.1
    Author: Jon Schroeder
    Author URI: https://elod.in

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
*/

/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}

// Plugin directory
define( 'NEIGHBORHOOD_ATTRACTIONS', dirname( __FILE__ ) );

// Define the version of the plugin
define( 'NEIGHBORHOOD_ATTRACTIONS_VERSION', '0.1' );
define( 'NEIGHBORHOOD_ATTRACTIONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'NEIGHBORHOOD_ATTRACTIONS_DIR', dirname( __FILE__ ) );

//* Initialize CMB2
require_once NEIGHBORHOOD_ATTRACTIONS_DIR . '/vendor/cmb2/init.php';

//* Include everything in /lib
foreach ( glob( NEIGHBORHOOD_ATTRACTIONS_DIR . "/inc/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}
/////////////
// Updater //
/////////////

require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/neighborhood-attractions',
	__FILE__,
	'neighborhood-attractions'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

