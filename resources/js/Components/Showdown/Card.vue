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
    class="sm:w-60 flex flex-col bg-coral-100 rounded-2xl border-4 border-coral-200"
  >
    <div class="flex sm:flex-col w-full">
      <div class="rounded-bl-xl sm:rounded-xl w-full">
        <div class="sm:hidden">
          <Button
            as="a"
            :label="props.card.name"
            :href="rikishiUrl"
            variant="link"
          />
        </div>
        <img
          :src="imageUrl"
          :alt="props.card.name"
          class="object-cover object-top w-full h-36 sm:h-52 rounded-bl-xl sm:rounded-t-xl"
        />
      </div>
      <div class="w-full flex flex-col sm:rounded">
        <div class="hidden sm:block">
          <Button
            as="a"
            :label="props.card.name"
            :href="rikishiUrl"
            variant="link"
          />
        </div>
        <div class="flex flex-col sm:h-auto">
          <CategoryValue
            v-for="(categoryValue, index) in props.card.categories"
            :key="`${props.card.id}-${categoryValue.code}`"
            :category-value="categoryValue"
            :class="
              index === props.card.categories.length - 1
                ? 'rounded-t-none! rounded-bl-none! rounded-br-xl! sm:rounded-b-xl!'
                : 'rounded-none!'
            "
            @selected="$emit('selected', $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>
