<?php

class Wp_Agents_System_Rest {

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
		$agent = Wp_Agents_Services_Agent_Manager::get( $request->get_param( 'agent' ) );

		$message  = $request->get_param( 'message' );
		$response = $agent
			->prompt( $message )
			->with_session( $request->get_param( 'session_id' ) )
			->chat();

		return rest_ensure_response(
			array(
				'agent'    => $request->get_param( 'agent' ),
				'message'  => $message,
				'response' => $response->to_array(),
			)
		);
	}
}
