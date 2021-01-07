import request from '@/utils/request'

const apiName = 'attachments'

/**
 * 附件查询
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

/**
 * 删除资源
 * @param {int} id 资源ID
 */
export function del(id) {
  return request({
    url: apiName + '/' + id,
    method: 'delete'
  })
}

