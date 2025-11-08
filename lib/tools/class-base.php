<?php

class Wp_Agents_Tools_Base implements Wp_Agents_Tools_Interface {

	protected array $definition = array();

	protected $callback;

	public function __construct( array $definition, callable $callback ) {
		$this->definition = $definition;
		$this->callback   = $callback;
	}

	public function definition(): array {
		return $this->definition;
	}

	public function execute( array $arguments ): mixed {
		return ( $this->callback )( $arguments );
	}
}
