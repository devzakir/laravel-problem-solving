import store from '~/store'

export default (to, from, next) => {
  console.log(store.getters['recipienter_auth/check'], "___asdfasdf")
  if (store.getters['recipienter_auth/check']) {
    next({ name: 'recipient.top' })
  } else {
    next()
  }
}
