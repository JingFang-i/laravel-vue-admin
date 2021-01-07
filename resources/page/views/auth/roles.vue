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
    :buttons="buttons"
    :update-batch="multiUpdate"
    :delete-batch="multiDestroy"
    :default-expand-all="true"
    :permission-rules="permissionRules"
  >
    <el-drawer
      title="分配权限"
      :size="formSize"
      :visible.sync="visibleAssignAuthForm"
      destroy-on-close
    >
      <powerful-form
        :fields="assignAuthFields"
        :rules="assignAuth.rules"
        :row="assignAuth.editRow"
        button-title="提交"
        @submit="assignAuthSubmit"
      />
    </el-drawer>
  </powerful-table>
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import PowerfulForm from '@/components/PowerfulForm'
import { lists, update, del, add, assignPermission, multiUpdate, multiDestroy } from '@/api/auth/role'
import { lists as permissionLists } from '@/api/auth/permission'
export default {
  components: {
    PowerfulTable,
    PowerfulForm
  },
  data() {
    return {
      fields: [
        { field: 'id', label: 'ID', searchable: true, editable: false },
        { field: 'parent_id', label: '父级', type: 'custom-select',  selectList: lists, visible: false},
        { field: 'name', label: '角色名称', operate: 'like' },
        { field: 'created_at', label: '创建时间', type: 'date', operate: 'range', editable: false },
        { field: 'updated_at', label: '更新时间', type: 'date', operate: 'range', editable: false }
      ],
      operations: ['add', 'edit', 'del'],
      rules: {},
      formSize: '30%',
      visibleAssignAuthForm: false,
      needFresh: false,
      assignAuth: {
        rules: {
          role_id: [{ required: true, message: '管理员ID不能为空', trigger: 'blur' }],
          permission_ids: [{ required: true, message: '权限不能为空', trigger: 'blur' }]
        },
        editRow: { role_id: 0, permission_ids: [] }
      },
      allPermissions: [],
      defaultCheckedKeys: [],
      permissionRules: {
        list: 'roles.index',
        detail: 'roles.show',
        edit: 'roles.update',
        add: 'roles.store',
        del: 'roles.destroy',
        updateBatch: 'roles.multi',
        deleteBatch: 'roles.multiDestroy',
        assignPermission: 'roles.assign-permission'
      }
    }
  },
  computed: {
    buttons() {
      return [
        {
          name: 'assignPermission',
          text: '分配权限',
          icon: 'el-icon-info',
          handle: id => {
            return this.handleAssignAuth(id)
          }
        }
      ]
    },
    assignAuthFields() {
      return [
        { field: 'role_id', label: '角色ID', editable: false },
        { field: 'permission_ids', label: '权限', type: 'tree', selectList: this.allPermissions, defaultCheckedKeys: this.defaultCheckedKeys, props: { label: 'title', children: 'children' }}
      ]
    }
  },
  mounted() {
    // 查出当前登录用户角色所有权限
    this.getAllPermissions()
  },
  methods: {
    lists,
    update,
    del,
    add,
    multiUpdate,
    multiDestroy,
    getAllPermissions() {
      const params = {
        is_select: 1
      }
      permissionLists(params).then(res => {
        this.allPermissions = res.data
      })
    },
    handleAssignAuth(id) {
      const params = {
        role_id: id,
        is_select: 1
      }
      this.assignAuth.editRow.role_id = 0
      this.assignAuth.editRow.permission_ids = []
      this.defaultCheckedKeys = []
      permissionLists(params).then(res => {
        this.defaultCheckedKeys = [...res.data]
        this.assignAuth.editRow.permission_ids = res.data
      })
      this.visibleAssignAuthForm = true
      this.assignAuth.editRow.role_id = id
    },
    assignAuthSubmit(data) {
      assignPermission(data).then(() => {
        this.visibleAssignAuthForm = false
      }).catch(err => this.$message.error(err))
    }
  }
}
</script>
