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
    :delete-popover="deletePopover"
    :permission-rules="permissionRules"
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, update, del, add } from '@/api/auth/permission'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      fields: [
        { field: 'id', label: 'ID', searchable: true, editable: false },
        { field: 'title', label: '权限/菜单名称', operate: 'like' },
        { field: 'icon', label: 'icon', type: 'icon', searchable: false },
        { field: 'pid', label: '父级', type: 'custom-select', selectList: lists, labelName: 'title', searchable: false, visible: false },
        { field: 'name', label: '规则', searchable: false },
        { field: 'component_path', label: '组件路径', searchable: false },
        { field: 'view_route_name', label: '前端路由名称', searchable: false },
        { field: 'view_route_path', label: '前端路由路径', searchable: false },
        { field: 'redirect_path', label: '重定向路径', searchable: false },
        { field: 'guard_name', label: '守卫名称', searchable: false, editable: false },
        { field: 'is_menu', label: '是否是菜单', type: 'switch', searchable: false },
        { field: 'is_hidden', label: '是否隐藏', type: 'switch', searchable: false },
        { field: 'created_at', label: '创建时间', type: 'date', operate: 'range', editable: false },
        { field: 'updated_at', label: '更新时间', type: 'date', operate: 'range', editable: false }
      ],
      operates: ['add', 'edit', 'delete'],
      rules: {},
      formSize: '35%',
      deletePopover: false,
      permissionRules: {
        list: 'permissions.index',
        detail: 'permissions.show',
        edit: 'permissions.update',
        add: 'permissions.store',
        delete: 'permissions.delete',
        updateBatch: 'permissions.multi',
        deleteBatch: 'permissions.multi-del'
      }
    }
  },
  computed: {},
  mounted() {},
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
