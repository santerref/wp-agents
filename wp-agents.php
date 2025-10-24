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

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';

	if ( ! function_exists( 'wp_agents_install' ) ) {

		function wp_agents_install() {
			$schema = new \Wp_Agents\System\Schema();
			$schema->install();
		}

		register_activation_hook( __FILE__, 'wp_agents_install' );

	}

	add_action(
		'plugins_loaded',
		function () {
			if ( ! function_exists( 'wp_agents_logger' ) ) {

				function wp_agents_logger() {
					static $logger;

					if ( ! isset( $logger ) ) {
						$logger = new \Wp_Agents\Services\Logger( __DIR__ );
					}

					return $logger;
				}

			}

			if ( ! function_exists( 'wp_agents_register' ) ) {

				function wp_agents_register( string $name, string $agent_class ) {
					\Wp_Agents\Services\Agent_Manager::register( $name, $agent_class );
				}

			}

			if ( ! function_exists( 'wp_agents_register_provider' ) ) {

				function wp_agents_register_provider( string $name, callable $callback ) {
					\Wp_Agents\Services\Provider_Manager::register( $name, $callback );
				}

				require_once __DIR__ . '/inc/providers.php';

			}

			if ( ! function_exists( 'wp_agents_get' ) ) {

				function wp_agents_get( string $name ) {
					return \Wp_Agents\Services\Agent_Manager::get( $name );
				}

			}

			add_action( 'init', array( \Wp_Agents\Services\Provider_Manager::class, 'boot' ) );
			add_action( 'init', array( \Wp_Agents\Services\Agent_Manager::class, 'boot' ) );
			add_action( 'rest_api_init', array( \Wp_Agents\System\Rest::class, 'register' ) );
		}
	);

}
