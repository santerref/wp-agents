<?php

class Wp_Agents_Services_Tool_Manager {

	protected array $tools = array();

	public function register( string $group, array $definition, callable $callback ): void {
		$this->tools[ $group . '.' . $definition['name'] ] = new Wp_Agents_Tools_Base(
			$definition,
			$callback
		);
	}

	public function get( string $group, string $name ): Wp_Agents_Tools_Base|WP_Error {
		if ( ! isset( $this->tools[ $group . '.' . $name ] ) ) {
			return new WP_Error(
				'wp_agents_tool_not_found',
				"The tool with the name {$name} in group {$group} was not found."
			);
		}

		return $this->tools[ $group . '.' . $name ];
	}

	public function boot(): void {
		$tools_directories = array(
			WP_PLUGIN_DIR . '/wp-agents/repository/core/tools',
			WP_PLUGIN_DIR . '/wp-agents/repository/installed/tools',
		);

		foreach ( $tools_directories as $directory ) {
			foreach ( glob( $directory . '/*.php' ) as $file ) {
				require_once $file;
			}
		}

		do_action( 'wp_agents_register_tools' );
	}
}
