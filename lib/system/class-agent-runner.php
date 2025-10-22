<?php

namespace Wp_Agents\System;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\Providers\Provider_Interface;
use Wp_Agents\Services\Provider_Manager;

class Agent_Runner {

	protected string $input;

	protected Provider_Interface $provider;

	protected Abstract_Llm_Agent $agent;

	public function __construct( string $input, Abstract_Llm_Agent $agent ) {
		$this->input    = $input;
		$this->agent    = $agent;
		$this->provider = Provider_Manager::get( $agent->get_provider() );
	}

	public function chat(): Message {
		$message_stack = $this->provider->chat( $this->input, $this->agent );

		return $message_stack->last();
	}
}
