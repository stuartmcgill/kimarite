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
      dataset.borderColor =
        originalDataset.borderColor || originalDataset.color || 'black'
      dataset.backgroundColor =
        originalDataset.backgroundColor || originalDataset.color || 'black'

      // Also copy point styles if needed (your regression has pointRadius: 0, so might not matter)
      dataset.pointBackgroundColor =
        originalDataset.pointBackgroundColor || originalDataset.backgroundColor
      dataset.pointBorderColor =
        originalDataset.pointBorderColor || originalDataset.borderColor
    })
  },
}

ChartJS.register(syncRegressionColorsPlugin)

const options = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    colors: {
      forceOverride: true,
    },
    syncRegressionColors: {},
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
