<?php

namespace BricksNextgen;
/**
 * The file that defines the core plugin class
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
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
 * Main plugin class.
 */
class Plugin
{


    /**
     * The unique identifier of this plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function __construct()
    {
        if (defined('BRICKS_NEXTGEN_VERSION')) {
            $this->version = BRICKS_NEXTGEN_VERSION;
        } else {
            $this->version = '0.0.1';
        }
        $this->plugin_name = 'bricks-nextgen';

        $this->load_dependencies();
        $this->set_locale(); 
        $this->set_provider();
        $this->set_query_loop();
        //$this->define_admin_hooks();
        //$this->define_public_hooks();
        //$this->is_nextgen_active();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Bricks_Nextgen_Loader. Orchestrates the hooks of the plugin.
     * - Bricks_Nextgen_i18n. Defines internationalization functionality.
     * - Bricks_Nextgen_Admin. Defines all hooks for the admin area.
     * - Bricks_Nextgen_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.0.1
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bricks-nextgen-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bricks-nextgen-i18n.php';

        /**
         * The class responsible for orchestrating the query loop of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bricks-nextgen-query-loop.php';  
        
        /**
         * The class responsible for orchestrating the providers of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/dynamic-data/class-bricks-nextgen-providers.php';        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/dynamic-data/class-bricks-nextgen-provider-nextgen.php';        
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bricks-nextgen-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bricks-nextgen-public.php';

        $this->loader = new Loader();

    }

    private function set_provider()
    {
        $providers = ['nextgen']; // Replace these with your actual provider keys

        $plugin_providers = new \BricksNextgen\DynamicData\Providers($providers);

        $this->loader->add_action('plugins_loaded', $plugin_providers, 'register');

        // Check if the Base class from Bricks exists.
        if ( class_exists('\Bricks\Integrations\Dynamic_Data\Providers\Base') ) {

            // Register the provider
            //\BricksNextgen\DynamicData\Provider_Nextgen::register($providers);
        }
    }


    private function set_query_loop()
    {
        $plugin_query_loop = new Query_Loop();
        $this->loader->add_action('plugins_loaded', $plugin_query_loop, 'init', 20);
    }
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Bricks_Nextgen_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.0.1
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.0.1
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.0.1
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.0.1
     * @return    Bricks_Nextgen_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.0.1
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}