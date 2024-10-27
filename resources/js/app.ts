import './bootstrap'
import '../css/app.css'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import Tooltip from 'primevue/tooltip'
import Aura from '@primevue/themes/aura'
import { definePreset } from '@primevue/themes'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: title => `${title} - ${appName}`,
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()

    const preset = definePreset(Aura, {
      semantic: {
        primary: {
          50: '{teal.50}',
          100: '{teal.100}',
          200: '{teal.200}',
          300: '{teal.300}',
          400: '{teal.400}',
          500: '{teal.500}',
          600: '{teal.600}',
          700: '{teal.700}',
          800: '{teal.800}',
          900: '{teal.900}',
          950: '{teal.950}',
        },
        colorScheme: {
          light: {
            primary: {
              color: '{primary.700}',
              contrastColor: '#ffffff',
              hoverColor: '{primary.900}',
            },
            highlight: {
              background: '{primary.100}',
              focusBackground: '{primary.200}',
            },
          },
        },
      },
    })

    const primeOptions = {
      theme: {
        preset: preset,
      },
    }

    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(pinia)
      .use(PrimeVue, primeOptions)
      .directive('tooltip', Tooltip)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
