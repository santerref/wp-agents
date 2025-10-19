<?php

namespace Wp_Agents\Inputs;

use Wp_Agents\Exceptions\Skip_Agent_Exception;

class Post_Updated_Input extends Abstract_Input {

	protected int $accepted_args = 2;

	public static function build( ...$args ): string {
		$post = $args[1];
		if ( $post->post_type != 'post' ) {
			throw new Skip_Agent_Exception();
		}

		$categories = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
		$tags       = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );

		$category_list = empty( $categories ) ? 'none' : implode( ', ', $categories );
		$tag_list      = empty( $tags ) ? 'none' : implode( ', ', $tags );

		return <<<PROMPT
Post title: {$post->post_title}

Post content: {$post->post_content}

Existing category: {$category_list}
Existing tags: {$tag_list}
PROMPT;
	}
}
