<template>
  <table class="wp-list-table widefat plugins">
    <thead>
    <tr>
      <th scope="col" id="name" class="manage-column column-name column-primary">Agent</th>
      <th scope="col" id="description" class="manage-column column-description">Description</th>
      <th scope="col" id="description" class="manage-column column-description">Provider</th>
      <th scope="col" id="description" class="manage-column column-description">Tools</th>
      <th scope="col" id="description" class="manage-column column-description">Hooks</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="agent in agents" :id="agent.id"
        :class="{'tw:bg-[#f0f6fc] tw:shadow-[inset_0_-1px_0_rgba(0,0,0,.1)]': agent.enabled}">
      <td class="tw:p-[10px] tw:border-l-4"
          :class="{'tw:border-l-transparent': !agent.enabled, 'tw:border-l-[#72aee6]': agent.enabled}">
        <span class="tw:text-[14px] tw:mb-1 tw:inline-block" :class="{'tw:font-semibold':agent.enabled}">{{ agent.name }}</span>
        <div class="row-actions visible">
          <span class="activate">
            <button
                @click.prevent="toggle(agent)"
                class="tw:border-none tw:bg-transparent tw:p-0 tw:m-0 tw:cursor-pointer tw:text-blue-wp">
              <span v-if="agent.enabled">Deactivate</span>
              <span v-else>Activate</span>
            </button>
          </span>
        </div>
      </td>
      <td class="tw:p-[10px]">
        <div class="plugin-description">
          <p>{{ agent.description }}</p>
        </div>
        <div class="plugin-version-author-uri">
          Version {{ agent.version }}
        </div>
      </td>
      <td class="tw:p-[10px]">
        OpenAI ({{ agent.model }})
      </td>
      <td v-html="agent.tools.join(',<br>')"></td>
      <td v-html="agent.hooks.join(',<br>')"></td>
    </tr>
    </tbody>
  </table>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import type {Agent} from "../types";

const agents = ref<Agent[]>([])

onMounted(async () => {
  const response = await fetch('/wp-json/wp-agents/v1/agents', {
    credentials: 'same-origin'
  })
  agents.value = await response.json()
})

const toggle = async (agent: Agent) => {
  const action = agent.enabled ? 'deactivate' : 'activate'

  const result = await fetch(`/wp-json/wp-agents/v1/agents/${agent.id}/${action}`, {
    method: 'POST',
    credentials: 'same-origin'
  }).then(response => response.json())

  const index = agents.value.findIndex(item => item.id === agent.id)
  if (index !== -1) {
    agents.value[index] = {
      ...agents.value[index],
      enabled: result.enabled
    }
  }
}
</script>
