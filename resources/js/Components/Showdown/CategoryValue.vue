<script setup lang="ts">
import { Category, CategoryValue as CategoryValueType } from '@/types/showdown'
import Button from 'primevue/button'
import { computed, ref } from 'vue'
import { useShowdownStore } from '@/stores/showdown'

const props = defineProps<{ categoryValue: CategoryValueType }>()
const emit = defineEmits<{
  selected: [categoryValue: CategoryValueType]
}>()
const store = useShowdownStore()

const selected = ref(false)

const category = computed(
  () =>
    store.game!.categories.find(
      (category: Category) => category.code === props.categoryValue.code,
    ) || null,
)

const name = computed(() => category.value?.name || props.categoryValue.code)
const suffix = computed(() => category.value?.suffix)

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
    <div>
      {{ name }} <span v-if="suffix">({{ suffix }})</span>
    </div>
    {{
      props.categoryValue.value % 1 === 0
        ? Math.floor(props.categoryValue.value)
        : props.categoryValue.value
    }}
  </Button>
</template>
