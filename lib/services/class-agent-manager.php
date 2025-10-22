<?php

namespace Wp_Agents\Services;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\Exceptions\Agent_Not_Found_Exception;

class Agent_Manager {

	protected static array $agents = array();

	public static function register( string $name, string $agent_class ): void {
		self::$agents[ $name ] = new $agent_class();
	}

	public static function get( string $name ): Abstract_Llm_Agent {
		if ( ! isset( self::$agents[ $name ] ) ) {
			throw new Agent_Not_Found_Exception();
		}

		return self::$agents[ $name ];
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_agents' );
	}
}
