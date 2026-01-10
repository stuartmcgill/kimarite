<script setup lang="ts">
import { useShowdownStore } from '@/stores/showdown'
import { Game as GameType } from '@/types/showdown'

const props = defineProps<{ game: GameType }>()

const store = useShowdownStore()
store.init(props.game)
</script>

<template>
  <div class="flex flex-col gap-4 w-full">The game</div>
  <div>{{ store.game }}</div>
</template>
