<?php

if ( ! function_exists( 'wp_agents_admin_page' ) ) {

	add_action( 'admin_menu', function () {
		add_menu_page( 'WP Agents', 'WP Agents', 'manage_options', 'wp-agents', 'wp_agents_admin_page' );
	} );

	function wp_agents_admin_page(): void {
		echo '<div id="wp-agents-app"></div>';
	}

	add_action( 'admin_enqueue_scripts', function ( $hook ) {
		if ( $hook !== 'toplevel_page_wp-agents' ) {
			return;
		}
		wp_agents_vite_enqueue( 'src/main.ts' );
	} );

}
