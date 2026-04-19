import { Ref, ref } from 'vue'
import type { TooltipModel } from 'chart.js'
import { useKimariteStore } from '@/stores/kimarite'

export interface RikishiMatch {
    basho_id: string
    day: number
    kimarite: string
    winner_id: number
    winner_en: string
    division: string
    winner_sumo_db_id: number
}

export interface TooltipContent {
    title: string
    bodyLines: string[]
    color: string
    x: number
    y: number
    kimariteType: string
    count: number
    skip: number
}

export function useKimariteTooltip(
    data: Readonly<Ref<{ labels: string[]; datasets: { data: unknown[] }[] }>>
) {
    const tooltipContent = ref<TooltipContent | null>(null)
    const cursorPos = ref({ x: 0, y: 0 })

    function trackCursor(e: MouseEvent) {
        cursorPos.value = { x: e.clientX, y: e.clientY }
    }

    const externalKimariteTooltip = (context: {
        chart: unknown
        tooltip: TooltipModel<'line'>
    }) => {
        const { tooltip } = context
        if (tooltip.opacity === 0) {
            return
        }

        const title = tooltip.title || []
        const body = tooltip.body || []
        const titleStr = title.length ? title.join('') : ''

        if (body.length === 0 || body[0].lines.length !== 1) {
            return
        }

        const split = body[0].lines[0].split(':')
        const kimariteType = split[0].toLowerCase().trim()
        const count = parseInt(split[1])

        const color = (tooltip.labelColors?.[0]?.borderColor as string) ?? '#ffffff'
        const bodyLines = body.flatMap((b) => b.lines)
        const { x, y } = cursorPos.value

        const labels = data.value.labels  // ordered oldest → newest, e.g. ['2025-01', '2025-03', '2026-01', '2026-03']
        const idx = labels.indexOf(titleStr)  // index of the selected basho

        // Use raw store datasets rather than chart data, which may be transformed
        // (e.g. converted to percentages) and therefore unsuitable for counting skip
        const store = useKimariteStore()
        const rawDataset = store.rawDatasets.find((ds: any) => {
            return ds.label?.toLowerCase().trim() === kimariteType
        })

        const datasetData = rawDataset?.data ?? []

        // The API returns matches newest-first, so to get matches for basho at `idx`,
        // we skip all matches from the bashos that come after it (i.e. more recent ones).
        // slice(idx + 1) gives us all basho counts newer than the selected basho.
        const skip = (datasetData.slice(idx + 1) as unknown[])
            .reduce((total: number, ds) => total + parseInt(String(ds), 10), 0)

        tooltipContent.value = {
            title: titleStr,
            bodyLines,
            color,
            x,
            y,
            kimariteType,
            count,
            skip,
        }
    }

    return { tooltipContent, externalKimariteTooltip, trackCursor }
}
