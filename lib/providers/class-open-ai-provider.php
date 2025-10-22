<?php

namespace Wp_Agents\Providers;

use Wp_Agents\Agents\Abstract_Llm_Agent;
use Wp_Agents\System\Message;
use Wp_Agents\System\Message_Stack;
use Wp_Agents\Tools\Tool_Registry;

class Open_Ai_Provider implements Provider_Interface {

	protected string $api_key;

	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	public function chat( string $input, Abstract_Llm_Agent $agent ): Message_Stack {
		$client = \OpenAI::client( $this->api_key );
		$tools  = $agent->tools();

		$messages = new Message_Stack(
			array(
				new Message( 'system', $agent->instructions() ),
				new Message( 'user', $input ),
			)
		);

		$parameters = array(
			'model'           => $agent->get_model(),
			'messages'        => $messages->to_raw_array(),
			'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
		);

		if ( count( $tools ) ) {
			$tool_registry       = new Tool_Registry( $tools );
			$parameters['tools'] = $tool_registry->definitions();
		}

		$response = $client->chat()->create( $parameters );

		$message = $response->choices[0]->message;
		$messages->add(
			new Message(
				$message->role,
				$message->content,
				$message->toArray()
			)
		);

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

				$messages->add(
					new Message(
						'tool',
						wp_json_encode( $result ),
						array(
							'tool_call_id' => $call->id,
						)
					)
				);
			}

			$follow = $client->chat()->create(
				array(
					'model'           => $agent->get_model(),
					'messages'        => $messages->to_raw_array(),
					'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
				)
			);

			$message = $follow->choices[0]->message;
			$messages->add(
				new Message(
					$message->role,
					$message->content,
					$message->toArray(),
				)
			);
		}

		return $messages;
	}
}
