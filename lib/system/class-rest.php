<?php

class Wp_Agents_System_Rest {

	public function register(): void {
		register_rest_route(
			'wp-agents/v1',
			'/chat',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'chat' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wp-agents/v1',
			'/agents',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'agents' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wp-agents/v1',
			'/agents/(?P<id>[a-zA-Z0-9_-]+)/activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'activate' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wp-agents/v1',
			'/agents/(?P<id>[a-zA-Z0-9_-]+)/deactivate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'deactivate' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function chat( \WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$agent = wp_agents_agent_manager()->get( $request->get_param( 'agent' ) );

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

	public function agents(): WP_REST_Response|WP_Error {
		$agents = array();

		foreach ( wp_agents_all() as $agent ) {
			$agents[] = $agent->to_array();
		}

		return rest_ensure_response( $agents );
	}

	public function activate( \WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$id = $request->get_param( 'id' );
		wp_agents_agent_manager()->activate( $id );

		return rest_ensure_response( array( 'enabled' => true ) );
	}

	public function deactivate( \WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$id = $request->get_param( 'id' );
		wp_agents_agent_manager()->deactivate( $id );

		return rest_ensure_response( array( 'enabled' => false ) );
	}
}
