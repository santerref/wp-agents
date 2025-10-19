<?php

namespace Wp_Agents\Services;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\Exceptions\Skip_Agent_Exception;

class Agent_Manager {

	protected static array $agents = array();

	public static function register( string $name, Abstract_Llm_Agent $agent ): void {
		self::$agents[ $name ] = $agent;

		foreach ( $agent->actions() as $action ) {
			$input = Input_Manager::make( $action );

			add_action(
				$action,
				function ( ...$args ) use ( $agent, $input, $name, $action ) {
					Recursion_Guard::run(
						"{$name}:{$action}",
						function () use ( $input, $args, $agent ) {
							try {
								$built_input = $input->build( ...$args );
								if ( ! empty( $built_input ) ) {
									$provider = Provider_Manager::get( $agent->get_provider() );
									$answer   = $agent->run( $built_input, $provider );
									$agent->handle_response( $answer, $args );
								}
							} catch ( Skip_Agent_Exception $e ) {
							}
						}
					);
				},
				$input->get_priority(),
				$input->get_accepted_args()
			);

		}
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_agents' );
	}
}
