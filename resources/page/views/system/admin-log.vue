<template>
  <powerful-table
    :fields="fields"
    :operations="operations"
    :rules="rules"
    :form-size="formSize"
    :resource="lists"
    :update="update"
    :del="del"
    :add="add"
    :delete-batch="multiDestroy"
    :update-batch="multiUpdate"
    :permission-rules="permissionRules"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, update, del, add, multiUpdate, multiDestroy } from '@/api/system/admin-log'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      operations: ['del'],
      rules: { 'id': [{ 'required': true, 'message': '不能为空', 'trigger': 'blur' }], 'admin_id': [{ 'required': true, 'message': '管理员ID不能为空', 'trigger': 'blur' }, { 'type': 'number', 'message': '管理员ID必须为一个数字', 'trigger': 'blur' }], 'name': [{ 'required': true, 'message': '姓名不能为空', 'trigger': 'blur' }, { 'max': '20', 'message': '姓名长度不能超过20', 'trigger': 'blur' }], 'title': [{ 'max': '255', 'message': '标题长度不能超过255', 'trigger': 'blur' }], 'ip': [{ 'type': 'number', 'message': 'IP必须为一个数字', 'trigger': 'blur' }], 'content': [], 'created_at': [], 'updated_at': [] },
      formSize: '30%',
      permissionRules: {
        list: 'admin-log.index',
        detail: 'admin-log.show',
        edit: 'admin-log.update',
        add: 'admin-log.store',
        del: 'admin-log.destroy',
        updateBatch: 'admin-log.multi',
        deleteBatch: 'admin-log.multiDestroy'
      }
    }
  },
  computed: {
    fields() {
      return [{ 'field': 'id', 'label': 'ID', 'type': 'text', width: 100 }, { 'field': 'admin_id', 'label': '管理员ID', 'type': 'number', width: 100 }, { 'field': 'name', 'label': '姓名', 'type': 'text' }, { 'field': 'title', 'label': '标题', 'type': 'text' }, { 'field': 'ip', 'label': 'IP', 'type': 'number' }, { 'field': 'content', 'label': '操作内容', 'type': 'code', width: 300, align: 'left' }, { 'field': 'created_at', 'label': '创建时间', 'type': 'date' }, { 'field': 'updated_at', 'label': '更新时间', 'type': 'date' }]
    }
  },
  methods: {
    lists,
    update,
    del,
    add,
    multiUpdate,
    multiDestroy
  }
}
</script>
