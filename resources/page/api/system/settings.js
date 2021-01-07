import request from '@/utils/request'

const apiName = 'configs'

/**
 * 列表查询
 * @param {object} params 查询参数
 */
export function lists(params) {
  return request({
    url: apiName,
    method: 'get',
    params
  })
}

/**
 * 新增
 * @param {object} data 提交数据
 */
export function add(data) {
  return request({
    url: apiName,
    method: 'post',
    data
  })
}

/**
 * 更新
 * @param {int} id 更新的ID
 * @param {Object} data 更新的数据
 */
export function update(id, data) {
  return request({
    url: apiName + '/' + id,
    method: 'put',
    data
  })
}

/**
 * 删除
 * @param {int} id
 */
export function del(id) {
  return request({
    url: apiName + '/' + id,
    method: 'delete'
  })
}

/**
 * 查询一条
 * @param {int} id 资源ID
 */
export function show(id) {
  return request({
    url: apiName + '/' + id,
    method: 'get'
  })
}

/**
 * 更新一个组的数据
 */
export function updateGroup(data) {
  return request({
    url: apiName + '/update-group',
    method: 'post',
    data
  })
}

/**
 * 获取配置组
 */
export function getConfigGroup() {
  return request({
    url: 'config-group',
    method: 'get'
  })
}

/**
 * 站点配置
 */
export function websiteConfig() {
  return request({
    url: 'website-config',
    method: 'get'
  })
}
