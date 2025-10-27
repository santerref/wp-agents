<?php

class Wp_Agents_Services_Recursion_Guard {

	protected static array $running = array();

	public static function running( string $key ): bool {
		return isset( self::$running[ $key ] );
	}

	public static function run( string $key, callable $callback ): void {
		if ( ! self::running( $key ) ) {
			self::$running[ $key ] = true;
			try {
				$callback();
			} finally {
				unset( self::$running[ $key ] );
			}
		}
	}
}
