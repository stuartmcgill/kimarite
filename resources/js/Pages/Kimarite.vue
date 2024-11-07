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
import Checkbox from 'primevue/checkbox'
import Menubar from 'primevue/menubar'
import { computed } from '@vue/reactivity'
import Graph from '@/Components/Kimarite/Graph.vue'
import { formatBashoId } from '@/Composables/utils'

const props = defineProps<{
  types: string[]
  availableBashos: string[]
}>()

const menuItems: object[] = [
  {
    label: 'Kimarite trends',
    icon: 'pi pi-fw pi-chart-bar',
    url: '#',
    class: 'bg-gray-50 border rounded',
  },
  {
    label: 'Head-to-head',
    icon: 'pi pi-fw pi-users',
    url: '/head2head',
  },
]

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
  store.displayAsPercent = true

  refreshGraph()
}

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

  if (errorFields.length > 0) {
    return `Please select: ${errorFields.join(', ')}`
  }

  // Now do some more sophisticated validation
  if (!bashoOptions.includes(from.value!)) {
    return 'Select a valid From date'
  }

  if (to.value && !bashoOptions.includes(to.value)) {
    return 'Select a valid To date'
  }

  return ''
})

const validated = computed(() => validationMessage.value.length === 0)
const errorMessage = ref('')

const refreshGraph = async () => {
  errorMessage.value = ''

  if (!validated.value) {
    return
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

resetCriteria()
</script>

<template>
  <body class="bg-coral-100">
    <Head title="Kimarite trends" />

    <div class="mx-auto flex flex-col w-full min-h-screen sm:max-w-7xl">
      <div
        class="px-4 md:p-4 w-full flex flex-col gap-4 justify-center text-center"
      >
        <div
          class="w-full flex flex-col md:flex-row items-center justify-between gap-4"
        >
          <h1
            class="mx-auto order-2 md:order-1 mb-4 font-semibold text-primary-900"
          >
            Kimarite trends
          </h1>
          <Menubar
            class="my-4 order-1 md:order-2 w-full md:w-96"
            :model="menuItems"
          />
        </div>

        <div class="p-6 w-full bg-white rounded shadow">
          <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col lg:flex-row gap-3 w-full">
              <MultiSelect
                v-model="selectedTypes"
                inputId="kimarite"
                :options="typeOptions"
                editable
                placeholder="Select kimarite"
                display="chip"
                class="flex w-full"
                @change="refreshGraph"
              />
              <Button
                icon="pi pi-times"
                outlined
                severity="contrast"
                label="Clear"
                class="max-w-40 ml-auto"
                :disabled="selectedTypes.length === 0"
                @click="clearSelectedTypes"
              />
            </div>
            <!-- From, to, and divisions -->
            <div
              class="flex flex-col sm:flex-row flex-wrap sm:items-center gap-2"
            >
              <!-- From and to -->
              <div
                class="flex flex-col sm:flex-row flex-wrap sm:items-center gap-2"
              >
                <IftaLabel>
                  <Select
                    v-model="from"
                    inputId="from-basho"
                    :options="bashoOptions"
                    editable
                    class="w-40"
                    @change="refreshGraph"
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
                    @change="refreshGraph"
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
              <!-- Divisions -->
              <IftaLabel class="sm:ml-4 md:ml-auto">
                <MultiSelect
                  inputId="divisions"
                  v-model="selectedDivisions"
                  :options="divisionOptions"
                  class="min-w-40 max-w-40 md:max-w-72 lg:max-w-none"
                  @change="refreshGraph"
                />
                <label for="divisions">Divisions</label>
              </IftaLabel>
            </div>

            <!-- Percentage and reset -->
            <div class="mt-auto flex flex-col sm:flex-row items-center gap-x-4">
              <div v-if="!validated" class="text-orange-800 flex w-full">
                {{ validationMessage }}
              </div>
              <div class="flex items-center gap-2 w-full">
                <div class="flex items-center gap-2 w-full">
                  <label for="displayAsPercent">Display as percentage</label>
                  <Checkbox
                    v-model="store.displayAsPercent"
                    name="displayAsPercent"
                    binary
                  />
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
            </div>
            <div v-if="errorMessage" class="flex justify-start text-orange-800">
              {{ errorMessage }}
            </div>
          </div>
        </div>
        <div
          class="p-6 w-full grid lg:grid-cols-[auto,200px] gap-x-12 gap-y-4 bg-white rounded shadow"
        >
          <Graph />
        </div>
      </div>
      <footer
        class="mt-auto p-4 flex flex-col sm:flex-row items-center justify-between gap-2"
      >
        <div class="flex text-lg">
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
        </div>
        <a
          class="hover:opacity-70"
          href="https://github.com/stuartmcgill/kimarite"
          title="Github repository"
          target="_blank"
        >
          <i class="pi pi-github" style="font-size: 1.5rem" />
        </a>
      </footer>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>
