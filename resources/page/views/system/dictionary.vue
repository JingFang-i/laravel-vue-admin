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
    :permission-rules="permissionRules"
    :disableBatch="true"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, update, del, add } from '@/api/system/dictionary'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      operations: ['add', 'edit', 'del'],
      rules: {},
      formSize: '30%',
      permissionRules: {
        list: 'dictionary.index',
        detail: 'dictionary.show',
        edit: 'dictionary.update',
        add: 'dictionary.store',
        del: 'dictionary.destroy',
        updateBatch: 'dictionary.multi',
        deleteBatch: 'dictionary.multiDestroy'
      }
    }
  },
  computed: {
    fields() {
      return [
        { field: 'id', label: 'ID', searchable: false, editable: false },
        { field: 'title', label: '字典名称', operate: 'like', required: true },
        { field: 'name', label: '标识符', required: true },
        { field: 'describe', label: '字典描述', searchable: false },
        {
          field: 'value',
          label: '字典内容',
          type: 'key-value',
          searchable: false,
          visible: false
        },
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
    update,
    del,
    add
  }
}
</script>
<style lang='scss' scoped>
</style>
