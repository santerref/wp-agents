<?php

namespace Wp_Agents\Inputs;

abstract class Abstract_Input {

	protected int $priority = 10;

	protected int $accepted_args = 1;

	abstract public static function build( ...$args ): string;

	public function get_priority(): int {
		return $this->priority;
	}

	public function get_accepted_args(): int {
		return $this->accepted_args;
	}
}
