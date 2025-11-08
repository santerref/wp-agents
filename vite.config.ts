import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import fs from 'fs'

let exitHandlersBound = false
const hotFile = './.hot'

export default defineConfig({
    server: {
        host: true,
        cors: {
            origin: '*',
        },
    },
    build: {
        manifest: true,
        modulePreload: {
            polyfill: false
        },
        rollupOptions: {
            input: './src/main.ts'
        }
    },
    plugins: [
        vue(),
        tailwindcss(),
        {
            name: 'wp-agents-wp-hot',
            configureServer(server) {
                server.httpServer?.once('listening', () => {
                    fs.writeFileSync(hotFile, 'http://localhost:5173')
                })
                server.httpServer?.once('close', () => {
                    if (fs.existsSync(hotFile)) fs.unlinkSync(hotFile)
                })

                if (!exitHandlersBound) {
                    const clean = () => {
                        if (fs.existsSync(hotFile)) {
                            fs.rmSync(hotFile)
                        }
                    }

                    process.on('exit', clean)
                    process.on('SIGINT', () => process.exit())
                    process.on('SIGTERM', () => process.exit())
                    process.on('SIGHUP', () => process.exit())

                    exitHandlersBound = true
                }
            }
        }
    ],
})
