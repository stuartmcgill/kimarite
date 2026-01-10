<script setup lang="ts">
import { CategoryValue as CategoryValueType } from '@/types/showdown'
import Button from 'primevue/button'

const props = defineProps<{ categoryValue: CategoryValueType }>()
</script>

<template>
  <Button type="button" class="grid grid-cols-2 gap-2 !justify-between">
    <div>{{ props.categoryValue.code }}</div>
    <div>{{ props.categoryValue.value }}</div>
  </Button>
</template>

<style scoped></style>
