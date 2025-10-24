<?php

namespace Wp_Agents\System;

use Traversable;

class Message_Stack implements \IteratorAggregate, \Countable {

	protected array $messages = array();

	public function __construct( array $messages = array() ) {
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

	public function unshift( Message $message ) {
		array_unshift( $this->messages, $message );
	}

	public function add( array|Message $messages ) {
		$messages = $messages instanceof Message ? array( $messages ) : $messages;

		foreach ( $messages as $message ) {
			if ( $message instanceof Message ) {
				$this->messages[] = $message;
			}
		}
	}

	public function all(): array {
		return $this->messages;
	}

	public function map( callable $callback ): array {
		return array_map( $callback, $this->all() );
	}
}
