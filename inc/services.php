<?php

if ( ! function_exists( 'wp_agents_tool_manager' ) ) {

	function wp_agents_tool_manager() {
		static $tool_manager;

		if ( ! isset( $tool_manager ) ) {
			$tool_manager = new Wp_Agents_Services_Tool_Manager();
		}

		return $tool_manager;
	}

}

if ( ! function_exists( 'wp_agents_rest' ) ) {

	function wp_agents_rest() {
		static $rest;

		if ( ! isset( $rest ) ) {
			$rest = new Wp_Agents_System_Rest();
		}

		return $rest;
	}

}

if ( ! function_exists( 'wp_agents_agent_manager' ) ) {

	function wp_agents_agent_manager() {
		static $agent_manager;

		if ( ! isset( $agent_manager ) ) {
			$agent_manager = new Wp_Agents_Services_Agent_Manager();
		}

		return $agent_manager;
	}

}

if ( ! function_exists( 'wp_agents_provider_manager' ) ) {

	function wp_agents_provider_manager() {
		static $provider_manager;

		if ( ! isset( $provider_manager ) ) {
			$provider_manager = new Wp_Agents_Services_Provider_Manager();
		}

		return $provider_manager;
	}

}
