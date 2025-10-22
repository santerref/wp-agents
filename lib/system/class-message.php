<?php

namespace Wp_Agents\System;

class Message {

	protected string $role;

	protected ?string $content;

	protected array $raw_response = array();

	public function __construct( string $role, ?string $content, array $raw_response = array() ) {
		$this->role    = $role;
		$this->content = $content;

		if ( empty( $raw_response ) ) {
			$this->raw_response = array(
				'role'    => $role,
				'content' => $content,
			);
		} else {
			$this->raw_response = $raw_response;
		}
	}

	public function get_role(): string {
		return $this->role;
	}

	public function get_content(): string {
		return $this->content;
	}

	public function get_raw_response(): array {
		return array(
			'role'    => $this->role,
			'content' => $this->content,
		) + $this->raw_response;
	}

	public function to_array(): array {
		return array(
			'role'         => $this->role,
			'content'      => $this->content,
			'raw_response' => $this->raw_response,
		);
	}
}
