<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useKimariteStore } from '@/stores/kimarite'
import LoadingIndicator from '@/Components/App/LoadingIndicator.vue'
import Select from 'primevue/select'
import MultiSelect from 'primevue/multiselect'
import { capitalize, ref } from 'vue'
import type { Ref } from 'vue'
import 'primeicons/primeicons.css'
import Button from 'primevue/button'
import IftaLabel from 'primevue/iftalabel'
import Listbox from 'primevue/listbox'
import { computed } from '@vue/reactivity'
import Graph from '@/Components/Kimarite/Graph.vue'
import { formatBashoId } from '@/Composables/utils'

const props = defineProps<{
  types: string[]
  availableBashos: string[]
}>()

const typeOptions = props.types.map(type => capitalize(type))

const bashoOptions = props.availableBashos.map(bashoId =>
  formatBashoId(bashoId),
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

const selectedTypes: Ref<string[]> = ref([])
const from: Ref<string | null> = ref(null)
const to: Ref<string | null> = ref(null)
const selectedDivisions: Ref<string[]> = ref([])

// Initial values
const resetCriteria = () => {
  selectedTypes.value = ['Yorikiri', 'Oshidashi']
  selectedDivisions.value = divisionOptions.value
  from.value = '1991-07' // All-division data becomes available
}
resetCriteria()

const clearSelectedTypes = () => (selectedTypes.value = [])

const validationMessage = computed(() => {
  const errorFields = []

  if (selectedTypes.value.length === 0) {
    errorFields.push('Kimarite')
  }

  if (selectedDivisions.value.length === 0) {
    errorFields.push('Divisions')
  }

  if (!from.value) {
    errorFields.push('From date')
  }

  if (errorFields.length === 0) {
    return ''
  }

  return `Please select: ${errorFields.join(', ')}`
})

const validated = computed(() => validationMessage.value.length === 0)
const errorMessage = ref('')

const refreshGraph = async () => {
  errorMessage.value = ''

  if (!validated.value) {
  }

  try {
    await store.fetchCounts(
      selectedTypes.value,
      selectedDivisions.value,
      from.value!,
      to.value!,
    )
  } catch (e: any) {
    if (e.isAxiosError && e.response && e.response.status === 422) {
      const messages = Object.entries(e.response.data.messages).map(
        ([key, value]) => value,
      )
      errorMessage.value = messages.join(', ')
    } else {
      console.error(e)
    }
  }
}

refreshGraph()
</script>

<template>
  <body class="bg-tan-200">
    <Head title="Kimarite trends" />

    <div class="mx-auto flex flex-col w-full min-h-screen sm:max-w-7xl">
      <div class="p-4 w-full flex flex-col gap-4 justify-center text-center">
        <h1 class="mb-4 font-semibold">Kimarite trends</h1>
        <div
          class="p-6 w-full grid lg:grid-cols-[auto,200px] gap-x-12 gap-y-4 bg-tan-50 rounded shadow"
        >
          <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col lg:flex-row gap-3 w-full">
              <MultiSelect
                v-model="selectedTypes"
                inputId="kimarite"
                :options="typeOptions"
                editable
                placeholder="Select kimarite"
                display="chip"
                class="flex w-full sm:max-w-[800px]"
              />
              <Button
                icon="pi pi-times"
                outlined
                severity="contrast"
                label="Clear"
                :disabled="selectedTypes.length === 0"
                @click="clearSelectedTypes"
              />
            </div>
            <div class="flex items-center gap-2">
              <IftaLabel>
                <Select
                  v-model="from"
                  inputId="from-basho"
                  :options="bashoOptions"
                  editable
                  class="w-40"
                />
                <label for="from-basho">From</label>
              </IftaLabel>
              <IftaLabel>
                <Select
                  v-model="to"
                  inputId="to-basho"
                  :options="bashoOptions"
                  editable
                  showClear
                  class="w-40"
                />
                <label for="to-basho">To</label>
              </IftaLabel>
              <i
                class="pi pi-info-circle"
                style="font-size: 1rem"
                v-tooltip="
                  'Before 1991-07 data is only available for the top 2 divisions'
                "
              />
            </div>
            <div class="mt-auto flex items-center gap-4 justify-start">
              <Button
                :disabled="!validated"
                raised
                label="Refresh graph"
                @click="refreshGraph"
              />
              <div v-if="validated">
                {{ selectedTypes.length }} kimarite,
                {{ selectedDivisions.length }} divisions
              </div>
              <div v-else class="text-orange-800">
                {{ validationMessage }}
              </div>
              <LoadingIndicator v-if="store.loading" />
              <Button
                class="ml-auto"
                icon="pi pi-undo"
                outlined
                severity="contrast"
                label="Reset"
                @click="resetCriteria"
              />
            </div>
            <div v-if="errorMessage" class="flex justify-start text-orange-800">
              {{ errorMessage }}
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
      <div class="mt-auto p-4 flex flex-col gap-2 text-sm">
        <div class="flex">
          Data from
          <a
            class="ml-1 flex items-center hover:opacity-70"
            href="https://www.sumo-api.com/"
            title="Sumo API"
            target="_blank"
            >Sumo API</a
          >
        </div>
        <div class="flex items-center">
          <img src="favicon.ico" class="mx-1 h-4 w-4" />
          Dohyo icon by
          <a
            class="ml-1 flex items-center hover:opacity-70"
            href="https://www.flaticon.com/authors/vitaly-gorbachev"
            title="Vitaly Gorbachev"
            target="_blank"
            >Vitaly Gorbachev - Flaticon</a
          >
          <a
            class="ml-auto hover:opacity-70"
            href="https://github.com/stuartmcgill/kimarite"
            title="Github repository"
            target="_blank"
          >
            <i class="pi pi-github" style="font-size: 1.5rem" />
          </a>
        </div>
      </div>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>
