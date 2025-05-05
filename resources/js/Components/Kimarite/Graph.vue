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
  //return store.datasets

  const baseDatasets = store.datasets.map(dataset => ({ ...dataset })) // clone to avoid mutation
  const mainData = store.datasets[0]?.data || []

  if (mainData.length > 1) {
    baseDatasets.push({
      label: 'Trendline',
      data: trendData(mainData),
      borderColor: 'rgba(255, 0, 0, 0.5)',
      borderWidth: 2,
      pointRadius: 0,
      fill: false,
      borderDash: [4, 4],
    })
  }

  return baseDatasets
})

const trendData = (originalData: number[]) => {
  const y = originalData.map(Number).filter(n => !isNaN(n))
  const x = y.map((_, i) => i)

  const n = y.length
  const sumX = x.reduce((a, b) => a + b, 0)
  const sumY = y.reduce((a, b) => a + b, 0)
  const sumXY = x.reduce((sum, xi, i) => sum + xi * y[i], 0)
  const sumXX = x.reduce((sum, xi) => sum + xi * xi, 0)

  const denominator = n * sumXX - sumX * sumX
  if (denominator === 0) return new Array(y.length).fill(0)

  const slope = (n * sumXY - sumX * sumY) / denominator
  const intercept = (sumY - slope * sumX) / n

  return x.map(i => slope * i + intercept)
}
</script>
<template>
  <Line v-show="data.datasets.length > 0" :data="data" :options="options" />
</template>
