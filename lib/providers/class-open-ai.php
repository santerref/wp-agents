<?php

class Wp_Agents_Providers_Open_Ai implements Wp_Agents_Providers_Interface {

	protected string $api_key;

	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	public function chat( Wp_Agents_System_Message_Stack $message_stack, Wp_Agents_Llm_Abstract $agent ): void {
		$tools = $agent->tools();

		$message_stack->unshift( new Wp_Agents_System_Message( 'developer', $agent->instructions() ) );

		$body = array(
			'model'           => $agent->get_model(),
			'messages'        => $message_stack->map( array( $this, 'flatten' ) ),
			'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
		);

		$tool_registry = new Wp_Agents_Tools_Registry();
		if ( count( $tools ) ) {
			$tool_registry->add( $tools );
			$body['tools'] = $tool_registry->definitions();
		}

		$response = $this->post( 'https://api.openai.com/v1/chat/completions', $body );
		if ( is_wp_error( $response ) ) {
			$message_stack->add( new Wp_Agents_System_Message( 'assistant', 'Error: request failed' ) );

			return;
		}

		$openai_message = $response['choices'][0]['message'];

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( ! empty( $openai_message['tool_calls'] ) ) {
			$tool_calls    = array();
			$tool_messages = array();

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			foreach ( $openai_message['tool_calls'] as $call ) {
				$args = json_decode( $call['function']['arguments'] ?? '{}', true ) ?: array();

				if ( ! is_array( $args ) ) {
					$args = array();
				}
				$result = $tool_registry->execute( $call['function']['name'], $args );

				$tool_calls[] = array(
					'id'       => $call['id'],
					'type'     => $call['type'] ?? 'function',
					'function' => array(
						'name'      => $call['function']['name'],
						'arguments' => $call['function']['arguments'],
					),
				);

				$tool_messages[] = new Wp_Agents_System_Message(
					'tool',
					wp_json_encode( $result ),
					array(
						'tool_call_id' => $call['id'],
					)
				);
			}

			$message_stack->add(
				new Wp_Agents_System_Message(
					$openai_message['role'],
					$openai_message['content'],
					array(
						'tool_calls' => $tool_calls,
					)
				)
			);
			$message_stack->add( $tool_messages );

			$follow = $this->post(
				'https://api.openai.com/v1/chat/completions',
				array(
					'model'           => $agent->get_model(),
					'messages'        => $message_stack->map( array( $this, 'flatten' ) ),
					'response_format' => array( 'type' => $agent->json() ? 'json_object' : 'text' ),
				)
			);
			if ( is_wp_error( $response ) ) {
				$message_stack->add( new Wp_Agents_System_Message( 'assistant', 'Error: request failed' ) );

				return;
			}

			$openai_message = $follow['choices'][0]['message'];
			$message_stack->add(
				new Wp_Agents_System_Message(
					$openai_message['role'],
					$openai_message['content']
				)
			);
		} else {
			$message_stack->add(
				new Wp_Agents_System_Message(
					$openai_message['role'],
					$openai_message['content'],
				)
			);
		}
	}

	public function memorizable( Wp_Agents_System_Message $message ): bool {
		return ! in_array( $message->get_author(), array( 'system', 'developer' ) );
	}

	public function flatten( Wp_Agents_System_Message $message ): array {
		return array(
			'role'    => $message->get_author(),
			'content' => $message->get_message(),
		) + $message->get_metadata();
	}

	protected function post( string $url, array $body = array() ): array|WP_Error {
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'  => 'application/json',
			),
			'timeout' => 30,
		);

		if ( ! empty( $body ) ) {
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'wp_agents_openai_invalid_response',
				'Invalid response returned by the OpenAI API.'
			);
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
