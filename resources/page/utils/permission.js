import store from '@/store'

/**
 * @param {String} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */
export default function checkPermission(value) {
  const permissions = store.getters && store.getters.permissions
  const hasPermission = permissions.includes(value)

  if (!hasPermission) {
    return false
  }
  return true
}
