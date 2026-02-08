<script setup lang="ts">
import { Card as CardType } from '@/types/showdown'
import { computed } from 'vue'
import CategoryValue from '@/Components/Showdown/CategoryValue.vue'
import Button from 'primevue/button'

const props = defineProps<{ card: CardType }>()

const rikishiUrl = computed(
  () => `https://www.sumo.or.jp/EnSumoDataRikishi/profile/${props.card.id}`,
)

const imageUrl = computed(
  () =>
    new URL(
      `../../../images/showdown/cards/${props.card.id}.jpg`,
      import.meta.url,
    ).href,
)
</script>

<template>
  <div
    class="w-60 flex flex-col bg-coral-100 rounded-2xl border-4 border-coral-200"
  >
    <div class="w-full rounded-2xl bg-coral-400">
      <img
        :src="imageUrl"
        :alt="props.card.name"
        class="object-cover object-top w-full h-52 rounded-t-2xl"
      />
    </div>
    <div class="p-3 flex flex-col rounded">
      <Button
        as="a"
        :label="props.card.name"
        :href="rikishiUrl"
        variant="link"
      />
      <div class="flex flex-col gap-2">
        <CategoryValue
          v-for="(categoryValue, index) in props.card.categories"
          :key="`${props.card.id}-${categoryValue.code}`"
          :category-value="categoryValue"
          @selected="$emit('selected', $event)"
        />
      </div>
    </div>
  </div>
</template>
