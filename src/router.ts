import {createRouter, createWebHashHistory} from 'vue-router'

import AgentsPage from './pages/Agents.vue'
import ToolsPage from './pages/Tools.vue'
import SettingsPage from './pages/Settings.vue'

const routes = [
    {path: '/', component: AgentsPage},
    {path: '/tools', component: ToolsPage},
    {path: '/settings', component: SettingsPage},
]

export const router = createRouter({
    history: createWebHashHistory(),
    routes,
})
