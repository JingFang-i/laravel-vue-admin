import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: '/user',
    method: 'get'
  })
}

/**
 * 获取菜单和权限
 */
export function getPermissions() {
  return request({
    url: '/auth',
    method: 'get'
  })
}

export function logout() {
  return request({
    url: '/logout',
    method: 'post'
  })
}
