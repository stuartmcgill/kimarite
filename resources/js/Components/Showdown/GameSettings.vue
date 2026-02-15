<script setup lang="ts">
import Badge from 'primevue/badge'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Slider from 'primevue/slider'
import { useSettings } from '@/Composables/showdown/useSettings'
import { GameSettings as GameSettingsType } from '@/types/showdown'

const emit = defineEmits<{
  (e: 'start', settings: GameSettingsType): void
}>()

const difficultyLabelsMap = new Map<number, string>([
  [0, 'Juryo'],
  [20, 'Maegashira'],
  [40, 'Komusubi'],
  [60, 'Sekiwake'],
  [80, 'Ozeki'],
  [100, 'Yokozuna'],
])

const { playerName, difficultyLevel, difficultyRank, difficultySeverity } =
  useSettings(difficultyLabelsMap)

const start = () => {
  localStorage.setItem('playerName', playerName.value)
  localStorage.setItem('difficultyLevel', difficultyLevel.value as string)

  emit('start', {
    playerName: playerName.value,
    difficultyLevel: difficultyLevel.value,
  })
}
</script>

<template>
  <form @submit.prevent="start">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-6">
        <label for="player-name" class="w-32">Player name</label>
        <InputText id="player-name" v-model="playerName" />
      </div>
      <div class="flex items-center gap-6">
        <label for="difficulty-level" class="w-32">Difficulty</label>
        <Slider
          id="difficulty-level"
          v-model="difficultyLevel"
          :step="20"
          class="w-56"
        />
        <Badge :severity="difficultySeverity">{{ difficultyRank }}</Badge>
      </div>
      <Button type="submit">Start game</Button>
    </div>
  </form>
</template>
