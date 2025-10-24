<?php

namespace Wp_Agents\Memory;

use Wp_Agents\System\Message;
use Wp_Agents\System\Message_Stack;

abstract class Abstract_Memory {

	protected string $agent;

	protected string $session_id;

	public function __construct( string $agent, string $session_id ) {
		$this->agent      = $agent;
		$this->session_id = $session_id;
	}

	abstract public function remember( Message $message ): void;

	abstract public function load( ?int $limit = null ): Message_Stack;

	abstract public function forget_all(): void;
}
