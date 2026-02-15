import { computed, ref } from 'vue'

export function useSettings(difficultyLabelsMap: Map<number, string>) {
  const name = localStorage.getItem('playerName') || ''
  const level = parseInt(localStorage.getItem('difficultyLevel') || '40')

  const playerName = ref(name)
  const difficultyLevel = ref(level)
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
    difficultyLevel,
    difficultyRank,
    difficultySeverity,
  }
}
