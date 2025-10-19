<?php

namespace Wp_Agents\Services;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger {

	protected string $file;

	public function __construct( string $baseDir ) {
		$dir = rtrim( $baseDir, '/' ) . '/logs';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		$this->file = "{$dir}/wp-agents.log";
	}

	public function log( $level, $message, array $context = array() ): void {
		$date = gmdate( 'Y-m-d H:i:s' );
		$msg  = $this->interpolate( $message, $context );

		$line = "[{$date}] {$level}: {$msg}\n";
		file_put_contents( $this->file, $line, FILE_APPEND | LOCK_EX );
	}

	protected function interpolate( string $message, array $context ): string {
		$replace = array();
		foreach ( $context as $key => $val ) {
			if ( is_array( $val ) || is_object( $val ) ) {
				$val = json_encode( $val, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			}
			$replace[ '{' . $key . '}' ] = $val;
		}

		return strtr( $message, $replace );
	}
}
