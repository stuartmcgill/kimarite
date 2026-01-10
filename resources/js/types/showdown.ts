export interface Card {
  name: string
  photoUrl: string
}

export interface Game {
  name: string
  cards: Card[]
}
