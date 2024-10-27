<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useRefreshStore } from '@/stores/refresh'
import LoadingIndicator from '@/Components/App/LoadingIndicator.vue'

const store = useRefreshStore()

const rebuild = () => {
  if (window.confirm('Are you SURE you want to rebuild the Kimarite DB')) {
    store.refresh()
  }
}

const refreshBashoPercentages = () => {
  if (
    window.confirm('Are you SURE you want to refresh the basho percentage data')
  ) {
    store.refreshBashoPercentages()
  }
}
</script>

<template>
  <Head title="Rebuild Kimarite statistics" />
  <div class="p-4 max-w-2xl container flex justify-center">
    <div class="flex flex-col gap-2 items-center">
      <div>
        This will rebuild the DB from the Sumo API site,
        <strong>destroying any existing data</strong>.
      </div>
      <div class="flex gap-4">
        <LoadingIndicator v-if="store.loading" />
        <template v-else>
          <button
            class="p-2 flex text-white justify-center bg-red-500 rounded hover:opacity-80"
            @click="rebuild"
          >
            Rebuild
          </button>
          <button
            class="p-2 flex text-white justify-center bg-orange-500 rounded hover:opacity-80"
            @click="refreshBashoPercentages"
          >
            Refresh percentages
          </button>
        </template>
      </div>
    </div>
  </div>
</template>
