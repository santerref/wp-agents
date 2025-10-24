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

	public function chat( Message_Stack $message_stack, Abstract_Llm_Agent $agent ): void {
		$client = \OpenAI::client( $this->api_key );
		$tools  = $agent->tools();

		$message_stack->unshift( new Message( 'system', $agent->instructions() ) );

		$flatten = function ( Message $message ) {
			return array(
				       'role'    => $message->get_author(),
				       'content' => $message->get_message(),
			       ) + $message->get_metadata();
		};

		$parameters = array(
			'model'           => $agent->get_model(),
			'messages'        => $message_stack->map( $flatten ),
			'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
		);

		if ( count( $tools ) ) {
			$tool_registry       = new Tool_Registry( $tools );
			$parameters['tools'] = $tool_registry->definitions();
		}

		$response       = $client->chat()->create( $parameters );
		$openai_message = $response->choices[0]->message;

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( ! empty( $openai_message->toolCalls ) ) {
			$tool_calls    = array();
			$tool_messages = array();

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			foreach ( $openai_message->toolCalls as $call ) {
				$function = $call->function;
				$args     = json_decode( $function->arguments ?? '{}', true );

				if ( ! is_array( $args ) ) {
					$args = array();
				}
				$result = $tool_registry->execute( $function->name, $args );

				$tool_calls[] = array(
					'id'       => $call->id,
					'type'     => $call->type ?? 'function',
					'function' => array(
						'name'      => $call->function->name,
						'arguments' => $call->function->arguments,
					),
				);

				$tool_messages[] = new Message(
					'tool',
					wp_json_encode( $result ),
					array(
						'tool_call_id' => $call->id,
					)
				);
			}

			$message_stack->add(
				new Message(
					$openai_message->role,
					$openai_message->content,
					array(
						'tool_calls' => $tool_calls
					)
				)
			);
			$message_stack->add( $tool_messages );

			$follow = $client->chat()->create(
				array(
					'model'           => $agent->get_model(),
					'messages'        => $message_stack->map( $flatten ),
					'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
				)
			);

			$openai_message = $follow->choices[0]->message;
			$message_stack->add(
				new Message(
					$openai_message->role,
					$openai_message->content
				)
			);
		} else {
			$message_stack->add(
				new Message(
					$openai_message->role,
					$openai_message->content,
				)
			);
		}
	}
}
