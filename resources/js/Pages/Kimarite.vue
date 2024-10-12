<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useKimariteStore } from '@/stores/kimarite'
import LoadingIndicator from '@/Components/App/LoadingIndicator.vue'
import Select from 'primevue/select'
import MultiSelect from 'primevue/multiselect'
import { capitalize, ref } from 'vue'
import 'primeicons/primeicons.css'
import Button from 'primevue/button'
import IftaLabel from 'primevue/iftalabel'
import Listbox from 'primevue/listbox'
import { computed } from '@vue/reactivity'
import Graph from '@/Components/Kimarite/Graph.vue'

const props = defineProps<{
  types: string[]
  availableBashos: string[]
}>()

const typeOptions = props.types.map(type => capitalize(type))

const bashoOptions = props.availableBashos.map(
  basho => `${basho.substring(0, 4)}-${basho.substring(4, 6)}`,
)

const divisionOptions = computed(() => [
  'Makuuchi',
  'Juryo',
  'Makushita',
  'Sandanme',
  'Jonidan',
  'Jonokuchi',
])

const store = useKimariteStore()

const selectedTypes = ref([])
const selectedDivisions = ref([])
const from = ref(null)
const to = ref(null)

const clearSelectedTypes = () => (selectedTypes.value = [])

const enableShow = computed(
  () => selectedTypes.value.length > 0 && selectedDivisions.value.length > 0,
)

const refreshGraph = () => {
  store.fetchCounts(
    selectedTypes.value,
    selectedDivisions.value,
    from.value!,
    to.value!,
  )
}
</script>

<template>
  <body class="bg-tan-200">
    <Head title="Kimarite visualisation" />

    <div class="mx-auto flex flex-col w-full min-h-screen sm:max-w-7xl">
      <div class="p-4 w-full flex flex-col gap-4 justify-center text-center">
        <h1 class="mb-4 font-semibold">Kimarite visualisation</h1>
        <div
          class="p-6 w-full grid lg:grid-cols-[auto,200px] gap-x-12 gap-y-4 bg-tan-50 rounded shadow"
        >
          <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col lg:flex-row gap-3 w-full">
              <MultiSelect
                v-model="selectedTypes"
                :options="typeOptions"
                filter
                placeholder="Select kimarite"
                display="chip"
                class="flex w-full sm:max-w-[800px]"
              />
              <Button
                icon="pi pi-times"
                outlined
                severity="danger"
                label="Clear"
                :disabled="selectedTypes.length === 0"
                @click="clearSelectedTypes"
              />
            </div>
            <div class="flex gap-2">
              <IftaLabel>
                <Select
                  v-model="from"
                  inputId="from-basho"
                  :options="bashoOptions"
                  filter
                  placeholder="Select start basho"
                />
                <label for="from-basho">From</label>
              </IftaLabel>
              <IftaLabel>
                <Select
                  v-model="to"
                  inputId="to-basho"
                  :options="bashoOptions"
                  filter
                  placeholder="Select end basho"
                  class=""
                />
                <label for="to-basho">To</label>
              </IftaLabel>
            </div>
            <div class="mt-auto flex items-center gap-4 justify-start">
              <Button
                :disabled="!enableShow"
                raised
                label="Show graph"
                @click="refreshGraph"
              />
              <div v-if="enableShow">
                {{ selectedTypes.length }} kimarite,
                {{ selectedDivisions.length }} divisions
              </div>
              <div v-else class="text-red-700">
                Select one or more kimarite and divisions
              </div>
              <LoadingIndicator v-if="store.loading" />
            </div>
          </div>
          <Listbox
            v-model="selectedDivisions"
            :options="divisionOptions"
            multiple
            class=""
          />
        </div>
        <div
          class="p-6 w-full grid lg:grid-cols-[auto,200px] gap-x-12 gap-y-4 bg-tan-50 rounded shadow"
        >
          <Graph />
        </div>
      </div>
      <div class="mt-auto p-4 flex text-sm">
        <a
          class="flex items-center hover:opacity-80"
          href="https://www.flaticon.com/authors/vitaly-gorbachev"
          title="Vitaly Gorbachev"
          ><img src="favicon.ico" class="mx-1 h-4 w-4" />Dohyo icon by Vitaly
          Gorbachev - Flaticon</a
        >
      </div>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>
