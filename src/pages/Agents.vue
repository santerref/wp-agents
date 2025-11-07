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
    <tr v-for="agent in agents" :id="agent.id">
      <td class="tw:p-[10px]">
        <span class="tw:mb-1 tw:inline-block">{{ agent.name }}</span>
        <div class="row-actions visible">
          <span class="activate">
            <button
                class="tw:border-none tw:bg-transparent tw:p-0 tw:m-0 tw:cursor-pointer tw:text-blue-wp">Activate</button>
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
  const res = await fetch('/wp-json/wp-agents/v1/agents', {
    credentials: 'same-origin'
  })
  agents.value = await res.json()
})

const activate = (agent: Agent) => {

}

const deactivate = (agent: Agent) => {

}
</script>
