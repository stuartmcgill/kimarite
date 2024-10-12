<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useKimariteStore } from '@/stores/kimarite'
import LoadingIndicator from '@/Components/App/LoadingIndicator.vue'
import MultiSelect from 'primevue/multiselect'
import { capitalize, ref } from 'vue'
import 'primeicons/primeicons.css'
import Button from 'primevue/button'

const props = defineProps<{
  types: string[]
}>()

console.log(props.types)

const typeOptions = props.types.map(type => capitalize(type))

const store = useKimariteStore()
store.fetchStats(['yorikiri', 'oshidashi'], '202401', '202409')

const selectedTypes = ref([])

const clearSelectedTypes = () => (selectedTypes.value = [])
</script>

<template>
  <body class="bg-tan-200 flex min-h-screen">
    <Head title="Kimarite visualisation" />
    <div class="mx-auto">
      <div class="p-4 w-full flex flex-col justify-center text-center">
        <h1 class="mb-8 font-semibold">Kimarite visualisation</h1>
        <div class="p-8 w-full bg-tan-50 rounded shadow">
          <div class="flex gap-4">
            <MultiSelect
              v-model="selectedTypes"
              :options="typeOptions"
              filter
              placeholder="Select kimarite"
              class="w-full"
            />
            <Button label="Clear selection" @click="clearSelectedTypes" />
          </div>
          <div class="mt-4 flex gap-4">
            <Select
              v-model="selectedTypes"
              :options="bashoOptions"
              filter
              placeholder="Select start basho"
              class="w-full"
            />
          </div>

          <LoadingIndicator v-if="store.loading" />
        </div>
      </div>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>
