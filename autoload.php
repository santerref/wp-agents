<?php

spl_autoload_register( function ( $class ) {
	$prefix = 'Wp_Agents_';
	$base   = __DIR__ . '/lib/';

	if ( ! str_starts_with( $class, $prefix ) ) {
		return;
	}

	$trim  = substr( $class, strlen( $prefix ) );
	$parts = explode( '_', $trim );

	if ( count( $parts ) < 2 ) {
		return;
	}

	$dirParts  = array_map( 'strtolower', array_slice( $parts, 0, 1 ) );
	$fileParts = array_map( 'strtolower', array_slice( $parts, 1 ) );

	$dir  = implode( '/', $dirParts );
	$file = 'class-' . implode( '-', $fileParts ) . '.php';

	$path = $base . $dir . '/' . $file;

	if ( file_exists( $path ) ) {
		require $path;
	}
} );
