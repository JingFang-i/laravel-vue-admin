import request from '@/utils/request'

const apiName = 'albums'

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
 * 增加相册
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
 * 更新相册
 * @param {int} id 更新相册的ID
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
 * 删除相册
 * @param {int} id 相册ID
 */
export function del(id) {
  return request({
    url: apiName + '/' + id,
    method: 'delete'
  })
}

/**
 * 查询一条相册
 * @param {int} id 资源ID
 */
export function show(id) {
  return request({
    url: apiName + '/' + id,
    method: 'get'
  })
}

/**
 * 批量更新
 * @param data
 */
export function multiUpdate(data) {
  return request({
    url: apiName + '/multi',
    method: 'post',
    data
  })
}

/**
 * 批量删除
 */
export function multiDestroy(data) {
  return request({
    url: apiName + '/multi-destroy',
    method: 'post',
    data
  })
}