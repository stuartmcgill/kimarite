<script setup lang="ts">
import { CategoryValue as CategoryValueType } from '@/types/showdown'
import Button from 'primevue/button'

const props = defineProps<{ categoryValue: CategoryValueType }>()
const emit = defineEmits<{
  selected: [categoryValue: CategoryValueType]
}>()
</script>

<template>
  <Button
    type="button"
    class="grid grid-cols-2 gap-2 !justify-between"
    @click="emit('selected', props.categoryValue)"
  >
    <div>{{ props.categoryValue.code }}</div>
    <div>{{ props.categoryValue.value }}</div>
  </Button>
</template>
