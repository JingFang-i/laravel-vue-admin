import { constantRoutes } from '@/router'
import { getPermissions } from '@/api/user'
import router from '@/router'
import Layout from '@/layout'

/**
 * Use meta.role to determine if the current user has permission
 * @param roles
 * @param route
 */
// function hasPermission(roles, route) {
//   if (route.meta && route.meta.roles) {
//     return roles.some(role => route.meta.roles.includes(role))
//   } else {
//     return true
//   }
// }

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function formatAsyncRoutes(routes, isTop) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route }
    const item = {}

    if (tmp.children) {
      tmp.children = formatAsyncRoutes(tmp.children, false)
    }
    if (!isTop) {
      if (tmp.component_path) {
        item.component = resolve => require(['./views' + tmp.component_path + '.vue'], resolve)
      }
    } else {
      item.component = Layout
    }
    if (tmp.view_route_name) {
      item.name = tmp.view_route_name
    }
    item.meta = {
      title: tmp.title,
      icon: tmp.icon
    }
    item.path = tmp.view_route_path
    item.children = tmp.children
    res.push(item)
  })

  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  } }

const actions = {
  generateRoutes({ commit }) {
    return new Promise(resolve => {
      getPermissions().then(res => {
        const accessedRoutes = formatAsyncRoutes(res.data.menu, true)
        commit('SET_ROUTES', accessedRoutes)
        // dynamically add accessible routes
        router.addRoutes(accessedRoutes)
        resolve(accessedRoutes)
      })
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
