<?php

class Wp_Agents_Services_Provider_Manager {

	protected array $providers = array();

	public function register( string $name, callable $callback ): void {
		$provider = $callback();
		if ( $provider instanceof Wp_Agents_Providers_Interface ) {
			$this->providers[ $name ] = $provider;
		}
	}

	public function get( string $name ): Wp_Agents_Providers_Interface|WP_Error {
		if ( ! isset( $this->providers[ $name ] ) ) {
			return new WP_Error(
				'wp_agents_provider_not_registered',
				"The requested provider {$name} is not registered."
			);
		}

		return $this->providers[ $name ];
	}

	public function boot(): void {
		do_action( 'wp_agents_register_providers' );
	}
}
