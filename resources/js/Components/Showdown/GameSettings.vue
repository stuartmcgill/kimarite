<script setup lang="ts">

import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Slider from "primevue/slider";
import {computed, ref} from "vue";
import {useSettings} from "@/Composables/showdown/useSettings";
import {GameSettings} from "@/types/showdown";

const emit = defineEmits<{
  start: GameSettings
}>()

const {name, level, getDifficultyRank} = useSettings()

const playerName = ref(name)
const difficultyLevel = ref(level)
const difficultyRank = computed(() => getDifficultyRank(difficultyLevel.value))

const start = () => {
  localStorage.setItem('playerName', playerName.value)
  localStorage.setItem('difficultyLevel', difficultyLevel.value)

  emit('start', {playerName: playerName.value, difficultyLevel: difficultyLevel.value})
}
</script>

<template>
  <form @submit.prevent="start">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-6">
        <label for="player-name" class="w-32">Player name</label>
        <InputText id="player-name" v-model="playerName"  />
      </div>
      <div class="flex items-center gap-6">
        <label for="difficulty-level" class="w-32">Difficulty</label>
        <Slider id="difficulty-level" v-model="difficultyLevel" :step="20" class="w-56" />
        <div>{{ difficultyRank }}</div>
      </div>
      <Button type="submit">Start game</Button>
    </div>
  </form>
</template>
