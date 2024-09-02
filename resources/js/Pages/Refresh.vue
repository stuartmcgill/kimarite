<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useRefreshStore } from '@/stores/refresh'

const store = useRefreshStore()
</script>

<template>
  <Head title="Refresh Kimarite statistics" />
  <div class="p-4 max-w-2xl container flex justify-center">
    <div class="flex flex-col gap-2">
      <div>Are you sure you want to refresh the Kimarite database?</div>
      <button
        class="p-2 flex text-white justify-center bg-red-500 rounded hover:opacity-80"
        @click="store.refresh"
      >
        Refresh
      </button>
    </div>
  </div>
</template>
