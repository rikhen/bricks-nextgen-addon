<?php

namespace BricksNextgen;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://webshore.io
 * @since      0.0.1
 *
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 * @author     H. Liebel <mail@henrikliebel.com>
 */
class i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bricks-nextgen',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
