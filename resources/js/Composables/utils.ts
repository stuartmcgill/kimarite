export const formatBashoId = (bashoId: string) => {
  return `${bashoId.substring(0, 4)}-${bashoId.substring(4, 6)}`
}
