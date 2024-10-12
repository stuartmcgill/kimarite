<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useKimariteStore } from '@/stores/kimarite'
import LoadingIndicator from '@/Components/App/LoadingIndicator.vue'

const props = defineProps<{
  types: string[]
}>()

const store = useKimariteStore()
store.fetchStats(['yorikiri', 'oshidashi'], '202401', '202409')
</script>

<template>
  <body class="bg-tan-200 flex min-h-screen">
  <Head title="Kimarite visualisation" />
  <div class="mx-auto max-w-5xl">
    <div class="p-4 w-full flex flex-col justify-center text-center">
      <h1 class="mb-8 text-5xl font-semibold">Kimarite visualisation</h1>
      <div class="p-8 w-full bg-tan-50 rounded shadow">
        <select>

        </select>
        <LoadingIndicator v-if="store.loading" />
      </div>
     </div>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>