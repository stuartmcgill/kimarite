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
  cardInPlay: Card | null
}

export interface HumanPlayer extends BasePlayer {
  type: 'human'
}

export interface ComputerPlayer extends BasePlayer {
  type: 'computer'
  level: number
}

export type Player = HumanPlayer | ComputerPlayer

export interface GameType {
  name: string
  categories: Category[]
  cards: Card[]
  image?: string
  players: Player[]
}

export type DifficultyLabelsMap = Record<number, string>

export enum GameResult {
  Human,
  Computer,
  Tie,
}

export interface GameSettings {
  playerName: string
  numCards: number
  difficultyLevel: number
}
