<script setup lang="ts">
import { computed } from 'vue'
import Menubar from 'primevue/menubar'

const props = withDefaults(
  defineProps<{
    forceHamburger?: boolean
  }>(),
  {
    forceHamburger: false,
  },
)

const menuItems = computed(() => {
  const currentRoute = route().current() || ''

  return [
    {
      label: 'Kimarite trends',
      icon: 'pi pi-fw pi-chart-bar',
      url: '/#',
      class:
        currentRoute === 'kimarite.show'
          ? 'bg-blue-100 border rounded-sm'
          : 'bg-gray-50 border rounded-sm',
    },
    {
      label: 'Head-to-head',
      icon: 'pi pi-fw pi-users',
      url: '/head2head',
      class: currentRoute === 'head2head.view' ? 'bg-blue-100' : '',
    },
    {
      label: 'Sumo showdown!',
      icon: 'pi pi-fw pi-crown',
      url: '/showdown',
      class: currentRoute === 'showdown.view' ? 'bg-blue-100' : '',
    },
  ]
})
</script>

<template>
  <Menubar
    class="my-4 order-1 md:order-2 w-full md:w-96"
    :class="{ 'force-hamburger': props.forceHamburger }"
    :model="menuItems"
  />
</template>

<style>
/* Apply mobile styles at all screen sizes */
@media screen and (min-width: 0) {
  .force-hamburger .p-menubar-root-list {
    display: none;
  }

  .force-hamburger .p-menubar-button {
    display: flex;
  }
}
</style>
