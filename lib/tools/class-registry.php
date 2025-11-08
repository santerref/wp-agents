<?php

class Wp_Agents_Tools_Registry {

	protected array $tools = array();

	public function __construct( array $tool_names = array() ) {
		$this->add( $tool_names );
	}

	public function add( array|string $tool_names ): void {
		$tool_names = is_string( $tool_names ) ? array( $tool_names ) : $tool_names;

		foreach ( $tool_names as $tool_name ) {
			[ $group, $name ] = explode( '.', $tool_name );
			$tool             = wp_agents_tool_manager()->get( $group, $name );

			if ( ! $tool instanceof WP_Error ) {
				$definition = $tool->definition();

				$this->tools[ $definition['name'] ] = $tool;
			}
		}
	}

	public function definitions(): array {
		$definitions = array();

		foreach ( $this->tools as $tool ) {
			$definitions[] = array(
				'type'     => 'function',
				'function' => $tool->definition(),
			);
		}

		return $definitions;
	}

	public function execute( string $name, array $arguments = array() ): mixed {
		if ( ! isset( $this->tools[ $name ] ) ) {
			return new WP_Error(
				'wp_agents_tool_not_found',
				"The tool with the name {$name} was not found."
			);
		}

		return $this->tools[ $name ]->execute( $arguments );
	}
}
