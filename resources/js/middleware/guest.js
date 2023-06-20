import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/check']) {
    
  } else if (store.getters['recipienter_auth/check']) {
    next({ name: 'recipient.top' })
  } else {
    next()
  }
}
