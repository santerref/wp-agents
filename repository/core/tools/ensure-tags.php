<?php

add_action( 'wp_agents_register_tools', function () {

	wp_agents_register_tool(
		'wordpress',
		array(
			'name'        => 'ensure_tags',
			'description' => 'Creates WordPress tags if missing.',
			'parameters'  => [
				'type'       => 'object',
				'properties' => [
					'names' => [
						'type'  => 'array',
						'items' => [ 'type' => 'string' ],
					],
				],
				'required'   => [ 'names' ],
			],
		),
		function ( array $arguments ): mixed {
			$ids = [];

			foreach ( $arguments['names'] as $name ) {
				$term = get_term_by( 'name', $name, 'post_tag', ARRAY_A );
				if ( ! $term ) {
					$term = wp_insert_term( $name, 'post_tag' );
				}

				if ( is_array( $term ) && isset( $term['term_id'] ) ) {
					$ids[] = (int) $term['term_id'];
				} elseif ( $term instanceof \WP_Term ) {
					$ids[] = (int) $term->term_id;
				}
			}

			return [ 'term_ids' => $ids ];
		}
	);

} );
