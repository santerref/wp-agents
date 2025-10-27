<?php

class Wp_Agents_Services_Provider_Manager {

	protected static array $providers = array();

	public static function register( string $name, callable $callback ): void {
		$provider = $callback();
		if ( $provider instanceof Wp_Agents_Providers_Interface ) {
			self::$providers[ $name ] = $provider;
		}
	}

	public static function get( string $name ): Wp_Agents_Providers_Interface|WP_Error {
		if ( ! isset( self::$providers[ $name ] ) ) {
			return new WP_Error(
				'wp_agents_provider_not_registered',
				"The requested provider {$name} is not registered."
			);
		}

		return self::$providers[ $name ];
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_providers' );
	}
}
