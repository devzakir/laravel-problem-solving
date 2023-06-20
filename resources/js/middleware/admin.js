import store from '~/store'

export default (to, from, next) => {
  if (!store.getters['admin_auth/check']) {
    next({ name: 'admin.login' })
  } else {
    next()
  }
}
