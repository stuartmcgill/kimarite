<script setup lang="ts">
import { computed } from 'vue'

const imageUrl = computed(
  () =>
    new URL(`../../../images/showdown/facedown-card.png`, import.meta.url).href,
)
</script>

<template>
  <div
    class="p-4 w-60 h-[445px] flex items-center justify-center bg-coral-100 rounded"
  >
    <img :src="imageUrl" alt="Dohyo logo" class="object-cover" />
  </div>
</template>
