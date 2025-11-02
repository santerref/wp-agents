# WP Agents – AI Agent Framework for WordPress

Build autonomous, hook-driven agents for WordPress — automate tasks and add LLM intelligence with clean, developer-first architecture.

📣 The project is open to contributions. Developers are invited to test, improve, and expand the framework as it evolves.

## Key Features

- **Providers:** Define and manage LLM providers (e.g., OpenAI, Anthropic) through a unified API for completions and structured outputs.
- **Tools:** Register callable functions that agents can dynamically execute during reasoning or task completion.
- **REST API:** Provides endpoints that allow external applications to interact with agents and send or receive chat messages programmatically.
- **Memory:** Supports persistent conversation history, enabling agents to retain context across sessions and handle long-running or multi-turn interactions.
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

## REST API

Each agent can be accessed programmatically through the WordPress REST API. 

This allows external systems or front-end applications to send a message to any registered agent and receive the model’s response in JSON format.

**Endpoint**

```bash
POST /wp-json/wp-agents/v1/chat
```

**Example**

```bash
curl --location 'https://example.com/wp-json/wp-agents/v1/chat' \
--header 'Content-Type: application/json' \
--data '{
    "agent": "taxonomy_agent",
    "message": "Hello"
}'
```

**Example response**

```json
{
    "agent": "taxonomy_agent",
    "message": "Hello",
    "response": {
        "role": "assistant",
        "content": "Hello! How can I assist you today?"
    }
}
```

## Demo

https://github.com/santerref/wp-agents-demo

## Roadmap

- **Workflows:** Introduce workflows to connect and orchestrate multiple agents for complex, multi-step tasks.
- **Tests:** Expand and complete Pest test coverage for agents, tools, and provider logic.
- **Providers:** Add more LLM providers and improve compatibility with third-party APIs.
- **RAG:** Integrate Retrieval-Augmented Generation to allow agents to query custom datasets, documents, or WordPress content for more context-aware responses.
- **MCP Server:** Ship a local Message-Control-Protocol server to expose WordPress as a tool provider and allow external AI clients to execute WordPress actions and workflows securely.
