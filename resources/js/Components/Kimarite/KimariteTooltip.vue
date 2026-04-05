<script lang="ts" setup>
import type { TooltipContent } from '@/Composables/useKimariteTooltip'
import TooltipRecord from '@/Components/Kimarite/TooltipRecord.vue'

const props = defineProps<{ content: TooltipContent | null }>()
const emit = defineEmits<{ (e: 'dismiss'): void }>()
</script>

<template>
    <Teleport to="body">
        <div
            v-if="content"
            class="absolute right-5 top-[40%] z-[9999] rounded-md bg-black/80 px-3 py-2 text-xs text-white"
        >
            <div class="relative">
                <button
                    type="button"
                    class="absolute right-0 top-0 cursor-pointer border-none bg-transparent p-0.5 text-base leading-none text-white"
                    @click="emit('dismiss')"
                >
                    ×
                </button>
                <div class="pr-5 flex flex-col">
                    <div v-if="props.content.title" class="mb-1 font-bold">
                        {{ props.content.title }}
                    </div>
                    <div class="mb-3">
                        <div v-for="line in props.content.bodyLines" :key="line" class="flex items-center gap-1.5">
                            <span
                                class="inline-block h-2.5 w-2.5 flex-shrink-0"
                                :style="{ backgroundColor: content.color }"
                            />
                            {{ line }}
                        </div>
                    </div>
                    <TooltipRecord v-for="(record, index) in props.content.records" :key="index" :record="record" class="mt-1" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
