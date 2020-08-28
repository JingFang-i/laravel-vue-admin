import Cookies from 'js-cookie'
import { websiteConfig } from '../../api/system/settings'
import { getImgUrl } from "../../utils/helper";

const state = {
  sidebar: {
    opened: Cookies.get('sidebarStatus') ? !!+Cookies.get('sidebarStatus') : true,
    withoutAnimation: false
  },
  device: 'desktop',
  size: Cookies.get('size') || 'medium',
  name: 'Laravel Vue Admin',
  logo: require('@/assets/images/logo.png')
}

const mutations = {
  TOGGLE_SIDEBAR: state => {
    state.sidebar.opened = !state.sidebar.opened
    state.sidebar.withoutAnimation = false
    if (state.sidebar.opened) {
      Cookies.set('sidebarStatus', 1)
    } else {
      Cookies.set('sidebarStatus', 0)
    }
  },
  CLOSE_SIDEBAR: (state, withoutAnimation) => {
    Cookies.set('sidebarStatus', 0)
    state.sidebar.opened = false
    state.sidebar.withoutAnimation = withoutAnimation
  },
  TOGGLE_DEVICE: (state, device) => {
    state.device = device
  },
  SET_SIZE: (state, size) => {
    state.size = size
    Cookies.set('size', size)
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_LOGO: (state, logo) => {
    state.logo = logo
  }
}

const actions = {
  toggleSideBar({ commit }) {
    commit('TOGGLE_SIDEBAR')
  },
  closeSideBar({ commit }, { withoutAnimation }) {
    commit('CLOSE_SIDEBAR', withoutAnimation)
  },
  toggleDevice({ commit }, device) {
    commit('TOGGLE_DEVICE', device)
  },
  setSize({ commit }, size) {
    commit('SET_SIZE', size)
  },
  getWebsiteConfig({ commit }) {
    return new Promise(resolve => {
      websiteConfig().then(res => {
        if (res.data.name) {
          commit('SET_NAME', res.data.name)
        }
        if (res.data.logo) {
          commit('SET_LOGO', getImgUrl(res.data.logo))
        }
        resolve(res.data)
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
