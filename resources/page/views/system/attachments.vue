<template>
  <powerful-table
    :fields="fields"
    :operations="operations"
    :rules="rules"
    :form-size="formSize"
    :resource="lists"
    :del="del"
    :delete-batch="multiDestroy"
    :permission-rules="permissionRules"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, del, multiDestroy } from '@/api/system/accessory'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      operations: ['del'],
      rules: {},
      formSize: '30%',
      permissionRules: {
        list: 'attachments.index',
        detail: 'attachments.show',
        edit: 'attachments.update',
        add: 'attachments.store',
        del: 'attachments.destroy',
        updateBatch: 'attachments.multi',
        deleteBatch: 'attachments.multiDestroy'
      }
    }
  },
  computed: {
    fields() {
      return [
        { field: 'id', label: 'ID', searchable: false, editable: false },
        { field: 'name', label: '附件名称', operate: 'like', required: true },
        { field: 'path', label: '图片', searchable: false, type: 'image' },
        { field: 'mime_type', label: 'mime-type', searchable: false },
        { field: 'size', label: '文件大小', formatter: (row) => row.size / 1024, searchable: false },
        {
          field: 'created_at',
          label: '创建时间',
          type: 'date',
          operate: 'range',
          editable: false
        },
        {
          field: 'updated_at',
          label: '更新时间',
          type: 'date',
          operate: 'range',
          editable: false
        }
      ]
    }
  },
  methods: {
    lists,
    del,
    multiDestroy
  }
}
</script>
<style lang='scss' scoped>
</style>
