<?php

spl_autoload_register( function ( $class ) {
	$prefix   = 'Wp_Agents\\';
	$base_dir = __DIR__ . '/lib/';

	if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
		return;
	}

	$relative = substr( $class, strlen( $prefix ) );
	$relative = str_replace( '\\', '/', $relative );

	$parts     = explode( '/', $relative );
	$className = array_pop( $parts );
	$filename  = 'class-' . strtolower( str_replace( '_', '-', $className ) ) . '.php';

	$path = $base_dir . strtolower( implode( '/', $parts ) ) . '/' . $filename;

	if ( file_exists( $path ) ) {
		require $path;
	}
} );
