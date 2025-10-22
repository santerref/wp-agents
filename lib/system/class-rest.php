<?php

namespace Wp_Agents\System;

use Wp_Agents\Services\Agent_Manager;

class Rest {

	public static function register(): void {
		register_rest_route(
			'wp-agents/v1',
			'/chat',
			array(
				'methods'             => 'POST',
				'callback'            => array( self::class, 'handle' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public static function handle( \WP_REST_Request $request ) {
		$agent = Agent_Manager::get( $request->get_param( 'agent' ) );

		$message  = $request->get_param( 'message' );
		$response = $agent
			->prompt( $message )
			->chat();

		return rest_ensure_response(
			array(
				'message'  => $message,
				'response' => $response,
			)
		);
	}
}
