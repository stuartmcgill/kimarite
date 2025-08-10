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
} from 'chart.js'
import { Line } from 'vue-chartjs'

const store = useKimariteStore()

const data = computed(() => {
  const labels = store.bashoIds
  return {
    labels: labels,
    datasets: datasets.value,
  }
})

const options = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    colors: {
      forceOverride: true,
    },
    legend: {
      labels: {
        filter: function (legendItem, data) {
          return !legendItem.text.includes('regression')
        },
      },
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
    // Original kimarite dataset
    allDatasets.push({ ...dataset })

    if (!store.regressionPlot) {
      return
    }

    const regressionData = calculateLinearRegression(dataset.data)

    if (regressionData.length > 0) {
      allDatasets.push({
        label: `${dataset.label} (regression)`,
        data: regressionData,
        borderColor:
          dataset.borderColor || dataset.backgroundColor || 'rgba(0,0,0,0.5)',
        borderWidth: 2,
        pointRadius: 0,
        fill: false,
        borderDash: [4, 4],
      })
    }
  })

  return allDatasets
})

/**
 * Calculates the Y values for a simple linear regression (least squares method)
 * to fit a straight line through the given data points.
 *
 * @param originalData - Array of numeric Y values.
 * @returns Array of Y values representing the regression line.
 */
const calculateLinearRegression = (originalData: number[]): number[] => {
  const yValues = originalData.map(Number).filter(n => !isNaN(n))
  const xValues = yValues.map((_, i) => i)

  const n = yValues.length
  if (n === 0) {
    return []
  }

  const sumX = xValues.reduce((a, b) => a + b, 0)
  const sumY = yValues.reduce((a, b) => a + b, 0)
  const sumXY = xValues.reduce((sum, xi, i) => sum + xi * yValues[i], 0)
  const sumXX = xValues.reduce((sum, xi) => sum + xi * xi, 0)

  const denominator = n * sumXX - sumX * sumX
  if (denominator === 0) {
    return new Array(n).fill(0)
  }

  const slope = (n * sumXY - sumX * sumY) / denominator
  const intercept = (sumY - slope * sumX) / n

  return xValues.map(x => slope * x + intercept)
}
</script>

<template>
  <Line v-show="data.datasets.length > 0" :data="data" :options="options" />
</template>
