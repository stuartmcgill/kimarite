<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import SelectedWrestler from '@/Components/Head2Head/SelectedWrestler.vue'
import Head2HeadCollection from '@/types/head2head/head2headCollection'
import { ref, Ref } from 'vue'
import Head2HeadCard from '@/Components/Head2Head/Head2HeadCard.vue'
import { WrestlerRecord } from '@/types/head2head/wrestlerRecord'

const props = defineProps<{ wrestlers: WrestlerRecord[] }>()

const selectedWrestler: Ref<WrestlerRecord | null> = ref(null)
const wrestlers: Ref<Head2HeadCollection | null> = ref(null)
const loading = ref(false)

const onHead2HeadSelected = async (id: number) => {
  if (!wrestlers.value) {
    return
  }

  loading.value = true

  try {
    const response = await fetch('/head2head/' + id)
    const json = await response.json()

    wrestlers.value.refreshHead2HeadData(JSON.parse(json))
    wrestlers.value.sort()
    selectedWrestler.value = wrestlers.value.find(id)
  } finally {
    loading.value = false
  }
}
wrestlers.value = new Head2HeadCollection(props.wrestlers)
</script>

<template>
  <Head title="Sumo Makuuchi head-to-heads" />
  <div class="container" id="wrestlers" data-wrestlers="{{ wrestlers }}">
    <div class="h-100 p-5 m-3 text-bg-dark rounded-3">
      <div class="row">
        <div class="col">
          <h2>Makuuchi head-to-heads</h2>
        </div>
        <a class="col text-end header-link hover-effect" href=".."
          >Kimarite trends</a
        >
      </div>
      <p class="lead">
        Select a rikishi to see his records against the others currently in
        Makuuchi
        <a
          class="header-link hover-effect"
          href="https://sumo-api.com"
          target="_blank"
          >Data from Sumo API</a
        >
      </p>
    </div>
    <div>
      <div class="row">
        <div class="col mb-3 mb-sm-0">
          <SelectedWrestler
            v-if="selectedWrestler"
            :selected="selectedWrestler"
            :test="null"
          />
        </div>
        <div v-show="loading" class="spinner-border m-3" role="status">
          <span class="sr-only"></span>
        </div>
      </div>
      <div class="row m-3">
        <Head2HeadCard
          v-for="head2head in wrestlers?.head2heads"
          :head2headRecord="head2head"
          :selected-wrestler="selectedWrestler"
          @selected="onHead2HeadSelected"
        />
      </div>
    </div>
    <div class="my-3 text-body-secondary">
      <a href="https://github.com/stuartmcgill/sumo-head2head"
        >GitHub repository</a
      >
    </div>
  </div>
</template>
<style scoped>
.hover-effect:hover {
  color: #b0a8a6;
}
</style>
