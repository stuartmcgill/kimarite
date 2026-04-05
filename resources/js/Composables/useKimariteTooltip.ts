import { ref } from 'vue'

const KIMARITE_API_URL = 'https://www.sumo-api.com/api/kimarite/'

export interface TooltipContent {
    title: string
    bodyHtml: string
    records: string
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

    function handleTooltipImpl(context: {
        tooltip: {
            opacity: number
            title: string[]
            body: { lines: string[] }[]
        }
    }) {
        const { tooltip } = context
        if (tooltip.opacity === 0) return

        const title = tooltip.title || []
        const body = tooltip.body || []
        const titleStr = title.length ? title.join('') : ''

        let bodyHtml = ''
        body.forEach((b) => {
            bodyHtml += `<div>${b.lines.join('<br>')}</div><br>`
        })

        if (body.length === 0 || body[0].lines.length !== 1) return

        const split = body[0].lines[0].split(':')
        const kimariteType = split[0].toLowerCase().trim()
        const count = split[1]?.trim()

        if (!count || parseInt(count) <= 0) return

        const labels = data.value.labels
        const firstDataset = data.value.datasets[0]?.data ?? []
        const idx = labels.indexOf(titleStr)
        const skip = (firstDataset.slice(idx + 1) as unknown[])
            .reduce((total: number, ds) => total + parseInt(String(ds), 10), 0)
        const skipParam = skip > 0 ? `&skip=${skip}` : ''

        fetch(`${KIMARITE_API_URL}${kimariteType}?limit=10&sortOrder=desc${skipParam}`)
            .then((r) => r.json())
            .then((json) => {
                const records = (json.records as { bashoId: string; day: number; kimarite: string; winnerEn: string; division: string }[])
                    .map((it) => `${it.bashoId}, day ${it.day}: ${it.kimarite} by ${it.winnerEn} (${it.division})`)
                    .join('<br>')

                tooltipContent.value = { title: titleStr, bodyHtml, records }
            })
    }

    const externalKimariteTooltip = debounce(handleTooltipImpl, 500)

    return { tooltipContent, externalKimariteTooltip, dismiss }
}
