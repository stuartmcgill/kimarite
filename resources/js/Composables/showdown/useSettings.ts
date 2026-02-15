import { computed, Ref, ref } from 'vue'

export function useSettings(difficultyLabelsMap: Map<number, string>) {
  const playerName: Ref<string> = ref(localStorage.getItem('playerName') || '')
  const numCards: Ref<number> = ref(
    parseInt(localStorage.getItem('numCards') || '32'),
  )
  const difficultyLevel: Ref<number> = ref(
    parseInt(localStorage.getItem('difficultyLevel') || '40'),
  )

  const difficultyRank = computed(() =>
    getDifficultyRank(difficultyLevel.value),
  )

  const getDifficultyRank = (difficultyLevel: number) => {
    return difficultyLabelsMap.get(difficultyLevel)
  }

  const difficultySeverity = computed(() => {
    if (difficultyLevel.value === 0) {
      return 'secondary'
    }
    if (difficultyLevel.value <= 20) {
      return 'success'
    }
    if (difficultyLevel.value <= 40) {
      return 'info'
    }
    if (difficultyLevel.value <= 60) {
      return 'warn'
    }
    if (difficultyLevel.value <= 80) {
      return 'danger'
    }

    return 'contrast'
  })

  return {
    playerName,
    numCards,
    difficultyLevel,
    difficultyRank,
    difficultySeverity,
  }
}
