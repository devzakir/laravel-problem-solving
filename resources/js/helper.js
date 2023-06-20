import moment from 'moment'

export default {
  validateMovie (name) { 
    return /\.(avi|mpg|mp4|ogg|mov)$/i.test(name)
  },

  validChecks(formData, excepts) {
    return Object.keys(formData).filter(item => !excepts.includes(item))
            .filter(key => (!formData[key] || formData[key] === '' || formData[key] === 0));
  }
}