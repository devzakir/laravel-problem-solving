import store from '~/store'

export default (to, from, next) => {
  if (!store.getters['recipienter_auth/check']) {
    next({ name: 'recipient.auth.login' })
  } else {
    next()
  }
}
