<?php

spl_autoload_register( function ( $class ) {
	$prefix = 'Wp_Agents_';
	$base   = __DIR__ . '/lib/';

	if ( strpos( $class, $prefix ) !== 0 ) {
		return;
	}

	$trim  = substr( $class, strlen( $prefix ) );
	$parts = explode( '_', $trim );

	$className = array_pop( $parts );
	$dir       = strtolower( implode( '/', $parts ) );
	$file      = 'class-' . strtolower( str_replace( '_', '-', $className ) ) . '.php';

	$path = rtrim( $base, '/' ) . '/' . $dir . '/' . $file;

	if ( file_exists( $path ) ) {
		require $path;
	}
} );
