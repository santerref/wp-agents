<?php

class Wp_Agents_Services_Agent_Manager {

	protected static array $agents = array();

	public static function register( array $definition ): void {
		self::$agents[ $definition['name'] ] = new Wp_Agents_Agent_Base( $definition );
	}

	public static function get( string $name ): Wp_Agents_Agent_Abstract|WP_Error {
		if ( ! isset( self::$agents[ $name ] ) ) {
			return new WP_Error(
				'wp_agents_agent_not_found',
				"The agent with the name {$name} was not found."
			);
		}

		return self::$agents[ $name ];
	}


	public static function all(): array {
		return static::$agents;
	}

	public static function boot(): void {
		$agents_directories = array(
			WP_PLUGIN_DIR . '/wp-agents/core/agents',
			WP_PLUGIN_DIR . '/wp-agents/installed/agents',
		);

		foreach ( $agents_directories as $directory ) {
			foreach ( glob( $directory . '/*/*.php' ) as $file ) {
				$agent_name = basename( dirname( $file ) );
				if ( basename( $file, '.php' ) !== $agent_name ) {
					continue;
				}

				$definition = self::read_definition( $file );
				if ( empty( $definition['title'] ) ) {
					continue;
				}

				$instructions = dirname( $file ) . '/instructions.txt';
				if ( ! file_exists( $instructions ) || ! is_readable( $instructions ) ) {
					continue;
				}

				$definition['instructions'] = trim( file_get_contents( $instructions ) );
				$definition['name']         = $agent_name;
				$definition['file']         = $file;
				$definition['directory']    = dirname( $file );

				wp_agents_register( $definition );
			}
		}

		do_action( 'wp_agents_register_agents' );
	}

	protected static function read_definition( string $file ): array {
		$headers = array(
			'Agent Name'  => 'Agent Name',
			'Description' => 'Description',
			'Version'     => 'Version',
			'Tools'       => 'Tools',
			'Hooks'       => 'Hooks',
		);

		$data = get_file_data( $file, $headers, 'agent' );

		foreach ( array( 'Tools', 'Hooks' ) as $list_key ) {
			if ( ! empty( $data[ $list_key ] ) ) {
				$data[ $list_key ] = array_filter(
					array_map(
						'trim',
						explode( ',', $data[ $list_key ] )
					)
				);
			} else {
				$data[ $list_key ] = array();
			}
		}

		return array(
			'title'       => $data['Agent Name'],
			'version'     => $data['Version'],
			'description' => $data['Description'],
			'tools'       => $data['Tools'],
			'hooks'       => $data['Hooks'],
		);
	}
}
