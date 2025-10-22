<?php

namespace Wp_Agents\Agents;

use Wp_Agents\Providers\Provider_Interface;
use Wp_Agents\Services\Provider_Manager;
use Wp_Agents\System\Agent_Runner;

abstract class Abstract_Llm_Agent {

	protected string $model = 'gpt-4o-mini';

	protected string $provider = 'openai';

	protected bool $json = false;

	protected array $filters = array();

	protected array $tools = array();

	abstract public function instructions(): string;

	public function filters(): array {
		return $this->filters;
	}

	public function prompt( mixed $input ): Agent_Runner {
		return new Agent_Runner(
			$input,
			$this
		);
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

	public function handle_response( mixed $answer, array $args = array() ): void {
	}
}
