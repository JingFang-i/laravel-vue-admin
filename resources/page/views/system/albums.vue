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
    :detail="detail"
    :permission-rules="permissionRules"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, add, del, update, show, multiDestroy } from '@/api/system/pictures'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      // 列表字段
      fields: [
        {
          field: 'name',
          label: '相册名称',
          type: 'text',
          placeholder: '相册名称'
        },
        {
          field: 'cover_image',
          label: '封面',
          type: 'images',
          searchable: false
        },
        {
          field: 'weigh',
          label: '权重',
          type: 'text',
          searchable: false,
          placeholder: '权重'
        }
      ],
      // 操作按钮
      operations: ['add', 'detail', 'edit', 'del'],
      rules: {},
      formSize: '30%', // 编辑的尺寸
      permissionRules: {
        list: 'albums.index',
        detail: 'albums.show',
        edit: 'albums.update',
        add: 'albums.store',
        del: 'albums.destroy',
        updateBatch: 'albums.multi',
        deleteBatch: 'albums.multiDestroy'
      }
    }
  },
  methods: {
    lists,
    add,
    del,
    update,
    show,
    multiDestroy,
    detail(id) {
      this.$router.push({ path: '/system/album-detail', query: { id: id }})
    }
  }
}
</script>
<style lang='scss' scoped>
</style>
