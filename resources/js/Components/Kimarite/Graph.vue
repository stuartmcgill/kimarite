<script lang="ts" setup>
import { computed } from '@vue/reactivity'
import { useKimariteStore } from '@/stores/kimarite.js'

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { KimariteCount } from '@/types/kimarite';

const store = useKimariteStore()

const data = computed(() => {
  const labels = store.bashoIds
  const datasets = store.counts.map((count: KimariteCount) => {
    const dataset = {
        label
    }

    return dataset
  })

  console.log(datasets)

  return {
    labels: labels,
    datasets: datasets,
  }

  return {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'Data One',
        backgroundColor: '#f87979',
        data: [40, 39, 10, 40, 39, 80, 40],
      },
    ],
  }
})

const options = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
}))

ChartJS.register(
  CategoryScale,
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
