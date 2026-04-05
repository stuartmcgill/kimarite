<script lang="ts" setup>
import type { KimariteRecord } from '@/Composables/useKimariteTooltip'
import { computed } from 'vue'

const props = defineProps<{ record: KimariteRecord }>()

const basho = computed(() => props.record.bashoId.slice(0, 4) +'-' + props.record.bashoId.slice(4))
</script>

<template>
    <div class="mt-1">
        {{ basho }} day {{ props.record.day }}, <a class="underline" :href="`https://sumodb.sumogames.de/Rikishi_basho.aspx?r=${props.record.winnerSumoDbId}&b=${props.record.bashoId}`">{{ props.record.winnerEn }}</a> ({{ props.record.division }})
    </div>
</template>
