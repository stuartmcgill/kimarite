<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'

const store = useShowdownStore()
</script>
<template>
  <div
    class="p-2 sm:p-8 w-full sm:w-60 h-48.5 sm:h-104 flex flex-col justify-center text-xl"
  >
    <div>No more cards</div>
    <div v-if="store.winner" class="mt-4 sm:mt-12 text-3xl font-bold">
      {{ store.winner.name }} kachikoshi!
    </div>
  </div>
</template>
