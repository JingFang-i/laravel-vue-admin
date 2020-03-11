import request from '@/utils/request'

/**
 * 列表查询
 * @param {object} params 查询参数
 */
export function lists(params) {
  return request({
    url: 'modules',
    method: 'get',
    params
  })
}

/**
 * 增加资源
 * @param {object} data 提交数据
 */
export function add(data) {
  return request({
    url: 'modules',
    method: 'post',
    data
  })
}

/**
 * 更新资源
 * @param {Integer} id 更新资源的ID
 * @param {Object} data 更新的数据
 */
export function update(id, data) {
  return request({
    url: 'modules/' + id,
    method: 'put',
    data
  })
}

/**
 * 删除资源
 * @param {Integer} id 资源ID
 */
export function del(id) {
  return request({
    url: 'modules/' + id,
    method: 'delete'
  })
}
