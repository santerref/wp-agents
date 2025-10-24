<?php

namespace Wp_Agents\System;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\Providers\Provider_Interface;
use Wp_Agents\Services\Provider_Manager;

class Agent_Runner {

	protected string $input;

	protected Provider_Interface $provider;

	protected Abstract_Llm_Agent $agent;

	protected ?string $session_id = null;

	public function __construct( string $input, Abstract_Llm_Agent $agent ) {
		$this->input    = $input;
		$this->agent    = $agent;
		$this->provider = Provider_Manager::get( $agent->get_provider() );
	}

	public function with_session( ?string $session_id ): static {
		$this->session_id = $session_id;

		return $this;
	}

	public function chat(): Message {
		$memory        = $this->session_id ? $this->agent->get_memory( $this->session_id ) : null;
		$message_stack = $memory ? $memory->load() : new Message_Stack();
		$message_stack->add( new Message( 'user', $this->input ) );

		$this->provider->chat( $message_stack, $this->agent );

		foreach ( $message_stack->all() as $message ) {
			if ( ! $message->memorized() && 'system' !== $message->get_author() ) {
				$memory?->remember( $message );
			}
		}

		return $message_stack->last();
	}
}
