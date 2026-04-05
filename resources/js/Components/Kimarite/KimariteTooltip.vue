<script lang="ts" setup>
import type { TooltipContent } from '@/Composables/useKimariteTooltip'

defineProps<{ content: TooltipContent | null }>()
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
                <div class="pr-5">
                    <div v-if="content.title" class="mb-1 font-bold">
                        {{ content.title }}
                    </div>
                    <!-- eslint-disable-next-line vue/no-v-html -->
                    <div v-html="content.bodyHtml" />
                    <!-- eslint-disable-next-line vue/no-v-html -->
                    <div v-html="content.records" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
