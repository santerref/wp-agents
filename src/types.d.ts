export interface Agent {
    id: string
    model: string
    provider: string
    name: string
    description: string
    hooks: string[]
    tools: string[]
    version: string
    enabled: boolean
}
