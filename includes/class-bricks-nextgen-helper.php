<?php

namespace BricksNextgen;
/**
 * The file that defines the helper plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webshore.io
 * @since      0.0.1
 *
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 */

/**
 * The helper plugin class.
 *
 * This is used to define additional helpers.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 * @author     H. Liebel <mail@henrikliebel.com>
 */

/**
 * Helper plugin class.
 */
class Helper
{

    /**
     * Check if the NextGEN Gallery plugin is active.
     *
     * @since    0.0.1
     * @access   private
     * @return   bool   True if NextGEN Gallery is active, false otherwise.
     */
    private function is_nextgen_active()
    {
        // Ensure the is_plugin_active() function is available.
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        // Check if NextGEN Gallery is active.
        return is_plugin_active('nextgen-gallery/nggallery.php'); // Adjust the path if it's different.
    }
}