<script setup lang="ts">
import { Card as CardType } from '@/types/showdown'
import { computed } from 'vue'

const props = defineProps<{ card: CardType }>()

const imageUrl = computed(
  () =>
    new URL(`../../../images/showdown/${props.card.id}.jpg`, import.meta.url)
      .href,
)
</script>

<template>
  <div class="w-60 flex flex-col bg-coral-100 rounded">
    <div class="w-full h-60 rounded bg-coral-400">
      <img
        :src="imageUrl"
        :alt="props.card.name"
        class="object-cover object-top w-full h-60"
      />
    </div>
    <div class="p-3 flex flex-col rounded">
      <div>
        {{ props.card.name }}
      </div>
      <div class="flex flex-col gap-2">
        <div
          v-for="(category, index) in props.card.categories"
          class="grid grid-cols-2 gap-2"
        >
          <div>{{ category.code }}</div>
          <div>{{ category.value }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
