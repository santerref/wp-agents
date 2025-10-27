<?php

abstract class Wp_Agents_Llm_Abstract {

	protected string $model = 'gpt-4o-mini';

	protected string $provider = 'openai';

	protected bool $json = false;

	protected array $filters = array();

	protected array $tools = array();

	protected ?string $memory = null;

	protected ?int $memory_limit = null;

	protected string $name = '';

	public function __construct( string $name ) {
		$this->name = $name;
	}

	abstract public function instructions(): string;

	public function filters(): array {
		return $this->filters;
	}

	public function prompt( mixed $input ): Wp_Agents_System_Agent_Runner {
		return new Wp_Agents_System_Agent_Runner(
			$input,
			$this
		);
	}

	public function name(): string {
		return $this->name;
	}

	public function get_model(): string {
		return $this->model;
	}

	public function get_provider(): string {
		return $this->provider;
	}

	public function json(): string {
		return $this->json;
	}

	public function tools(): array {
		return $this->tools;
	}

	public function get_memory( string $session_id ): ?Wp_Agents_Memory_Abstract {
		if ( $this->memory ) {
			$memory_class = $this->memory;

			return new $memory_class( $this->name(), $session_id );
		}

		return null;
	}
}
