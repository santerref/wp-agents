<?php
/*
Agent Name: Post Taxonomy Agent
Description: Automatically assign a category and tags to a post based on the content.
Version: 1.0.0
Tools: wordpress.ensure_category, wordpress.ensure_tags
Hooks: action.post_updated
*/

add_action( 'post_updated', function ( $post_id, $post_after ) {
	if ( 'post' === $post_after->post_type ) {
		$categories = wp_get_post_categories( $post_after->ID, array( 'fields' => 'names' ) );
		$tags       = wp_get_post_tags( $post_after->ID, array( 'fields' => 'names' ) );

		$category_list = empty( $categories ) ? 'none' : implode( ', ', $categories );
		$tag_list      = empty( $tags ) ? 'none' : implode( ', ', $tags );

		$input = "Post title: {$post_after->post_title}\n\n"
		         . "Post content: {$post_after->post_content}\n\n"
		         . "Existing category: {$category_list}\n"
		         . "Existing tags: {$tag_list}";

		$response = wp_agents_get( 'post-taxonomy' )
			->prompt( $input )
			->chat();

		$data = json_decode( $response->get_message(), true );

		if ( ! empty( $data['category']['term_id'] ) ) {
			wp_set_post_terms( $post_id, [ $data['category']['term_id'] ], 'category' );
		}

		if ( ! empty( $data['tags']['term_ids'] ) ) {
			wp_set_post_terms( $post_id, $data['tags']['term_ids'], 'post_tag' );
		}
	}
}, 10, 2 );
