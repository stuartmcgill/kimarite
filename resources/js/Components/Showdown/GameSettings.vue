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
  <div class="mx-auto max-w-lg flex flex-col gap-4 justify-center">
    <img
      v-if="props.game.startImage"
      :src="props.game.startImage"
      :alt="props.game.name || 'Game logo'"
      class=""
    />
    <form class="mx-auto" @submit.prevent="start">
      <h1
        v-if="!props.game.startImage"
        class="mb-8 kimarite-header font-semibold text-primary-900"
      >
        {{ props.game.name }}
      </h1>
      <div
        class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-4 items-center [&>*:nth-child(odd)]:text-left"
      >
        <label for="player-name">Player name</label>
        <InputText id="player-name" v-model="playerName" />

        <label for="num-cards">Number of cards</label>
        <InputNumber
          v-model="numCards"
          inputId="num-cards"
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

        <label for="difficulty-level">Difficulty</label>
        <div class="flex items-center gap-6">
          <Slider
            id="difficulty-level"
            v-model="difficultyLevel"
            :step="20"
            class="flex-1"
          />
          <Badge :severity="difficultySeverity">{{ difficultyRank }}</Badge>
        </div>

        <div class="col-span-2">
          <Button type="submit" class="w-full">Start game</Button>
        </div>
      </div>
    </form>
  </div>
</template>
