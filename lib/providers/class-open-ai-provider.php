<?php

namespace Wp_Agents\Providers;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\Tools\Tool_Registry;

class Open_Ai_Provider implements Provider_Interface {

	protected string $api_key;

	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	public function complete( string $prompt, Abstract_Llm_Agent $agent ): string {
		$client = \OpenAI::client( $this->api_key );
		$tools  = $agent->tools();

		$messages   = array(
			array(
				'role'    => 'system',
				'content' => $agent->instructions(),
			),
			array(
				'role'    => 'user',
				'content' => $prompt,
			),
		);
		$parameters = array(
			'model'           => $agent->get_model(),
			'messages'        => $messages,
			'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
		);

		if ( count( $tools ) ) {
			$tool_registry       = new Tool_Registry( $tools );
			$parameters['tools'] = $tool_registry->definitions();
		}

		$response = $client->chat()->create( $parameters );

		$message    = $response->choices[0]->message;
		$messages[] = $message->toArray();

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( ! empty( $message->toolCalls ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			foreach ( $message->toolCalls as $call ) {
				$function = $call->function;
				$args     = json_decode( $function->arguments ?? '{}', true );

				if ( ! is_array( $args ) ) {
					$args = array();
				}
				$result = $tool_registry->execute( $function->name, $args );

				$messages[] = array(
					'role'         => 'tool',
					'tool_call_id' => $call->id,
					'content'      => wp_json_encode( $result ),
				);
			}

			$follow = $client->chat()->create(
				array(
					'model'           => $agent->get_model(),
					'messages'        => $messages,
					'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
				)
			);

			return $follow->choices[0]->message->content ?? '';
		}

		return $response->choices[0]->message->content ?? '';
	}
}
