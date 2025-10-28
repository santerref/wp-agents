<?php
/*
 * Plugin Name:       WP Agents
 * Plugin URI:        https://santerref.com/
 * Description:       Build autonomous, hook-driven agents for WordPress — automate tasks and add LLM intelligence with clean, developer-first architecture.
 * Version:           0.3.0
 * Requires at least: 6.8
 * Requires PHP:      8.4
 * Author:            Francis Santerre
 * Author URI:        https://santerref.com/
 * License:           GPL v2 or later
 * Text Domain:       wp-agents
 * Domain Path:       /languages
 */

require_once 'autoload.php';

if ( ! function_exists( 'wp_agents_install' ) ) {

	function wp_agents_install(): void {
		$schema = new Wp_Agents_System_Schema();
		$schema->install();
	}

	register_activation_hook( __FILE__, 'wp_agents_install' );

}

if ( ! function_exists( 'wp_agents_register' ) ) {

	function wp_agents_register( array $definition ): void {
		Wp_Agents_Services_Agent_Manager::register( $definition );
	}

}

if ( ! function_exists( 'wp_agents_register_provider' ) ) {

	function wp_agents_register_provider( string $name, callable $callback ): void {
		Wp_Agents_Services_Provider_Manager::register( $name, $callback );
	}

	require_once __DIR__ . '/inc/providers.php';

}

if ( ! function_exists( 'wp_agents_get' ) ) {

	function wp_agents_get( string $name ): Wp_Agents_Agent_Abstract|WP_Error {
		return Wp_Agents_Services_Agent_Manager::get( $name );
	}

}

if ( ! function_exists( 'wp_agents_all' ) ) {

	function wp_agents_all(): array {
		return Wp_Agents_Services_Agent_Manager::all();
	}

}

add_action(
	'plugins_loaded',
	function () {
		add_action( 'init', array( Wp_Agents_Services_Provider_Manager::class, 'boot' ) );
		add_action( 'init', array( Wp_Agents_Services_Agent_Manager::class, 'boot' ) );
		add_action( 'rest_api_init', array( Wp_Agents_System_Rest::class, 'register' ) );
	}
);

add_action(
	'plugins_loaded',
	function () {
		foreach ( wp_agents_all() as $agent ) {
			if ( true && $file = $agent->get_file() ) {
				require_once $file;
			}
		}
	},
	20
);
