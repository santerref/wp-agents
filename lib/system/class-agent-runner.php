<?php

class Wp_Agents_System_Agent_Runner {

	protected string $input;

	protected Wp_Agents_Providers_Interface $provider;

	protected Wp_Agents_Agent_Abstract $agent;

	protected ?string $session_id = null;

	public function __construct( string $input, Wp_Agents_Agent_Abstract $agent ) {
		$this->input    = $input;
		$this->agent    = $agent;
		$this->provider = wp_agents_provider_manager()->get( $agent->get_provider() );
	}

	public function with_session( ?string $session_id ): static {
		$this->session_id = $session_id;

		return $this;
	}

	public function chat(): Wp_Agents_System_Message {
		$memory        = $this->session_id ? $this->agent->get_memory( $this->session_id ) : null;
		$message_stack = $memory ? $memory->load() : new Wp_Agents_System_Message_Stack();
		$message_stack->add( new Wp_Agents_System_Message( 'user', $this->input ) );

		$this->provider->chat( $message_stack, $this->agent );

		foreach ( $message_stack->all() as $message ) {
			if ( ! $message->memorized() && $this->provider->memorizable( $message ) ) {
				$memory?->remember( $message );
			}
		}

		return $message_stack->last();
	}
}
