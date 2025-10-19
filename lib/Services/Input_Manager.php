<?php

namespace Wp_Agents\Services;

use Wp_Agents\Exceptions\Input_Not_Registered_Exception;
use Wp_Agents\Inputs\Abstract_Input;

class Input_Manager {

	protected static array $inputs = array();

	public static function register( string $name, string $input_class ): void {
		self::$inputs[ $name ] = $input_class;
	}

	public static function make( string $name ): Abstract_Input {
		if ( ! isset( self::$inputs[ $name ] ) ) {
			throw new Input_Not_Registered_Exception();
		}

		return new self::$inputs[ $name ]();
	}

	public static function boot(): void {
		do_action( 'wp_agents_register_inputs' );
	}
}
