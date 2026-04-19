<script lang="ts" setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { useKimariteTooltip } from '@/Composables/useKimariteTooltip'
import type { RikishiMatch } from '@/Composables/useKimariteTooltip'
import TooltipRecord from '@/Components/Kimarite/TooltipRecord.vue'
import { useKimariteStore } from '@/stores/kimarite'

const props = defineProps<{
    data: { labels: string[]; datasets: { data: unknown[] }[] }
}>()

const dataRef = computed(() => props.data)
const { tooltipContent, externalKimariteTooltip, trackCursor } = useKimariteTooltip(dataRef)

defineExpose({ externalKimariteTooltip, trackCursor })

const store = useKimariteStore()
const records = ref<RikishiMatch[]>([])
const loading = ref(false)
const loaded = ref(false)

watch(tooltipContent, () => {
    records.value = []
    loading.value = false
    loaded.value = false
})

async function loadInstances() {
    if (!tooltipContent.value) {
        return
    }
    loading.value = true

    try {
        const bashoId = tooltipContent.value.title.replace('-', '')

        records.value = await store.fetchMatches(
            bashoId,
            tooltipContent.value.kimariteType
        ) as RikishiMatch[]

        loaded.value = true
    } finally {
        loading.value = false
    }
}

function onClickOutside(e: MouseEvent) {
    const el = document.getElementById('kimarite-tooltip')
    if (el && !el.contains(e.target as Node)) {
        tooltipContent.value = null
    }
}

const tooltipEl = ref<HTMLElement | null>(null)

const EXPANDED_TOOLTIP_ESTIMATE = 100

const tooltipStyle = computed(() => {
    if (!tooltipContent.value) return {}

    const x = tooltipContent.value.x
    const y = tooltipContent.value.y
    const viewportHeight = window.innerHeight

    const estimatedHeight = loaded.value ? EXPANDED_TOOLTIP_ESTIMATE : (tooltipEl.value?.offsetHeight ?? 80)
    const bottom = Math.max(viewportHeight - y, estimatedHeight + 8)

    return {
        left: `${x}px`,
        bottom: `${bottom}px`,
        transition: 'bottom 0.2s ease',
    }
})

onMounted(() => document.addEventListener('mousedown', onClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside))
</script>

<template>
    <Teleport to="body">
        <div
            v-if="tooltipContent"
            id="kimarite-tooltip"
            ref="tooltipEl"
            class="fixed z-[9999] min-w-64 rounded-md bg-black/80 px-3 py-2 text-xs text-white"
            :style="tooltipStyle"
        >
            <div class="relative">
                <button
                    type="button"
                    class="absolute right-0 top-0 cursor-pointer border-none bg-transparent p-0.5 text-base leading-none text-white"
                    @click="tooltipContent = null"
                >
                    ✕
                </button>
                <div class="pr-5 flex flex-col">
                    <div v-if="tooltipContent.title" class="mb-1 font-bold">
                        {{ tooltipContent.title }}
                    </div>
                    <div class="mb-3">
                        <div v-for="line in tooltipContent.bodyLines" :key="line" class="flex items-center gap-1.5">
                            <span
                                class="inline-block h-2.5 w-2.5 flex-shrink-0"
                                :style="{ backgroundColor: tooltipContent.color }"
                            />
                            {{ line }}
                            <span v-if="store.displayAsPercent">%</span>
                        </div>
                    </div>
                    <template v-if="!loaded">
                        <button
                            v-if="parseFloat(tooltipContent.count) > 0"
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
                            <span v-else>Show instances</span>
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
