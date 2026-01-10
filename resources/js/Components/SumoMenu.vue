<script setup lang="ts">
import Menubar from 'primevue/menubar'

const menuItems: object[] = [
  {
    label: 'Kimarite trends',
    icon: 'pi pi-fw pi-chart-bar',
    url: '/#',
    class: 'bg-gray-50 border rounded-sm',
  },
  {
    label: 'Head-to-head',
    icon: 'pi pi-fw pi-users',
    url: '/head2head',
  },
  {
    label: 'Sumo showdown!',
    icon: 'pi pi-fw pi-crown',
    url: '/showdown',
  },
]
</script>

<template>
  <Menubar class="my-4 order-1 md:order-2 w-full md:w-96" :model="menuItems" />
</template>
