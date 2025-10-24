<?php

namespace Wp_Agents\Memory;

use Wp_Agents\System\Memorized_Message;
use Wp_Agents\System\Message;
use Wp_Agents\System\Message_Stack;

class Database_Memory extends Abstract_Memory {

	protected \wpdb $db;

	protected string $table;

	public function __construct( string $agent, string $session_id ) {
		parent::__construct( $agent, $session_id );
		global $wpdb;
		$this->db    = $wpdb;
		$this->table = $wpdb->prefix . 'agents_memory_messages';
	}

	public function remember( Message $message ): void {
		$this->db->insert(
			$this->table,
			array(
				'agent'      => $this->agent,
				'session_id' => $this->session_id,
				'author'     => $message->get_author(),
				'message'    => $message->get_message(),
				'metadata'   => $message->get_metadata()
					? wp_json_encode( $message->get_metadata() )
					: null,
			)
		);
	}

	public function load( ?int $limit = null ): Message_Stack {
		$sql = "SELECT author, message, metadata
	        FROM {$this->table}
	        WHERE agent = %s AND session_id = %s
	        ORDER BY id ASC";

		$params = array( $this->agent, $this->session_id );

		if ( null !== $limit ) {
			$sql     .= ' LIMIT %d';
			$params[] = $limit;
		}

		$results = $this->db->get_results(
			$this->db->prepare( $sql, ...$params ),
			ARRAY_A
		);

		$messages = array_map(
			function ( $row ) {
				return new Memorized_Message(
					$row['author'],
					$row['message'],
					json_decode( $row['metadata'] ?? '[]', true )
				);
			},
			$results
		);

		return new Message_Stack( $messages );
	}

	public function forget_all(): void {
		$this->db->delete(
			$this->table,
			array(
				'agent'      => $this->agent,
				'session_id' => $this->session_id,
			)
		);
	}
}
