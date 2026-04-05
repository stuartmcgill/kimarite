import { Ref, ref } from 'vue'
import type { TooltipModel } from 'chart.js'
import { useKimariteStore } from '@/stores/kimarite'

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
    records: RikishiMatch[]
    color: string
    loading: boolean
}

export function useKimariteTooltip(
    data: Readonly<Ref<{ labels: string[]; datasets: { data: unknown[] }[] }>>
) {
    const tooltipContent = ref<TooltipContent | null>(null)

    function dismiss() {
        tooltipContent.value = null
    }

    const externalKimariteTooltip = async (context: {
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
        const count = split[1]?.trim()

        if (!count || parseInt(count) <= 0) {
            return
        }

        const color = (tooltip.labelColors?.[0]?.borderColor as string) ?? '#ffffff'
        const bodyLines = body.flatMap((b) => b.lines)

        // Show tooltip immediately with loading state
        tooltipContent.value = {
            title: titleStr,
            bodyLines,
            records: [],
            color,
            loading: true,
        }

        const labels = data.value.labels
        const idx = labels.indexOf(titleStr)
        const matchingDataset = data.value.datasets.find((ds: any) =>
            ds.label?.toLowerCase().trim() === kimariteType
        )
        const datasetData = matchingDataset?.data ?? []
        const skip = (datasetData.slice(idx + 1) as unknown[])
            .reduce((total: number, ds) => total + parseInt(String(ds), 10), 0)

        const store = useKimariteStore()
        const instances = await store.fetchRecentInstances(kimariteType, skip) as RikishiMatch[]

        // Only update if the tooltip hasn't been dismissed or changed
        if (tooltipContent.value?.title === titleStr && tooltipContent.value?.bodyLines[0] === bodyLines[0]) {
            tooltipContent.value = {
                title: titleStr,
                bodyLines,
                records: instances,
                color,
                loading: false,
            }
        }
    }

    return { tooltipContent, externalKimariteTooltip, dismiss }
}
