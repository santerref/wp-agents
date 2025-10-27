<?php

add_action( 'wp_agents_register_providers', function () {
	wp_agents_register_provider( 'openai', function () {
		if ( ! defined( 'OPENAI_API_KEY' ) ) {
			return new WP_Error(
				'wp_agents_openai_api_key_missing',
				'The OpenAI API Key is not set in the wp-config.php.'
			);
		}

		return new Wp_Agents_Providers_Open_Ai( OPENAI_API_KEY );
	} );
} );
