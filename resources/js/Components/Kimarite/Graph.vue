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
  const datasets = store.datasets

  return {
    labels: labels,
    datasets: datasets,
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
</script>
<template>
  <Line :data="data" :options="options" />
</template>
