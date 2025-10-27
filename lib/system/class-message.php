<?php

class Wp_Agents_System_Message {

	protected string $author;

	protected ?string $message;

	protected array $metadata = array();

	protected bool $memorized = false;

	public function __construct( string $author, ?string $message, array $metadata = array() ) {
		$this->author   = $author;
		$this->message  = $message;
		$this->metadata = $metadata;
	}

	public function get_author(): string {
		return $this->author;
	}

	public function get_message(): string {
		return $this->message;
	}

	public function memorized(): bool {
		return $this->memorized;
	}

	public function get_metadata( ?string $key = null, mixed $default_value = null ): array {
		return $key ? ( $this->metadata[ $key ] ?? $default_value ) : $this->metadata;
	}

	public function to_array(): array {
		return array(
			'author'   => $this->author,
			'message'  => $this->message,
			'metadata' => $this->metadata,
		);
	}
}
