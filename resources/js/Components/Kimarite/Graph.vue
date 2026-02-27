<script lang="ts" setup>
import { computed } from '@vue/reactivity'
import { useKimariteStore } from '@/stores/kimarite.js'

import {
  Chart as ChartJS,
  CategoryScale,
  Colors,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ChartDataset,
  Chart,
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { KimariteConstants } from '@/Composables/kimariteConstants'

const store = useKimariteStore()

const data = computed(() => {
  const labels = store.bashoIds
  return {
    labels: labels,
    datasets: datasets.value,
  }
})

const syncRegressionColorsPlugin = {
  id: 'syncRegressionColors',
  afterLayout(chart: Chart<'line'>) {
    const datasets = chart.data.datasets

    datasets.forEach((dataset, idx) => {
      if (!dataset.label) {
        return
      }

      if (!dataset.label.includes('R²')) {
        return
      }

      // Assume the original dataset is the one before the regression dataset
      const originalDataset = datasets[idx - 1]
      if (!originalDataset) {
        return
      }

      // Copy border color from original dataset to regression dataset
      dataset.borderColor = originalDataset.borderColor || 'black'
      dataset.backgroundColor = originalDataset.backgroundColor || 'black'

      // Also copy point styles if needed (your regression has pointRadius: 0, so might not matter)
      dataset.pointBackgroundColor =
        originalDataset.pointBackgroundColor || originalDataset.backgroundColor
      dataset.pointBorderColor =
        originalDataset.pointBorderColor || originalDataset.borderColor
    })
  },
}

ChartJS.register(syncRegressionColorsPlugin)

const KIMARITE_API_URL = 'https://www.sumo-api.com/api/kimarite/'

let customTooltipEl: HTMLElement | null = null

function debounce<A extends unknown[]>(fn: (...args: A) => void, ms: number): (...args: A) => void {
  let timeoutId: ReturnType<typeof setTimeout>
  return (...args: A) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => fn(...args), ms)
  }
}

function externalKimariteTooltipImpl(context: { tooltip: { opacity: number; x: number; y: number; title: string[]; body: { lines: string[] }[] } }) {
  const { tooltip } = context
  if (tooltip.opacity === 0) return

  const title = tooltip.title || []
  const body = tooltip.body || []
  const titleStr = title.length ? (Array.isArray(title) ? title.join('') : String(title)) : ''
  let inner = ''
  if (title.length) {
    inner += `<div style="font-weight:bold;margin-bottom:4px">${titleStr}</div>`
  }
  body.forEach((b: { lines: string[] }) => {
    inner += `<div>${(b.lines || []).join('<br>')}</div><br>`
  })

  if(body.length > 0 && body[0].lines.length == 1) {
    const split = body[0].lines[0].split(':');
    const kimariteType = split[0].toLowerCase().trim();
    const count = split[1].trim();

    if(!count || isNaN(0) || parseInt(count) <= 0) {
      return
    }

    const labels = data.value.labels
    const datasets = data.value.datasets[0].data
    const idx = labels.indexOf(titleStr)
    console.log(idx);
    console.log(labels);
    console.log(datasets);
    console.log(datasets.slice(idx + 1));
    const skip = datasets.slice(idx + 1).reduce((total: number, ds: any) => total += parseInt(ds), 0) 
    const skipParam = skip > 0 ? `&skip=${skip}` : ''

    fetch(KIMARITE_API_URL+`${kimariteType}?limit=10&sortOrder=desc${skipParam}`)
      .then(r => r.json())
      .then(data => {
        let kimariteList = ''

        data.records.map((it: any) => {
          kimariteList += `${it.bashoId}, day ${it.day}: ${it.kimarite} by ${it.winnerEn} (${it.division})<br>`
        });
        inner += `<div>${kimariteList}</div>`

        if (tooltip.opacity === 0) {
          if (customTooltipEl) {
            customTooltipEl.remove()
            customTooltipEl = null
          }
          return
        }

        if (!customTooltipEl) {
          customTooltipEl = document.createElement('div')
          customTooltipEl.style.cssText =
            'position:absolute; top:40%; right:20px; opacity:1; background:rgba(0,0,0,0.8); color:#fff; border-radius:6px; padding:8px 12px; font-size:12px; transition:opacity 0.1s; z-index:9999'
          document.body.appendChild(customTooltipEl)
        }
        const closeBtn = '<button type="button" class="kimarite-tooltip-close" style="position:absolute;top:4px;right:4px;background:none;border:none;color:#fff;cursor:pointer;font-size:16px;line-height:1;padding:2px;">×</button>'
        customTooltipEl.innerHTML = `<div style="position:relative"><div style="padding-right:20px">${inner}</div>${closeBtn}</div>`
        customTooltipEl.querySelector('.kimarite-tooltip-close')?.addEventListener('click', () => {
          customTooltipEl?.remove()
          customTooltipEl = null
        })
      })
  }
}

const externalKimariteTooltip = debounce(externalKimariteTooltipImpl, 500)

const options = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    colors: {
      forceOverride: true,
    },
    syncRegressionColors: {},
    tooltip: {
      external: externalKimariteTooltip,
    },
  },
  scales: {
    y: {
      ticks: {
        stepSize: 1,
      },
    },
  },
  elements: {
    line: {
      tension: 0.4,
    },
  },
}))

ChartJS.register(
  CategoryScale,
  Colors,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
)

const datasets = computed(() => {
  const allDatasets: ChartDataset<'line'>[] = []

  store.datasets.forEach(dataset => {
    // Add the original kimarite dataset
    allDatasets.push({ ...dataset })

    if (!store.showRegression) {
      return
    }

    const regressionData = linearRegressionWithR2(dataset.data)
    if (regressionData.data.length === 0) {
      return
    }

    if (
      regressionData.r2 < KimariteConstants.r2Threshold &&
      store.hideWeakCorrelations
    ) {
      return
    }

    const r2 = regressionData.r2.toFixed(2)

    allDatasets.push({
      label: `${dataset.label} (R² = ${r2})`,
      data: regressionData.data,
      borderWidth: 2,
      pointRadius: 0,
      fill: false,
      borderDash: [4, 4],
    })
  })

  return allDatasets
})

/**
 * Calculates linear regression and R².
 * @param originalData - Array of numeric Y values
 * @returns { data: number[], r2: number }
 */
const linearRegressionWithR2 = (
  originalData: number[],
): { data: number[]; r2: number } => {
  const yValues = originalData.map(Number).filter(n => !isNaN(n))
  const xValues = yValues.map((_, i) => i)

  const n = yValues.length
  if (n === 0) {
    return { data: [], r2: 0 }
  }

  const sumX = xValues.reduce((a, b) => a + b, 0)
  const sumY = yValues.reduce((a, b) => a + b, 0)
  const sumXY = xValues.reduce((sum, xi, i) => sum + xi * yValues[i], 0)
  const sumXX = xValues.reduce((sum, xi) => sum + xi * xi, 0)

  const denominator = n * sumXX - sumX * sumX
  if (denominator === 0) {
    return { data: new Array(n).fill(0), r2: 0 }
  }

  const slope = (n * sumXY - sumX * sumY) / denominator
  const intercept = (sumY - slope * sumX) / n

  const regressionData = xValues.map(x => slope * x + intercept)

  // Calculate R²
  const meanY = sumY / n
  const ssTot = yValues.reduce((sum, y) => sum + Math.pow(y - meanY, 2), 0)
  const ssRes = yValues.reduce(
    (sum, y, i) => sum + Math.pow(y - regressionData[i], 2),
    0,
  )
  const r2 = ssTot === 0 ? 0 : 1 - ssRes / ssTot

  return { data: regressionData, r2 }
}
</script>

<template>
  <Line v-show="data.datasets.length > 0" :data="data" :options="options" />
</template>
