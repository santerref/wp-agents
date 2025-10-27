<?php

class Wp_Agents_System_Schema {

	protected \wpdb $db;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	public function install(): void {
		$installed = get_option( 'wp_agents_installed' );
		if ( ! $installed ) {
			$table   = $this->db->prefix . 'agents_memory_messages';
			$charset = $this->db->get_charset_collate();

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta(
				"CREATE TABLE $table (
		        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		        agent VARCHAR(150) NOT NULL,
		        session_id VARCHAR(150) NOT NULL,
		        author VARCHAR(150) NOT NULL,
		        message LONGTEXT NOT NULL,
		        metadata JSON NULL,
		        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		        INDEX (agent),
		        INDEX (session_id)
		    ) $charset;"
			);

			update_option( 'wp_agents_installed', true );
		}
	}
}
