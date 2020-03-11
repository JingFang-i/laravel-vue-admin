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
 * 附件删除
 * @param {object} params id数组
 */
export function deletes(params) {
  return request({
    url: apiName + '/multi-del',
    method: 'post',
    params
  })
}
