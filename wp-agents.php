<?php
/*
 * Plugin Name:       WP Agent Framework
 * Plugin URI:        https://santerref.com/
 * Description:       Build autonomous, hook-driven agents for WordPress — automate tasks and add LLM intelligence with clean, developer-first architecture.
 * Version:           0.0.1
 * Requires at least: 6.8
 * Requires PHP:      8.4
 * Author:            Francis Santerre
 * Author URI:        https://santerref.com/
 * License:           GPL v2 or later
 * Text Domain:       wp-agents
 * Domain Path:       /languages
 */

add_action(
	'plugins_loaded',
	function () {
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';

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

				function wp_agents_register( string $name, string $agent_class, array $actions = array(), $filters = array() ) {
					\Wp_Agents\Services\Agent_Manager::register( $name, new $agent_class( $actions, $filters ) );
				}

			}

			if ( ! function_exists( 'wp_agents_register_input' ) ) {

				function wp_agents_register_input( string $name, string $input_class ) {
					\Wp_Agents\Services\Input_Manager::register( $name, $input_class );
				}

				require_once __DIR__ . '/inputs.php';

			}

			if ( ! function_exists( 'wp_agents_register_provider' ) ) {

				function wp_agents_register_provider( string $name, callable $callback ) {
					\Wp_Agents\Services\Provider_Manager::register( $name, $callback );
				}

				require_once __DIR__ . '/providers.php';

			}

			add_action( 'init', array( \Wp_Agents\Services\Provider_Manager::class, 'boot' ) );
			add_action( 'init', array( \Wp_Agents\Services\Input_Manager::class, 'boot' ) );
			add_action( 'init', array( \Wp_Agents\Services\Agent_Manager::class, 'boot' ) );
		}
	}
);
