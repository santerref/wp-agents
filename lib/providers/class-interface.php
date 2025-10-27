<?php

interface Wp_Agents_Providers_Interface {

	public function chat( Wp_Agents_System_Message_Stack $message_stack, Wp_Agents_Llm_Abstract $agent ): void;
}
