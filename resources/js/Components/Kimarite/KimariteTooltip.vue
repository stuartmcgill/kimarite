vue<script lang="ts" setup>
import { onMounted, onUnmounted } from 'vue'
import type { TooltipContent } from '@/Composables/useKimariteTooltip'
import TooltipRecord from '@/Components/Kimarite/TooltipRecord.vue'

const props = defineProps<{ content: TooltipContent }>()
const emit = defineEmits<{ (e: 'dismiss'): void }>()

function onClickOutside(e: MouseEvent) {
    const el = document.getElementById('kimarite-tooltip')
    if (el && !el.contains(e.target as Node)) {
        emit('dismiss')
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside))
</script>

<template>
    <Teleport to="body">
        <div id="kimarite-tooltip"
            class="fixed z-[9999] min-w-64 rounded-md bg-black/80 px-3 py-2 text-xs text-white"
            :style="{ left: `${content.x + 12}px`, top: `${content.y}px` }"
        >
            <div class="relative">
                <button
                    type="button"
                    class="absolute right-0 top-0 cursor-pointer border-none bg-transparent p-0.5 text-base leading-none text-white"
                    @click="emit('dismiss')"
                >
                    ✕
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
                    <div v-if="props.content.loading" class="flex items-center gap-2 text-white/60">
                        <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 3 0 5.373 12H4z"/>
                        </svg>
                        Loading...
                    </div>
                    <template v-else>
                        <TooltipRecord v-for="(record, index) in props.content.records" :key="index" :record="record" class="mt-1" />
                    </template>
                </div>
            </div>
        </div>
    </Teleport>
</template>
