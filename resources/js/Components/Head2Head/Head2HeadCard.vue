<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue'
import 'bootstrap/dist/css/bootstrap.css'
import { Popover } from 'bootstrap'
import { WrestlerRecord } from '@/types/head2head/wrestlerRecord'

const props = defineProps<{
  head2headRecord: WrestlerRecord
  selectedWrestler: WrestlerRecord | null
}>()

const renderPopover = ref(true)

const enablePopovers = () => {
  const popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]'),
  )
  const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new Popover(popoverTriggerEl, {
      delay: { show: 0, hide: 200 },
    })
  })
}
const forceRender = async () => {
  renderPopover.value = false
  await nextTick()

  renderPopover.value = true
  await nextTick()

  enablePopovers()
}

const populated = computed(() => !isNaN(props.head2headRecord.wins))

const record = computed(() => {
  if (!populated.value) {
    return ''
  }

  return props.head2headRecord.wins + '-' + props.head2headRecord.losses
})

const percentage = computed(() => {
  if (!populated.value) {
    return ''
  }
  if (props.head2headRecord.winningPercentage === null) {
    return ''
  }

  return props.head2headRecord.winningPercentage + '%'
})

const colour = computed(() => {
  if (props.head2headRecord.heya === props.selectedWrestler?.heya) {
    return 'secondary'
  }
  if (props.head2headRecord.wins === 0 && props.head2headRecord.losses === 0) {
    return 'info'
  }
  if (props.head2headRecord.wins === props.head2headRecord.losses) {
    return 'primary'
  }
  if (props.head2headRecord.winningPercentage > 50) {
    return 'success'
  }

  return 'danger'
})

const borderClass = computed(() => 'border-' + colour.value)
const textClass = computed(() => 'text-' + colour.value)
const textBgClass = computed(() => 'text-bg-' + colour.value)

const isVisible = computed(() => {
  if (!props.selectedWrestler) {
    return true
  }

  return props.head2headRecord.id !== props.selectedWrestler.id
})

const detailsText = computed(() => {
  let text =
    '<div>' +
    props.head2headRecord.shikonaJp +
    '</div>' +
    '<div>Rank: ' +
    props.head2headRecord.currentRank +
    '</div>' +
    '<div>Stable: ' +
    props.head2headRecord.heya +
    '</div>' +
    '<div>Height: ' +
    props.head2headRecord.height +
    'cm</div>' +
    '<div>Weight: ' +
    props.head2headRecord.weight +
    'kg</div>'

  if (
    props.selectedWrestler &&
    props.head2headRecord.wins + props.head2headRecord.losses > 0
  ) {
    text +=
      '<div><a target="_blank" href="https://sumodb.sumogames.de/Rikishi_opp.aspx?r=' +
      props.selectedWrestler.sumoDbId +
      '#' +
      props.head2headRecord.sumoDbId +
      '">Head-to-head details</a></div>'
  }

  return text
})

enablePopovers()

watch(
  () => props.selectedWrestler,
  () => forceRender(),
)
</script>

<template>
  <div
    class="card col-md-4 col-lg-3 col-xl-2 m-1"
    :class="borderClass"
    v-show="isVisible"
  >
    <h5 class="card-header">{{ head2headRecord.shikonaEn }}</h5>
    <div class="card-body p-1" :class="textClass">
      <h3 class="card-title">
        {{ record }}
        <div class="winning-percentage">{{ percentage }}</div>
      </h3>
      <a
        href="#"
        @click="$emit('selected', head2headRecord.id)"
        class="btn btn-outline-primary m-1"
        >Select</a
      >
      <a
        v-if="renderPopover"
        tabindex="0"
        class="btn btn-outline-secondary m-1"
        role="button"
        data-bs-toggle="popover"
        data-bs-trigger="focus"
        data-bs-placement="right"
        data-bs-html="true"
        :title="head2headRecord.shikonaEn"
        :data-bs-content="detailsText"
      >
        Details
      </a>
    </div>
  </div>
</template>
