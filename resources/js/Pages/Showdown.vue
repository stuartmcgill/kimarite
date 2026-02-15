<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import 'primeicons/primeicons.css'
import SumoMenu from '@/Components/SumoMenu.vue'
import {
  DifficultyLabelsMap,
  GameSettings as GameSettingsType,
  GameType as GameType,
} from '@/types/showdown'
import Game from '@/Components/Showdown/Game.vue'
import { Ref, ref } from 'vue'
import GameSettings from '@/Components/Showdown/GameSettings.vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{
  game: GameType
  difficultyLabelsMap: DifficultyLabelsMap
}>()

const store = useShowdownStore()
store.difficultyLabelsMap = new Map(
  Object.entries(props.difficultyLabelsMap).map(([k, v]) => [Number(k), v]),
)

const settings: Ref<GameSettingsType | null> = ref(null)

const start = (gameSettings: GameSettingsType) => {
  settings.value = gameSettings
}
</script>

<template>
  <body class="bg-coral-100">
    <Head title="Sumo showdown" />

    <div class="mx-auto flex flex-col w-full min-h-screen sm:max-w-3xl">
      <div
        class="px-2 sm:px-4 md:p-4 w-full flex flex-col gap-4 justify-center text-center"
      >
        <div
          class="w-full flex flex-col md:flex-row items-center justify-between gap-4"
        >
          <SumoMenu :force-hamburger="true" />
        </div>
        <div class="p-6 w-full bg-white rounded-sm shadow-sm">
          <Game v-if="settings" :game="props.game" :settings="settings" />
          <GameSettings
            v-else
            :game="props.game"
            :difficulty-labels-map="store.difficultyLabelsMap"
            @start="start"
          />
        </div>
      </div>
    </div>
  </body>
</template>
