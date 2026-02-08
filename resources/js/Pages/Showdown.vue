<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import 'primeicons/primeicons.css'
import SumoMenu from '@/Components/SumoMenu.vue'
import SumoFooter from '@/Components/SumoFooter.vue'
import { GameType as GameType } from '@/types/showdown'
import Game from '@/Components/Showdown/Game.vue'
import {Ref, ref} from "vue"
import GameSettings from "@/Components/Showdown/GameSettings.vue";

const props = defineProps<{ game: GameType }>()

const settings: Ref<GameSettings | null> = ref(null)

const ready = ref(false)

const start = (gameSettings: GameSettings) => {
  settings.value = gameSettings
  ready.value = true
}
</script>

<template>
  <body class="bg-coral-100">
    <Head title="Kimarite trends" />

    <div class="mx-auto flex flex-col w-full min-h-screen sm:max-w-3xl">
      <div
        class="px-4 md:p-4 w-full flex flex-col gap-4 justify-center text-center"
      >
        <div
          class="w-full flex flex-col md:flex-row items-center justify-between gap-4"
        >
          <h1
            class="kimarite-header mx-auto order-2 md:order-1 mb-4 font-semibold text-primary-900"
          >
            Sumo showdown
          </h1>
          <SumoMenu />
        </div>
        <div class="p-6 w-full bg-white rounded-sm shadow-sm">
          <Game v-if="ready" :game="props.game" :settings="settings" />
          <GameSettings @start="start" v-else />
        </div>
      </div>
      <SumoFooter />
    </div>
  </body>
</template>
