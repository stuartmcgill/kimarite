export interface Category {
  code: string
  name: string
  suffix?: string
  inverse: boolean
}

export interface CategoryValue {
  code: string
  value: number
}

export interface Card {
  id: string
  name: string
  photoUrl: string
  categories: CategoryValue[]
}

export interface BasePlayer {
  name: string
  cards: Card[]
}

export interface HumanPlayer extends BasePlayer {
  type: 'human'
}

export interface ComputerPlayer extends BasePlayer {
  type: 'computer'
  level: number
}

export type Player = HumanPlayer | ComputerPlayer

export interface Game {
  name: string
  categories: Category[]
  cards: Card[]
  players: Player[]
}
