import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'

// state
export const state = {
  user: null,
  token: Cookies.get('admin_token')
}

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null
}

// mutations
export const mutations = {
  [types.ADMIN_SAVE_TOKEN] (state, { token, remember }) {
    state.token = token
    Cookies.set('admin_token', token, { expires: remember ? 365 : null })
  },

  [types.ADMIN_FETCH_USER_SUCCESS] (state, { user }) {
    state.user = user
  },

  [types.ADMIN_FETCH_USER_FAILURE] (state) {
    state.token = null
    Cookies.remove('admin_token')
  },

  [types.ADMIN_LOGOUT] (state) {
    state.user = null
    state.token = null

    Cookies.remove('admin_token')
  },

  [types.ADMIN_UPDATE_USER] (state, { user }) {
    state.user = user
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, payload) {
    commit(types.ADMIN_SAVE_TOKEN, payload)
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.post('/admin/user')
      commit(types.ADMIN_FETCH_USER_SUCCESS, { user: data })
    } catch (e) {
      commit(types.ADMIN_FETCH_USER_FAILURE)
    }
  },

  updateUser ({ commit }, payload) {
    commit(types.ADMIN_UPDATE_USER, payload)
  },

  async logout ({ commit, dispatch }) {
    try {
      await axios.post('/admin/logout')
    } catch (e) { }

    commit(types.ADMIN_LOGOUT)
  },
}
