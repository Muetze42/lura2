import './bootstrap'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

/**
 * Without Layout.
 */
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

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
//     app.use(plugin).mount(el)
//   }
// })
