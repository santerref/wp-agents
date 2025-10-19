<?php

namespace Wp_Agents\Tools;

use Wp_Agents\Exceptions\Tool_Not_Found_Exception;

class Tool_Registry {

	protected array $tools = array();

	public function __construct( array $tool_classes = array() ) {
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
			throw new Tool_Not_Found_Exception();
		}

		return $this->tools[ $name ]->execute( $arguments );
	}
}
