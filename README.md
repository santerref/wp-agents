# WP Agents – AI Agent Framework for WordPress

Build autonomous, hook-driven agents for WordPress — automate tasks and add LLM intelligence with clean, developer-first architecture.

## Key Features

- **Providers:** Define and manage LLM providers (e.g., OpenAI, Anthropic) through a unified API for completions and structured outputs.
- **Tools:** Register callable functions that agents can dynamically execute during reasoning or task completion.
- **Input:** Capture and preprocess data from actions and filters to generate structured prompts for LLMs.
- **Agents:** Create modular agents that interact with WordPress using a consistent architecture.
- **Tests:** Basic Pest testing structure in place — full coverage and assertions to be implemented.

## Quick Start
```bash
cd wp-content/plugins

# Clone the plugin into your plugins folder
git clone git@github.com:santerref/wp-agents.git wp-agents

# Install PHP packages
cd wp-agents && composer install

# Install the plugin with WP-CLI or via the administration
wp plugin activate wp-agents
```

## Demo

In this demo, we’ll build a simple agent that analyzes WordPress post content and automatically assigns the most relevant **category** and **tags**.  

The agent connects to an LLM provider, processes post data received through WordPress hooks, and returns structured metadata ready to be applied to the post — showing how **Wp Agents** can bridge AI reasoning with real WordPress actions.

### wp-agents-demo

Edit your `wp-config.php` file to set your OpenAI key:

```php
define( 'OPENAI_API_KEY', '' );
```

Create a new folder inside `plugins` named `wp-agents-demo`. Add the `wp-agents-demo.php` file:

```php
<?php
/*
 * Plugin Name: WP Agent Demo
 * Version: 0.0.1
 * Requires Plugins: wp-agents
 */

add_action( 'wp_agents_register_agents', function () {
	require_once __DIR__ . '/tools.php';
	require_once __DIR__ . '/agents.php';

	wp_agents_register(
		'taxonomy_agent',
		Wp_Agent_Post_Taxonomy::class,
	);
} );

```

Create a new file `tools.php` to register the tools used by the agent (get or create categories and get or create tags):

```php
<?php

if ( ! class_exists( 'Category_Lookup_Tool' ) ) {

	class Category_Lookup_Tool implements \Wp_Agents\Tools\Tool_Interface {

		public function definition(): array {
			return array(
				'name'        => 'find_or_create_category',
				'description' => 'Creates a WordPress category if missing.',
				'parameters'  => [
					'type'       => 'object',
					'properties' => [
						'name' => [ 'type' => 'string' ],
					],
					'required'   => [ 'name' ],
				],
			);
		}

		public function execute( array $arguments ): mixed {
			$term = get_term_by( 'name', $arguments['name'], 'category', ARRAY_A );
			if ( ! $term ) {
				$term = wp_insert_term( $arguments['name'], 'category' );
			}

			return [ 'term_id' => (int) ( $term['term_id'] ?? 0 ) ];
		}
	}

}

if ( ! class_exists( 'Tag_Lookup_Tool' ) ) {

	class Tag_Lookup_Tool implements \Wp_Agents\Tools\Tool_Interface {

		public function definition(): array {
			return array(
				'name'        => 'find_or_create_tags',
				'description' => 'Creates WordPress tags if missing.',
				'parameters'  => [
					'type'       => 'object',
					'properties' => [
						'names' => [
							'type'  => 'array',
							'items' => [ 'type' => 'string' ],
						],
					],
					'required'   => [ 'names' ],
				],
			);
		}

		public function execute( array $arguments ): array {
			$ids = [];

			foreach ( $arguments['names'] as $name ) {
				$term = get_term_by( 'name', $name, 'post_tag', ARRAY_A );
				if ( ! $term ) {
					$term = wp_insert_term( $name, 'post_tag' );
				}

				if ( is_array( $term ) && isset( $term['term_id'] ) ) {
					$ids[] = (int) $term['term_id'];
				} elseif ( $term instanceof \WP_Term ) {
					$ids[] = (int) $term->term_id;
				}
			}

			return [ 'term_ids' => $ids ];
		}
	}

}
```

Create a new file `agents.php` to register our demo agent:

```php
<?php

if ( ! class_exists( 'Wp_Agent_Post_Taxonomy' ) ) {

	class Wp_Agent_Post_Taxonomy extends \Wp_Agents\Agents\Abstract_Llm_Agent {

		protected array $actions = [
			'post_updated'
		];

		protected array $tools = [
			Category_Lookup_Tool::class,
			Tag_Lookup_Tool::class
		];

		public function instructions(): string {
			return 'You are a WordPress editorial AI assistant. '
			       . 'Analyze the post content and assign the most relevant category and tags. '
			       . 'If the category and tags are already relevant enough, do not add extra. '
			       . 'Try using existing categories and tags, only create new if needed.'
			       . 'You can call tools to create them if missing. '
			       . 'Return only JSON in this exact format: '
			       . '{"category": "Travel", "tags": ["Thailand", "Beaches", "Vacation"]}.';
		}

		public function handle_response( mixed $answer, array $args = array() ): void {
			$post_id = (int) $args[0];
			$data    = json_decode( $answer, true );

			if ( ! empty( $data['category'] ) ) {
				$term = get_term_by( 'name', $data['category'], 'category' );
				if ( $term ) {
					wp_set_post_terms( $post_id, [ $term->term_id ], 'category' );
				}
			}

			if ( ! empty( $data['tags'] ) && is_array( $data['tags'] ) ) {
				$tag_ids = [];
				foreach ( $data['tags'] as $tag_name ) {
					$term = get_term_by( 'name', $tag_name, 'post_tag' );
					if ( $term ) {
						$tag_ids[] = $term->term_id;
					}
				}
				if ( $tag_ids ) {
					wp_set_post_terms( $post_id, $tag_ids );
				}
			}
		}
	}

}
```

That's all we need. After, go into WordPress and create a new post (you can use ChatGPT to generate a title with content).

After you hit save (or publish), you should see that your post has a category and tags set.

<img width="250" height="500" alt="Screenshot 2025-10-19 at 6 41 27 PM" src="https://github.com/user-attachments/assets/62254931-92f8-4ae8-88aa-1a82f23eb524" />

## Roadmap

- **Workflows:** Introduce workflows to connect and orchestrate multiple agents for complex, multi-step tasks.
- **REST API:** Add REST API endpoints to enable external chat and interaction with agents.
- **Memory:** Implement persistent memory to maintain context across conversations and enable long-running sessions.
- **Tests:** Expand and complete Pest test coverage for agents, tools, and provider logic.
- **Providers:** Add more LLM providers and improve compatibility with third-party APIs.
