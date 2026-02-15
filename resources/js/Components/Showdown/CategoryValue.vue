<script setup lang="ts">
import { Category, CategoryValue as CategoryValueType } from '@/types/showdown'
import Button from 'primevue/button'
import { computed, ref, useAttrs } from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ categoryValue: CategoryValueType }>()
const emit = defineEmits<{
  selected: [categoryValue: CategoryValueType]
}>()
const store = useShowdownStore()

const selected = computed(() => {
  return props.categoryValue.code === store.selection?.code
})

const category = computed(
  () =>
    store.game!.categories.find(
      (category: Category) => category.code === props.categoryValue.code,
    ) || null,
)

const name = computed(() => category.value?.name || props.categoryValue.code)
const suffix = computed(() => category.value?.suffix)

const value = computed(() => {
  const value = props.categoryValue.value

  return value % 1 === 0 ? Math.floor(value) : value
})

const disabled = computed(() => {
  if (store.chooser === store.computer) {
    return true
  }

  if (!store.selection) {
    return false
  }

  return props.categoryValue.code !== store.selection?.code
})

const handleSelected = () => {
  emit('selected', props.categoryValue)
}
</script>

<template>
  <Button
    type="button"
    size="small"
    class="grid grid-cols-2 gap-2 justify-between! px-1! sm:px-2.5! py-1!"
    :disabled="disabled"
    :class="selected ? 'opacity-100!' : ''"
    @click="handleSelected"
  >
    <div>
      {{ name }} <span v-if="suffix">({{ suffix }})</span>
    </div>
    {{ value }}
  </Button>
</template>
