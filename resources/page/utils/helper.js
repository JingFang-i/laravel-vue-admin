// 获取图片完整路径
export function getImgUrl(src) {
  if (!src || src.indexOf('http') !== -1) {
    return src
  }
  return process.env.VUE_APP_ASSETS_URL ? process.env.VUE_APP_ASSETS_URL + src : src
}
