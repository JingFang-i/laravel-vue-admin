import { constantRoutes, endRoutes } from '@/router'
import { getPermissions } from '@/api/user'
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
 * @param isTop
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
        item.component = resolve => require(['@/views' + tmp.component_path + '.vue'], resolve)
      }
    } else {
      item.component = Layout
    }
    if (tmp.view_route_name) {
      item.name = tmp.view_route_name
    }
    if (tmp.redirect_path) {
      item.redirect = tmp.redirect_path
    }
    item.meta = {
      title: tmp.title,
      icon: tmp.icon
    }
    item.hidden = tmp.is_hidden === 1
    item.path = tmp.view_route_path
    item.children = tmp.children
    item.noShowingChildren = !(tmp.children && tmp.children.length === 1 && tmp.children[0].is_hidden === 0)
    res.push(item)
  })

  return res
}

const state = {
  routes: [],
  addRoutes: [],
  permissions: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  },
  SET_PERMISSIONS: (state, permissions) => {
    state.permissions = permissions
  }
}

const actions = {
  generateRoutes({ commit }) {
    return new Promise(resolve => {
      getPermissions().then(res => {
        let accessedRoutes = formatAsyncRoutes(res.data.menu, true)
        accessedRoutes = accessedRoutes.concat(endRoutes)
        commit('SET_ROUTES', accessedRoutes)
        commit('SET_PERMISSIONS', res.data.permission)

        resolve(accessedRoutes)
      })
    })
  },
  clearRoutes({ commit }) {
    return new Promise(resolve => {
      commit('SET_ROUTES', [])
      commit('SET_PERMISSIONS', [])
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
