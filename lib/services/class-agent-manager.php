<?php

class Wp_Agents_Services_Agent_Manager {

	protected array $agents = array();

	public function register( array $definition ): void {
		$this->agents[ $definition['id'] ] = new Wp_Agents_Agent_Base( $definition );
	}

	public function get( string $id ): Wp_Agents_Agent_Abstract|WP_Error {
		if ( ! isset( $this->agents[ $id ] ) ) {
			return new WP_Error(
				'wp_agents_agent_not_found',
				"The agent with the name {$id} was not found."
			);
		}

		return $this->agents[ $id ];
	}

	public function all(): array {
		return $this->agents;
	}

	public function activate( string $id ): void {
		$enabled_agents        = $this->enabled();
		$enabled_agents[ $id ] = true;
		$this->update_enabled( $enabled_agents );
	}

	public function deactivate( string $id ): void {
		$enabled_agents        = $this->enabled();
		$enabled_agents[ $id ] = false;
		$this->update_enabled( $enabled_agents );
	}

	public function is_enabled( string $id ): bool {
		$enabled_agents = $this->enabled();

		return isset( $enabled_agents[ $id ] ) && $enabled_agents[ $id ];
	}

	public function boot(): void {
		$agents_directories = array(
			WP_PLUGIN_DIR . '/wp-agents/repository/core/agents',
			WP_PLUGIN_DIR . '/wp-agents/repository/installed/agents',
		);

		foreach ( $agents_directories as $directory ) {
			foreach ( glob( $directory . '/*/*.php' ) as $file ) {
				$agent_name = basename( dirname( $file ) );
				if ( basename( $file, '.php' ) !== $agent_name ) {
					continue;
				}

				$definition = $this->read_definition( $file );
				if ( empty( $definition['name'] ) ) {
					continue;
				}

				$instructions = dirname( $file ) . '/instructions.txt';
				if ( ! file_exists( $instructions ) || ! is_readable( $instructions ) ) {
					continue;
				}
				$instructions_content = file( $instructions, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

				$definition['instructions'] = trim( $instructions_content ? implode( "\n", $instructions_content ) : '' );
				$definition['id']           = $agent_name;
				$definition['file']         = $file;
				$definition['directory']    = dirname( $file );

				wp_agents_register( $definition );
			}
		}

		do_action( 'wp_agents_register_agents' );
	}

	protected function enabled(): array {
		return get_option( 'wp_agents_enabled_agents', array() );
	}

	protected function update_enabled( array $enabled_agents ): void {
		update_option( 'wp_agents_enabled_agents', $enabled_agents );
	}

	protected function read_definition( string $file ): array {
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
			'name'        => $data['Agent Name'],
			'version'     => $data['Version'],
			'description' => $data['Description'],
			'tools'       => $data['Tools'],
			'hooks'       => $data['Hooks'],
		);
	}
}
