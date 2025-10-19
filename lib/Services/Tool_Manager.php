<?php

namespace Wp_Agents\Services;

class Tool_Manager {

	public static function build_definitions( array $tools ): array {
		$definitions = array();

		foreach ( $tools as $tool ) {
			if ( ! class_exists( $tool ) ) {
				continue;
			}

			$instance   = new $tool();
			$definition = $instance->definition();

			$definitions[ $definition['name'] ] = array(
				'type'     => 'function',
				'function' => $definition,
			);
		}

		return $definitions;
	}

}
