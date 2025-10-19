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

https://github.com/santerref/wp-agents-demo

## Roadmap

- **Workflows:** Introduce workflows to connect and orchestrate multiple agents for complex, multi-step tasks.
- **REST API:** Add REST API endpoints to enable external chat and interaction with agents.
- **Memory:** Implement persistent memory to maintain context across conversations and enable long-running sessions.
- **Tests:** Expand and complete Pest test coverage for agents, tools, and provider logic.
- **Providers:** Add more LLM providers and improve compatibility with third-party APIs.
