<script setup lang="ts">
const props = defineProps<{
  text: string
}>()
</script>

<template>
  <i class="pi pi-info-circle" style="font-size: 1rem" v-tooltip="props.text" />
</template>
