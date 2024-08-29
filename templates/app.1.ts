import './bootstrap'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import * as Sentry from '@sentry/vue'

/**
 * Without Layout.
 */
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    Sentry.init({
      app,
      dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
      tunnel: '/api/sentry-tunnel',
      tracesSampleRate: 0
    })

    app.use(plugin).mount(el)
  }
})

/**
 * With Layout.
 */
// import Layout from './Layout'
//
// createInertiaApp({
//   resolve: (name) => {
//     const page = resolvePageComponent(
//       `./Pages/${name}.vue`,
//       import.meta.glob<DefineComponent>('./Pages/**/*.vue')
//     )
//
//     page.then((module) => {
//       module.default.layout = module.default.layout || Layout
//     })
//
//     return page
//   },
//   setup({ el, App, props, plugin }) {
//     const app = createApp({ render: () => h(App, props) })
//
//     Sentry.init({
//       app,
//       dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
//       tunnel: '/api/sentry-tunnel',
//       tracesSampleRate: 0
//     })
//
//     app.use(plugin).mount(el)
//   }
// })
