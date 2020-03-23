<template>
  <powerful-table
    :fields="fields"
    :operates="operates"
    :rules="rules"
    :form-size="formSize"
    :resource="lists"
    :update="update"
    :del="del"
    :add="add"
    :detail="detail"
    :permission-rules="permissionRules"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, add, del, update, show } from '@/api/system/pictures'
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
      operates: ['add', 'detail', 'edit', 'delete'],
      rules: {},
      formSize: '30%', // 编辑的尺寸
      permissionRules: {
        list: 'albums.index',
        detail: 'albums.show',
        edit: 'albums.update',
        add: 'albums.store',
        delete: 'albums.delete',
        updateBatch: 'albums.multi',
        deleteBatch: 'albums.multi-del'
      }
    }
  },
  methods: {
    lists,
    add,
    del,
    update,
    show,
    detail(id) {
      this.$router.push({ path: '/system/album-picture-list', query: { id: id }})
    }
  }
}
</script>
<style lang='scss' scoped>
</style>
