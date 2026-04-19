<script lang="ts" setup>
import type { RikishiMatch } from '@/Composables/useKimariteTooltip'
import { computed } from 'vue'

const props = defineProps<{ record: RikishiMatch }>()

const basho = computed(() => {
    const basho = props.record.basho_id.slice(0, 4) +'-' + props.record.basho_id.slice(4)

    return basho
})
</script>

<template>
    <div class="mt-1">
        {{ basho }} day {{ props.record.day }}, <a class="underline" :href="`https://sumodb.sumogames.de/Rikishi_basho.aspx?r=${props.record.winner_sumo_db_id}&b=${props.record.basho_id}`">{{ props.record.winner_en }}</a> ({{ props.record.division }})
    </div>
</template>
