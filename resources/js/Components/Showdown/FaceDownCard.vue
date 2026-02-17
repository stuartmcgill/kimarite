<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'

const store = useShowdownStore()
</script>

<template>
  <div
    class="w-full sm:w-60 h-48.5 sm:h-104 sm:bg-coral-100 rounded-2xl border-4 border-coral-200 sm:[mask-image:none] overflow-hidden"
  >
    <img
      :src="store.game!.startImage"
      alt="Game logo"
      class="w-full h-full object-cover sm:object-contain"
      :class="[
        store.thinking ? 'animate-pulse' : '',
        'sm:mask-[radial-gradient(ellipse_60%_80%_at_50%_50%,black_50%,transparent_85%)]',
      ]"
    />
  </div>
</template>
