import { sentryVitePlugin } from '@sentry/vite-plugin'
import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'

const env = loadEnv('all', process.cwd())

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.ts'],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    sentryVitePlugin({
      org: 'norman-huth',
      project: env.VITE_SENTRY_PROJECT,
      telemetry: false,
      release: {
        name: new Date().toISOString()
      },
      authToken: env.VITE_SENTRY_AUTH_TOKEN.trim()
    })
  ],
  resolve: {
    alias: {
      '@': resolve('./resources/js')
    }
  }
})
