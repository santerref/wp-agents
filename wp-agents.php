<?php
/*
 * Plugin Name:       WP Agents
 * Plugin URI:        https://santerref.com/
 * Description:       Build autonomous, hook-driven agents for WordPress — automate tasks and add LLM intelligence with clean, developer-first architecture.
 * Version:           0.3.1
 * Requires at least: 6.8
 * Requires PHP:      8.4
 * Author:            Francis Santerre
 * Author URI:        https://santerref.com/
 * License:           GPL v2 or later
 * Text Domain:       wp-agents
 * Domain Path:       /languages
 */

if ( ! defined( 'WP_AGENTS_PLUGIN_DIR' ) ) {
	define( 'WP_AGENTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_AGENTS_PLUGIN_URL' ) ) {
	define( 'WP_AGENTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require_once 'autoload.php';
require_once 'inc/assets.php';
require_once 'inc/admin.php';
require_once 'inc/services.php';

if ( ! function_exists( 'wp_agents_install' ) ) {

	function wp_agents_install(): void {
		$schema = new Wp_Agents_System_Schema();
		$schema->install();
	}

	register_activation_hook( __FILE__, 'wp_agents_install' );

}

if ( ! function_exists( 'wp_agents_register' ) ) {

	function wp_agents_register( array $definition ): void {
		wp_agents_agent_manager()->register( $definition );
	}

}

if ( ! function_exists( 'wp_agents_register_provider' ) ) {

	function wp_agents_register_provider( string $id, callable $callback ): void {
		wp_agents_provider_manager()->register( $id, $callback );
	}

	require_once __DIR__ . '/inc/providers.php';

}

if ( ! function_exists( 'wp_agents_get' ) ) {

	function wp_agents_get( string $name ): Wp_Agents_Agent_Abstract|WP_Error {
		return wp_agents_agent_manager()->get( $name );
	}

}

if ( ! function_exists( 'wp_agents_all' ) ) {

	function wp_agents_all(): array {
		return wp_agents_agent_manager()->all();
	}

}

if ( ! function_exists( 'wp_agents_register_tool' ) ) {

	function wp_agents_register_tool( string $group, array $definition, callable $callback ): void {
		wp_agents_tool_manager()->register(
			$group,
			$definition,
			$callback
		);
	}

}

add_action(
	'plugins_loaded',
	function () {
		wp_agents_provider_manager()->boot();
		wp_agents_tool_manager()->boot();
		wp_agents_agent_manager()->boot();
		add_action( 'rest_api_init', array( wp_agents_rest(), 'register' ) );
	}
);

add_action(
	'plugins_loaded',
	function () {
		foreach ( wp_agents_all() as $agent ) {
			$file = $agent->get_file();

			if ( $file && $agent->is_enabled() ) {
				require_once $file;
			}
		}
	},
	20
);
