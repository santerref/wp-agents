<?php

use Wp_Agents\Exceptions\Missing_Provider_Environment_Variables;
use Wp_Agents\Providers\Open_Ai_Provider;

add_action( 'wp_agents_register_providers', function () {
	wp_agents_register_provider( 'openai', function () {
		if ( ! defined( 'OPENAI_API_KEY' ) ) {
			throw new Missing_Provider_Environment_Variables();
		}

		return new Open_Ai_Provider( OPENAI_API_KEY );
	} );
} );
