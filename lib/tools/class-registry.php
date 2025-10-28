<?php

class Wp_Agents_Tools_Registry {

	protected array $tools = array();

	public function __construct( array $tool_classes = array() ) {
		$this->add( $tool_classes );
	}

	public function add( array|string $tool_classes ): void {
		$tool_classes = is_string( $tool_classes ) ? array( $tool_classes ) : $tool_classes;

		foreach ( $tool_classes as $tool_class ) {
			if ( ! class_exists( $tool_class ) ) {
				continue;
			}

			$tool       = new $tool_class();
			$definition = $tool->definition();

			$this->tools[ $definition['name'] ] = $tool;
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
