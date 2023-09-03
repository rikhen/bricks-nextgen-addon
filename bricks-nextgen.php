<?php

/*
 * Plugin Name:       Bricks NextGEN Gallery Addon
 * Plugin URI:        https://wpturbo.dev
 * Description:       A Bricks Builder Addon for the NextGEN Gallery Plugin
 * Version:           0.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            H.Liebel
 * Author URI:        https://webshore.io
 * Text Domain:       webshr
*/

namespace BricksNextgen;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.0.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BRICKS_NEXTGEN_VERSION', '0.0.1' );

// Load plugin class files.
require_once 'includes/class-bricks-nextgen.php';

/**
 * Returns the main instance of WordPress_Plugin_Template to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WordPress_Plugin_Template
 */
function run_bricks_nextgen() {
	
	$plugin = new Plugin();
	$plugin->run();

}

run_bricks_nextgen();