import { Ref, ref } from 'vue'
import { useKimariteStore } from '@/stores/kimarite'

export interface KimariteRecord {
    bashoId: string
    day: number
    kimarite: string
    winnerId: number
    winnerEn: string
    division: string
}

export interface TooltipContent {
    title: string
    bodyLines: string[]
    records: KimariteRecord[]
    color: string
}

function debounce<A extends unknown[]>(fn: (...args: A) => void, ms: number): (...args: A) => void {
    let timeoutId: ReturnType<typeof setTimeout>
    return (...args: A) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => fn(...args), ms)
    }
}

export function useKimariteTooltip(
    data: Readonly<Ref<{ labels: string[]; datasets: { data: unknown[] }[] }>>
) {
    const tooltipContent = ref<TooltipContent | null>(null)

    function dismiss() {
        tooltipContent.value = null
    }

    const handleTooltipImpl = async (context: {
        tooltip: {
            opacity: number
            title: string[]
            body: { lines: string[] }[]
            labelColors: { backgroundColor: string; borderColor: string }[]
        }
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

        const labels = data.value.labels
        const idx = labels.indexOf(titleStr)

        // Find the dataset that matches the hovered kimarite type
        const matchingDataset = data.value.datasets.find((ds: any) =>
            ds.label?.toLowerCase().trim() === kimariteType
        )
        const datasetData = matchingDataset?.data ?? []

        const skip = (datasetData.slice(idx + 1) as unknown[])
            .reduce((total: number, ds) => total + parseInt(String(ds), 10), 0)

        const store = useKimariteStore()
        const instances = await store.fetchRecentInstances(kimariteType, skip) as KimariteRecord[]

        const color = tooltip.labelColors?.[0]?.borderColor ?? '#ffffff'

        tooltipContent.value = {
            title: titleStr,
            bodyLines: body.flatMap((b) => b.lines),
            records: instances,
            color
        }
    }

    const externalKimariteTooltip = debounce(handleTooltipImpl, 500)

    return { tooltipContent, externalKimariteTooltip, dismiss }
}
