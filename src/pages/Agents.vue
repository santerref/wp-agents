<template>
  <table class="wp-list-table widefat plugins">
    <thead>
    <tr>
      <td id="cb" class="manage-column column-cb check-column">
        <input id="select-all" type="checkbox">
        <label for="select-all"><span class="tw:sr-only">Select All</span></label>
      </td>
      <th scope="col" id="name" class="manage-column column-name column-primary">Agent</th>
      <th scope="col" id="description" class="manage-column column-description">Description</th>
      <th scope="col" id="description" class="manage-column column-description">Provider</th>
      <th scope="col" id="description" class="manage-column column-description">Tools</th>
      <th scope="col" id="description" class="manage-column column-description">Hooks</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="agent in agents" :id="agent.id">
      <th scope="row" class="check-column tw:pl-[6px]">
        <label class="label-covers-full-cell"
               :for="`checkbox_${agent.id}`"><span
            class="tw:sr-only">Select {{ agent.name }}</span>
        </label>
        <input type="checkbox" class="tw:mt-[4px] tw:ml-[8px]" name="checked[]"
               :value="agent.id"
               :id="`checkbox_${agent.id}`">
      </th>
      <td class="tw:p-[10px]">{{ agent.name }}</td>
      <td class="tw:p-[10px]">
        <div class="plugin-description">
          <p>{{ agent.description }}</p>
        </div>
        <div class="plugin-version-author-uri">
          Version {{ agent.version }}
        </div>
      </td>
      <td class="tw:p-[10px]">
        OpenAI
      </td>
      <td v-html="agent.tools.join(',<br>')"></td>
      <td v-html="agent.hooks.join(',<br>')"></td>
    </tr>
    </tbody>
  </table>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";

const agents = ref([])

onMounted(async () => {
  const res = await fetch('/wp-json/wp-agents/v1/agents', {
    credentials: 'same-origin'
  })
  agents.value = await res.json()
})
</script>
