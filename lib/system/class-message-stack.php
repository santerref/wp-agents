<?php

namespace Wp_Agents\System;

use Traversable;

class Message_Stack implements \IteratorAggregate, \Countable {

	protected array $messages = array();

	public function __construct( array $messages ) {
		$this->messages = $messages;
	}

	public function getIterator(): Traversable {
		return new \ArrayIterator( $this->messages );
	}

	public function count(): int {
		return count( $this->messages );
	}

	public function first(): ?Message {
		return $this->messages[0] ?? null;
	}

	public function last(): ?Message {
		$count = count( $this->messages );

		return $count ? $this->messages[ $count - 1 ] : null;
	}

	public function add( Message $message ) {
		$this->messages[] = $message;
	}

	public function to_raw_array(): array {
		return array_map(
			function ( Message $message ) {
				return $message->get_raw_response();
			},
			$this->messages
		);
	}
}
