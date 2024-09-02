export interface Kimarite {
  name: string
}

export interface KimariteInstance {
  kimarite: Kimarite
  sumoDbId: string
  bashoId: string
  division: string
  day: number
}

export interface KimariteCount {
  kimarite: Kimarite
  count: number
}

export interface BashoCount {
  bashoId: string
  counts: KimariteCount[]
}
