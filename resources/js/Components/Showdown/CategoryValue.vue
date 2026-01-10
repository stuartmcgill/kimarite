<script setup lang="ts">
import { CategoryValue as CategoryValueType } from '@/types/showdown'
import Button from 'primevue/button'
import { computed, ref } from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ categoryValue: CategoryValueType }>()
const emit = defineEmits<{
  selected: [categoryValue: CategoryValueType]
}>()
const store = useShowdownStore()

const selected = ref(false)

const disabled = computed(() => {
  if (!store.selection) {
    return false
  }

  return props.categoryValue.code !== store.selection?.code
})

const handleSelected = () => {
  selected.value = true
  emit('selected', props.categoryValue)
}
</script>

<template>
  <Button
    type="button"
    class="grid grid-cols-2 gap-2 !justify-between"
    :disabled="disabled"
    :class="selected ? '!opacity-100' : ''"
    @click="handleSelected"
  >
    <div>{{ props.categoryValue.code }}</div>
    <div>{{ props.categoryValue.value }}</div>
  </Button>
</template>
