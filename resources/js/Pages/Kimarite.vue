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

const props = defineProps<{
  types: string[]
  availableBashos: string[]
}>()

console.log(props.availableBashos)

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
store.fetchStats(['yorikiri', 'oshidashi'], '202401', '202409')

const selectedTypes = ref([])
const selectedDivisions = ref([])
const selectedFrom = ref(null)
const selectedTo = ref(null)

const clearSelectedTypes = () => (selectedTypes.value = [])
</script>

<template>
  <body class="bg-tan-200 flex min-h-screen">
    <Head title="Kimarite visualisation" />

    <div class="mx-auto w-full max-w-7xl">
      <div class="p-4 w-full flex flex-col justify-center text-center">
        <h1 class="mb-8 font-semibold">Kimarite visualisation</h1>
        <div
          class="p-6 w-full grid md:grid-cols-2 gap-x-12 gap-y-4 bg-tan-50 rounded shadow"
        >
          <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col md:flex-row gap-4 w-full">
              <MultiSelect
                v-model="selectedTypes"
                :options="typeOptions"
                filter
                placeholder="Select kimarite"
                display="chip"
                class="w-full"
              />
              <Button class="" label="Clear" @click="clearSelectedTypes" />
            </div>
            <div class="flex gap-2">
              <IftaLabel>
                <Select
                  v-model="selectedFrom"
                  inputId="from-basho"
                  :options="bashoOptions"
                  filter
                  placeholder="Select start basho"
                />
                <label for="from-basho">From</label>
              </IftaLabel>
              <IftaLabel>
                <Select
                  v-model="selectedTo"
                  inputId="to-basho"
                  :options="bashoOptions"
                  filter
                  placeholder="Select end basho"
                  class=""
                />
                <label for="to-basho">To</label>
              </IftaLabel>
            </div>
          </div>
          <Listbox
            v-model="selectedDivisions"
            :options="divisionOptions"
            multiple
            class=""
          />
        </div>
      </div>
    </div>
  </body>
</template>
<style>
@import '@/../css/kimarite.css';
</style>
