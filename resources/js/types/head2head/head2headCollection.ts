import { WrestlerRecord } from '@/types/head2head/wrestlerRecord'

interface Matchup {
  opponentId: number
  rikishiWins: number
  opponentWins: number
  winningPercentage: number
}

export default class Head2HeadCollection {
  head2heads: WrestlerRecord[]

  constructor(head2heads: WrestlerRecord[]) {
    this.head2heads = head2heads
  }

  sort() {
    function compareHead2Heads(a: WrestlerRecord, b: WrestlerRecord) {
      // Sort by winning percentage, then wins, then name
      if (a.winningPercentage < b.winningPercentage) {
        return 1
      }
      if (a.winningPercentage > b.winningPercentage) {
        return -1
      }

      if (a.winningPercentage !== b.winningPercentage) {
        // One (or both) must be null
        return a.winningPercentage === null ? 1 : -1
      }

      if (a.wins !== b.wins) {
        return a.wins < b.wins ? 1 : -1
      }

      return a.shikonaEn > b.shikonaEn ? 1 : -1
    }

    this.head2heads.sort(compareHead2Heads)
  }

  refreshHead2HeadData(head2heads: { matchups: Matchup[] }) {
    this.head2heads.forEach((wrestler: WrestlerRecord, index: number) => {
      const relevantMatchup = head2heads.matchups.filter(
        matchup => matchup.opponentId === wrestler.id,
      )[0]

      this.head2heads[index].wins = relevantMatchup.rikishiWins
      this.head2heads[index].losses = relevantMatchup.opponentWins
      this.head2heads[index].winningPercentage =
        relevantMatchup.winningPercentage
    })
  }

  find(id: number) {
    return this.head2heads.filter(
      (head2head: WrestlerRecord) => head2head.id === id,
    )[0]
  }
}
