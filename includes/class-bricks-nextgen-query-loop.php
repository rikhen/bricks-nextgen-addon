<?php

namespace BricksNextgen;
/**
 * Register all dynamic data providers for the plugin
 *
 * @link       https://webshore.io
 * @since      0.0.1
 *
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 */

/**
 * Register all dynamic data providers for the plugin.
 *
 * Maintain a list of all providers that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Bricks_Nextgen
 * @subpackage Bricks_Nextgen/includes
 * @author     H. Liebel <mail@henrikliebel.com>
 */
class Query_Loop {

    public function init() {

		add_action( 'init', [ $this, 'add_nextgen_query_controls' ], 40 );
		add_filter( 'bricks/setup/control_options', [ $this, 'setup_nextgen_query_types' ]);
		add_filter( 'bricks/query/run', [ $this, 'run_nextgen_gallery_query' ], 10, 2 );
		add_filter( 'bricks/query/loop_object', [ $this, 'setup_nextgen_post_data' ], 10, 3);

	}

    function add_nextgen_query_controls() {

		$elements = [ 
			'container', 
			'block', 
			'div'
		];
	
		foreach ( $elements as $element ) {
			add_filter( "bricks/elements/{$element}/controls", [ $this, 'nextgen_query_controls' ], 40 );
		}

	
	}

    public function nextgen_query_controls($controls)
    {
        global $nggdb;

        // Fetch galleries from NextGEN Gallery
        $galleries = $nggdb->find_all_galleries();
        if (!$galleries) {
            return $controls;
        }

        /*
        $taxonomies = \Bricks\Setup::$control_options['taxonomies'];
          unset( $taxonomies['Wp_template_part'] );
        */

        $newControls['nextgen_gallery_query'] = [
            'tab' => 'content',
            'label' => esc_html__('NextGEN Galleries', 'bricks'),
            'type' => 'select',
            'options' => [],
            'placeholder' => esc_html__('Choose a gallery', 'bricks'),
            'required' => array(
                ['query.objectType', '=', 'nextgen_gallery_query'],
                ['hasLoop', '!=', false]
            ),
            'rerender' => true,
            'description' => esc_html__('Please ensure NextGEN Gallery is active', 'bricks'),
            'searchable' => true,
            'multiple' => false,
        ];


        foreach ($galleries as $gallery) {
            $newControls['nextgen_gallery_query']['options'][$gallery->gid] = $gallery->title;
        }


        $query_key_index = absint(array_search('query', array_keys($controls)));
        $new_controls = array_slice($controls, 0, $query_key_index + 1, true) + $newControls + array_slice($controls, $query_key_index + 1, null, true);

        return $new_controls;
    }

    function setup_nextgen_query_types( $control_options ) {

        $control_options['queryTypes']['nextgen_gallery_query'] = esc_html__('NextGEN Galleries');
        $control_options['queryTypes']['nextgen_album_query'] = esc_html__('NextGEN Albums');
		return $control_options;
	
	}

    public function run_nextgen_gallery_query($results, $query_obj) {
        do_action( 'qm/info', 'Query run filter running!' );
        global $nggdb;

        // Check if the query is for the NextGEN Gallery
        if ($query_obj->object_type === 'nextgen_gallery_query') {
            // Get the gallery ID from the query settings
            $gallery_id = $query_obj->settings['nextgen_queried_gallery_id'];
    
            // Fetch the images from the specified gallery
            $images = $nggdb->get_gallery($gallery_id);
    
            // Convert the images to the format expected by Bricks
            foreach ($images as $image) {
                $results[] = (object) [
                    'ID' => $image->pid,
                    'post_title' => $image->alttext,
                    'post_excerpt' => $image->description,
                    'post_date' => $image->imagedate,
                    'imageURL' => $image->imageURL,
                    // Add any other properties you need
                ];
            }
        }
        return $results;
    }

    public function setup_nextgen_post_data($loop_object, $loop_key, $query_obj) {
        do_action( 'qm/info', 'Loop object filter running!' );
        // Check for the custom parameter in the query
        if (isset($query_obj->nextgen_gallery_query) && $query_obj->nextgen_gallery_query) {
            // Convert the loop object to the format expected by Bricks
            $loop_object = (object) [
                'ID' => $loop_object->ID,
                'post_title' => $loop_object->post_title,
                'post_excerpt' => $loop_object->post_excerpt,
                'post_date' => $loop_object->post_date,
                // Add any other properties you need
            ];
        }
    
        return $loop_object;
    }
}