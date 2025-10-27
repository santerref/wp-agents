<?php

class Wp_Agents_Services_Agent_Manager {

	protected static array $agents = array();

	public static function register( string $name, string $agent_class ): void {
		self::$agents[ $name ] = new $agent_class( $name );
	}

	public static function get( string $name ): Wp_Agents_Llm_Abstract|WP_Error {
		if ( ! isset( self::$agents[ $name ] ) ) {
			return new WP_Error(
				'wp_agents_agent_not_found',
				"The agent with the name {$name} was not found."
			);
		}

		return self::$agents[ $name ];
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_agents' );
	}
}
