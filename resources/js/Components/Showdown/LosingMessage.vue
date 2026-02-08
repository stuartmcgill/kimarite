<script setup lang="ts">
import { computed } from 'vue'

const imageUrl = computed(
  () =>
    new URL(`../../../images/showdown/facedown-card.png`, import.meta.url).href,
)
</script>

<template>
  <div
    class="p-8 w-60 h-[416px] flex items-center justify-center text-xl"
  >
    No more cards
  </div>
</template>
