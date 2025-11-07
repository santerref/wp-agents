import { createMemoryHistory, createRouter } from 'vue-router'

import AgentsPage from './pages/Agents.vue'
import ToolsPage from './pages/Tools.vue'

const routes = [
    { path: '/', component: AgentsPage },
    { path: '/tools', component: ToolsPage },
]

export const router = createRouter({
    history: createMemoryHistory(),
    routes,
})
