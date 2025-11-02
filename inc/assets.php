<?php

if ( ! function_exists( 'wp_agents_manifest' ) ) {

	function wp_agents_vite_manifest(): array {
		static $manifest;

		if ( isset( $manifest ) ) {
			return $manifest;
		}

		$file = WP_AGENTS_PLUGIN_DIR . 'dist/.vite/manifest.json';
		if ( ! file_exists( $file ) ) {
			return [];
		}

		$checksum   = md5_file( $file );
		$cache      = get_option( 'wp_agents_vite_manifest_cache' );
		$cache_hash = get_option( 'wp_agents_vite_manifest_hash' );

		if ( $cache && $cache_hash === $checksum ) {
			$manifest = $cache;

			return $manifest;
		}

		ob_start();
		readfile( $file );
		$json = ob_get_clean();
		$data = json_decode( $json, true );

		update_option( 'vite_manifest_cache', $data, false );
		update_option( 'vite_manifest_hash', $checksum, false );

		$manifest = $data;

		return $data;
	}

}

if ( ! function_exists( 'wp_agents_vite_enqueue' ) ) {

	function wp_agents_vite_enqueue( $entry ): void {
		$hot_file = WP_AGENTS_PLUGIN_DIR . '.hot';

		if ( file_exists( $hot_file ) ) {
			ob_start();
			readfile( $hot_file );
			$dev = trim( ob_get_clean() );

			wp_enqueue_script_module( 'vite-client', $dev . '/@vite/client', array(), null );
			wp_enqueue_script_module( 'vite-entry', $dev . '/' . $entry, array(), null );

			return;
		}

		$manifest = wp_agents_vite_manifest();
		if ( ! isset( $manifest[ $entry ] ) ) {
			return;
		}

		$plugin_data = get_plugin_data( WP_AGENTS_PLUGIN_DIR . 'wp-agents.php' );

		$asset = $manifest[ $entry ];
		$base  = WP_AGENTS_PLUGIN_URL . 'dist/';

		if ( ! empty( $asset['css'] ) ) {
			foreach ( $asset['css'] as $css ) {
				wp_enqueue_style( 'wp-agents-vite-' . md5( $css ), $base . $css, array(), $plugin_data['Version'] );
			}
		}

		wp_enqueue_script( 'wp-agents-vite-' . md5( $asset['file'] ), $base . $asset['file'], array(), $plugin_data['Version'], true );
	}

}
