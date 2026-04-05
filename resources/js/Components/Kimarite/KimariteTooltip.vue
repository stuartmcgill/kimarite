<script lang="ts" setup>
import { onMounted, onUnmounted, ref } from 'vue'
import type { TooltipContent } from '@/Composables/useKimariteTooltip'
import type { RikishiMatch } from '@/Composables/useKimariteTooltip'
import TooltipRecord from '@/Components/Kimarite/TooltipRecord.vue'
import { useKimariteStore } from '@/stores/kimarite'

const props = defineProps<{ content: TooltipContent }>()
const emit = defineEmits<{ (e: 'dismiss'): void }>()

const store = useKimariteStore()
const records = ref<RikishiMatch[]>([])
const loading = ref(false)
const loaded = ref(false)

async function loadInstances() {
    loading.value = true
    records.value = await store.fetchMatches(props.content.kimariteType, props.content.skip) as RikishiMatch[]
    loading.value = false
    loaded.value = true
}

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
        <div
            id="kimarite-tooltip"
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
                    <div v-if="content.title" class="mb-1 font-bold">
                        {{ content.title }}
                    </div>
                    <div class="mb-3">
                        <div v-for="line in content.bodyLines" :key="line" class="flex items-center gap-1.5">
                            <span
                                class="inline-block h-2.5 w-2.5 flex-shrink-0"
                                :style="{ backgroundColor: content.color }"
                            />
                            {{ line }}
                        </div>
                    </div>

                    <template v-if="!loaded">
                        <button
                            type="button"
                            class="mt-1 rounded bg-white/20 px-2 py-1 text-xs text-white hover:bg-white/30 disabled:opacity-50"
                            :disabled="loading"
                            @click="loadInstances"
                        >
                            <span v-if="loading" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 3 0 5.373 12H4z"/>
                                </svg>
                                Loading...
                            </span>
                            <span v-else>Show instances...</span>
                        </button>
                    </template>
                    <template v-else>
                        <TooltipRecord v-for="(record, index) in records" :key="index" :record="record" class="mt-1" />
                    </template>
                </div>
            </div>
        </div>
    </Teleport>
</template>
