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
  name: string
  photoUrl: string
  categories: CategoryValue[]
}

export interface Game {
  name: string
  cards: Card[]
}
