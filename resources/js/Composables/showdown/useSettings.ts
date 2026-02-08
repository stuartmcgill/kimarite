import {ref} from "vue";

const ranks = new Map([
  [0, "Juryo"],
  [20, "Maegashira"],
  [40, "Komusubi"],
  [60, "Sekiwake"],
  [80, "Ozeki"],
  [100, "Yokozuna"],
])

export function useSettings() {
  const name = localStorage.getItem('playerName') || ''
  const level = parseInt(localStorage.getItem('difficultyLevel') || '40')

  const getDifficultyRank = (difficultyLevel: number) => {
    return ranks.get(difficultyLevel)
  }

  return {name, level, getDifficultyRank}
}
