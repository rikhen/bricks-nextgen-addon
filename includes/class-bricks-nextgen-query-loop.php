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

		add_action( 'init', [ $this, 'add_nextgen_query_loop_controls' ], 40 );
		add_filter( 'bricks/setup/control_options', [ $this, 'setup_nextgen_query_loop_types' ]);
		add_filter( 'bricks/query/run', [ $this, 'run_nextgen_query' ], 10, 2 );
		add_filter( 'bricks/query/loop_object', [ $this, 'setup_nextgen_query_post_data' ], 10, 3);

	}

    function add_nextgen_query_loop_controls() {

		$elements = [ 
			'container', 
			'block', 
			'div'
		];
	
		foreach ( $elements as $element ) {
			add_filter( "bricks/elements/{$element}/controls", [ $this, 'nextgen_query_loop_controls' ], 40 );
		}

	
	}

    public function nextgen_query_loop_controls($controls)
    {
        do_action( 'qm/info', 'Hello from the nextgen_query_loop_controls() function!' );
        global $nggdb;

        /*
        $taxonomies = \Bricks\Setup::$control_options['taxonomies'];
          unset( $taxonomies['Wp_template_part'] );
        */

        $newControls['nextgenQuery'] = [
            'tab' => 'content',
            'label' => esc_html__('Query type', 'bricks'),
            'type' => 'select',
            'inline' => true,
            'options' => [
                'nextgenAlbum' => 'Album',
                'nextgenGallery' => 'Gallery'
            ],
            'placeholder' => esc_html__('Select...', 'bricks'),
            'required' => array(
                ['query.objectType', '=', 'queryLoopNextgen'],
                ['hasLoop', '!=', false]
            ),
            'rerender' => true,
            'multiple' => false,
        ];

        $newControls['nextgenAlbum'] = [
            'tab' => 'content',
            'label' => esc_html__('Album', 'bricks'),
            'type' => 'select',
            'options' => [],
            'placeholder' => esc_html__('Select Album', 'bricks'),
            'required' => array(
                ['nextgenQuery', '=', 'nextgenAlbum'],
                ['query.objectType', '=', 'queryLoopNextgen'],
                ['hasLoop', '!=', false]
            ),
            'rerender' => true,
            'description' => esc_html__('Please ensure you have created an album', 'bricks'),
            'searchable' => true,
            'multiple' => false,
        ];

        $newControls['nextgenGallery'] = [
            'tab' => 'content',
            'label' => esc_html__('Gallery', 'bricks'),
            'type' => 'select',
            'options' => [],
            'placeholder' => esc_html__('Select Gallery', 'bricks'),
            'required' => array(
                ['nextgenQuery', '=', 'nextgenGallery'],
                ['query.objectType', '=', 'queryLoopNextgen'],
                ['hasLoop', '!=', false]
            ),
            'rerender' => true,
            'description' => esc_html__('Please ensure you have created a gallery', 'bricks'),
            'searchable' => true,
            'multiple' => false,
        ];



        // Fetch albums from NextGEN Gallery
/*         $albums = $nggdb->find_all_albums();
        if (!$albums) {
            return $controls;
        }

        foreach ($albums as $album) {
            $newControls['nextgenAlbum']['options'][$album->id] = $album->name;
        }
        */
        // Fetch galleries from NextGEN Gallery
        $galleries = $nggdb->find_all_galleries();
        if ($galleries) {
            foreach ($galleries as $gallery) {
                $newControls['nextgenGallery']['options'][$gallery->gid] = $gallery->title;
            }
        }

        $query_key_index = absint( array_search( 'query', array_keys( $controls ) ) );
        $new_controls    = array_slice( $controls, 0, $query_key_index + 1, true ) + $newControls + array_slice( $controls, $query_key_index + 1, null, true );

        return $new_controls;

    }

    function setup_nextgen_query_loop_types( $control_options ) {
        do_action( 'qm/info', 'Hello from the setup_nextgen_query_loop_types() function!' );
        $control_options['queryTypes']['queryLoopNextgen'] = esc_html__('NextGEN', 'bricks');
		return $control_options;
	
	}

    public function run_nextgen_query($results, $query_obj) {
        do_action( 'qm/info', 'Hello from the run_nextgen_query() function!' );

        if ( $query_obj->object_type !== 'queryLoopNextgen' ) {
			return $results;
		}

        $settings = $query_obj->settings;
	
		if ( ! $settings['hasLoop'] ) {
			return [];
		}
        
		$nextgenQuery = isset( $settings['nextgenQuery'] ) ? $settings['nextgenQuery'] : false;

        if ('nextgenAlbum' === $nextgenQuery) {
            do_action( 'qm/info',  'NextGEN Album selected!' );
            //NextGEN album
            $nextgenAlbum = isset( $settings['nextgenAlbum'] ) ? $settings['nextgenAlbum'] : false;

        } elseif ( 'nextgenGallery' === $nextgenQuery ) {
            do_action( 'qm/info',  'NextGEN Gallery selected!' );
            global $nggdb;

            // Get the gallery ID from the query settings
            $gallery_id = $query_obj->settings['nextgenGallery'];

            // Fetch the images from the specified gallery
            $images = $nggdb->get_gallery($gallery_id);

            if(!$images) {
                do_action( 'qm/debug', 'Gallery has no images!' );
                return;
            }

        }
        // Check if the query is for the NextGEN Gallery
        if ($query_obj->object_type === 'queryLoopNextgen') {

    
            // Convert the images to the format expected by Bricks
            foreach ($images as $image) {
                $results[] = (object) [
                    'thumbnail_id' => $image->pid,
                    'post_title' => $image->alttext,
                    'post_excerpt' => $image->description,
                    'post_date' => $image->imagedate,
                    'image_url' => $image->imageURL,
                    // Add any other properties you need
                ];
            }
        }
        do_action( 'qm/debug',  $results );
        return $results;
    }

    public function setup_nextgen_query_post_data($loop_object, $loop_key, $query_obj) {
        do_action( 'qm/info', 'Hello from the setup_nextgen_query_post_data() function!' );
        // Check for the custom parameter in the query
        if ( $query_obj->object_type !== 'nextgenQueryLoop' ) {
			return $loop_object;
		}

        // Convert the loop object to the format expected by Bricks
        $loop_object = (object) [
            'thumbnail_id' => $loop_object->ID,
            'post_title' => $loop_object->post_title,
            'post_excerpt' => $loop_object->post_excerpt,
            'post_date' => $loop_object->post_date,
            'image_url' => $loop_object->image_url,
            // Add any other properties you need
        ];

    
        return $loop_object;
    }
}