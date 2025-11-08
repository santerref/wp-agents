<?php

add_action( 'wp_agents_register_tools', function () {

	wp_agents_register_tool(
		'wordpress',
		array(
			'name'        => 'ensure_category',
			'description' => 'Creates a WordPress category if missing.',
			'parameters'  => [
				'type'       => 'object',
				'properties' => [
					'name' => [ 'type' => 'string' ],
				],
				'required'   => [ 'name' ],
			],
		),
		function ( array $arguments ): mixed {
			$term = get_term_by( 'name', $arguments['name'], 'category', ARRAY_A );
			if ( ! $term ) {
				$term = wp_insert_term( $arguments['name'], 'category' );
			}

			return [ 'term_id' => (int) ( $term['term_id'] ?? 0 ) ];
		}
	);

} );
