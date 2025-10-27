<?php

abstract class Wp_Agents_Memory_Abstract {

	protected string $agent;

	protected string $session_id;

	public function __construct( string $agent, string $session_id ) {
		$this->agent      = $agent;
		$this->session_id = $session_id;
	}

	abstract public function remember( Wp_Agents_System_Message $message ): void;

	abstract public function load( ?int $limit = null ): Wp_Agents_System_Message_Stack;

	abstract public function forget_all(): void;
}
