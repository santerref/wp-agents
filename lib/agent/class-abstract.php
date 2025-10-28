<?php

abstract class Wp_Agents_Agent_Abstract {

	protected array $definition = array(
		'model'        => 'gpt-4o-mini',
		'provider'     => 'openai',
		'json'         => false,
		'tools'        => array(),
		'memory'       => null,
		'memory_limit' => null,
		'instructions' => '',
		'name'         => '',
		'version'      => '',
		'description'  => '',
		'title'        => '',
		'hooks'        => array(),
		'file'         => null,
		'dir'          => null
	);

	public function __construct( array $definition ) {
		$this->definition = array_merge(
			$this->definition,
			$definition
		);
	}

	public function instructions(): string {
		return $this->definition['instructions'];
	}

	public function prompt( mixed $input ): Wp_Agents_System_Agent_Runner {
		return new Wp_Agents_System_Agent_Runner(
			$input,
			$this
		);
	}

	public function name(): string {
		return $this->definition['name'];
	}

	public function get_model(): string {
		return $this->definition['model'];
	}

	public function get_provider(): string {
		return $this->definition['provider'];
	}

	public function json(): bool {
		return $this->definition['json'];
	}

	public function tools(): array {
		return $this->definition['tools'];
	}

	public function hooks(): array {
		return $this->definition['hooks'];
	}

	public function get_file(): ?string {
		return $this->definition['file'];
	}

	public function get_memory( string $session_id ): ?Wp_Agents_Memory_Abstract {
		if ( $this->definition['memory'] ) {
			$memory_class = $this->definition['memory'];

			return new $memory_class( $this->name(), $session_id );
		}

		return null;
	}

	public function toArray(): array {
		return $this->definition;
	}
}
