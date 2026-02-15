<script setup lang="ts">
import Badge from 'primevue/badge'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Slider from 'primevue/slider'
import { useSettings } from '@/Composables/showdown/useSettings'
import { GameType, GameSettings as GameSettingsType } from '@/types/showdown'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ game: GameType }>()

const emit = defineEmits<{
  (e: 'start', settings: GameSettingsType): void
}>()

const store = useShowdownStore()

const {
  playerName,
  numCards,
  difficultyLevel,
  difficultyRank,
  difficultySeverity,
} = useSettings(store.difficultyLabelsMap)

const start = () => {
  localStorage.setItem('playerName', playerName.value)
  localStorage.setItem('numCards', numCards.value.toString())
  localStorage.setItem('difficultyLevel', difficultyLevel.value.toString())

  emit('start', {
    playerName: playerName.value,
    numCards: numCards.value,
    difficultyLevel: difficultyLevel.value,
  })
}
</script>

<template>
  <form @submit.prevent="start">
    <div class="max-w-md flex flex-col gap-4">
      <div class="flex items-center gap-6">
        <label for="player-name" class="">Player name</label>
        <InputText id="player-name" v-model="playerName" />
      </div>
      <div class="flex items-center gap-6">
        <label for="player-name" class="">Number of cards</label>
        <InputNumber
          v-model="numCards"
          inputId="horizontal-buttons"
          showButtons
          buttonLayout="horizontal"
          :step="2"
          fluid
          :min="2"
          :max="props.game.cards.length"
        >
          <template #incrementbuttonicon>
            <span class="pi pi-plus" />
          </template>
          <template #decrementbuttonicon>
            <span class="pi pi-minus" />
          </template>
        </InputNumber>
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
