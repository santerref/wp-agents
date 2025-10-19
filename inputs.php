<?php

use Wp_Agents\Inputs\Post_Updated_Input;

add_action( 'wp_agents_register_inputs', function () {
	wp_agents_register_input( 'post_updated', Post_Updated_Input::class );
} );
