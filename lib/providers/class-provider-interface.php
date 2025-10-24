<?php

namespace Wp_Agents\Providers;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\System\Message_Stack;

interface Provider_Interface {

	public function chat( Message_Stack $message_stack, Abstract_Llm_Agent $agent ): void;
}
