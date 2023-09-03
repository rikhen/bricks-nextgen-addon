<?php

namespace BricksNextgen\DynamicData;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Provider_Nextgen extends \Bricks\Integrations\Dynamic_Data\Providers\Base {

	public function register_tags() {
		$tags = $this->get_tags_config();

		foreach ( $tags as $key => $tag ) {
			$this->tags[ $key ] = [
				'name'     => '{' . $key . '}',
				'label'    => $tag['label'],
				'group'    => $tag['group'],
				'provider' => $this->name,
			];

			if ( ! empty( $tag['deprecated'] ) ) {
				$this->tags[ $key ]['deprecated'] = $tag['deprecated'];
			}

			if ( ! empty( $tag['render'] ) ) {
				$this->tags[ $key ]['render'] = $tag['render'];
			}
		}
	}

    public function get_tags_config() {

		$nextgen_group = esc_html__( 'NextGEN', 'bricks' );

		$tags = [
            // Nextgen Image
			'nextgen_image' => [
				'label' => esc_html__( 'NextGEN Image', 'bricks' ),
				'group' => $nextgen_group,
			],
			// Album
			'nextgen_album' => [
				'label' => esc_html__( 'NextGEN Album', 'bricks' ),
				'group' => $nextgen_group,
			],
        ];

        return $tags;
    }

	public function get_tag_value( $tag, $post, $args, $context ) {

		$post_id = isset( $post->ID ) ? $post->ID : '';

		// STEP: Check for filter args
		$filters = $this->get_filters_from_args( $args );

		// STEP: Get the value
		$value = '';

		$render = isset( $this->tags[ $tag ]['render'] ) ? $this->tags[ $tag ]['render'] : $tag;

        switch ( $render ) {
            case 'nextgen_image':
                $filters['object_type'] = 'media';
				$filters['image']       = 'true';

                // Loop
				if ( method_exists( '\Bricks\Query', 'get_query_object' ) ) {
					if ( \Bricks\Query::is_looping() && \Bricks\Query::get_loop_object_type() == 'term' ) {
						$term_id = \Bricks\Query::get_loop_object_id();
					}
				}
                // Logic to retrieve the NextGEN gallery data
                // For example, you can fetch the gallery images based on the post ID or any other criteria
                $value = "Your logic to fetch NextGEN gallery data";
                break;

            // Add more cases if you have other render types
        }

        // STEP: Apply context (text, link, image, media)
		$value = $this->format_value_for_context( $value, $tag, $post_id, $filters, $context );

		return $value;
    }

}