<?php

class Wp_Agents_System_Message_Stack implements \IteratorAggregate, \Countable {

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

	public function first(): ?Wp_Agents_System_Message {
		return $this->messages[0] ?? null;
	}

	public function last(): ?Wp_Agents_System_Message {
		$count = count( $this->messages );

		return $count ? $this->messages[ $count - 1 ] : null;
	}

	public function unshift( Wp_Agents_System_Message $message ) {
		array_unshift( $this->messages, $message );
	}

	public function add( array|Wp_Agents_System_Message $messages ) {
		$messages = $messages instanceof Wp_Agents_System_Message ? array( $messages ) : $messages;

		foreach ( $messages as $message ) {
			if ( $message instanceof Wp_Agents_System_Message ) {
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
