import { Ref, ref } from 'vue'
import type { TooltipModel } from 'chart.js'

export interface RikishiMatch {
    bashoId: string
    day: number
    kimarite: string
    winnerId: number
    winnerEn: string
    division: string
    winnerSumoDbId: number
}

export interface TooltipContent {
    title: string
    bodyLines: string[]
    color: string
    x: number
    y: number
    kimariteType: string
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
            tooltipContent.value = null
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

        const color = (tooltip.labelColors?.[0]?.borderColor as string) ?? '#ffffff'
        const bodyLines = body.flatMap((b) => b.lines)
        const { x, y } = cursorPos.value

        const labels = data.value.labels
        const idx = labels.indexOf(titleStr)
        const matchingDataset = data.value.datasets.find((ds: any) =>
            ds.label?.toLowerCase().trim() === kimariteType
        )
        const datasetData = matchingDataset?.data ?? []
        const skip = (datasetData.slice(idx + 1) as unknown[])
            .reduce((total: number, ds) => total + parseInt(String(ds), 10), 0)

        tooltipContent.value = {
            title: titleStr,
            bodyLines,
            color,
            x,
            y,
            kimariteType,
            skip,
        }
    }

    return { tooltipContent, externalKimariteTooltip, trackCursor }
}
