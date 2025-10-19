<?php

namespace Wp_Agents\Services;


use Wp_Agents\Exceptions\Provider_Not_Registered_Exception;
use Wp_Agents\Providers\Provider_Interface;

class Provider_Manager {

	protected static array $providers = [];

	public static function register( string $name, callable $callback ): void {
		self::$providers[ $name ] = $callback();
	}

	public static function get( string $name ): Provider_Interface {
		if ( ! isset( self::$providers[ $name ] ) ) {
			throw new Provider_Not_Registered_Exception();
		}

		return self::$providers[ $name ];
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_providers' );
	}

}
