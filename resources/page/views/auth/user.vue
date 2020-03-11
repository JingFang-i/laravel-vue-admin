<template>
  <div>
    <powerful-table
      :fields="fields"
      :operates="operates"
      :rules="rules"
      :form-size="formSize"
      :buttons="buttons"
      :resource="lists"
      :update="update"
      :del="del"
      :add="add"
    />
    <el-drawer
      title="分配角色"
      :size="formSize"
      :visible.sync="visibleAssignRoleForm"
      destroy-on-close
    >
      <powerful-form
        :fields="assignRole.fields"
        :rules="assignRole.rules"
        :row="assignRole.editRow"
        button-title="提交"
        @submit="assignRoleSubmit"
      />
    </el-drawer>
  </div>
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import PowerfulForm from '@/components/PowerfulForm'
import { lists, update, del, add, assignRole } from '@/api/auth/user'
import { lists as roleLists } from '@/api/auth/role'
export default {
  components: {
    PowerfulTable,
    PowerfulForm
  },
  data() {
    const validateRoleIds = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('请选择要分配的角色'))
      } else {
        callback()
      }
    }

    return {
      fields: [
        { field: 'id', label: 'ID', searchable: true, editable: false },
        { field: 'avatar', label: '头像', type: 'avatar', searchable: false },
        { field: 'username', label: '用户名', operate: 'like' },
        { field: 'name', label: '姓名', operate: 'like' },
        { field: 'roles.name', label: '角色', type: 'formatter', searchable: false, editable: false, formatter: (row, column, cellValue, index) => {
          const rolesName = []
          row.roles.forEach(item => {
            rolesName.push(item.name)
          })
          return rolesName.join('、')
        } },
        {
          field: 'password',
          label: '密码',
          type: 'password',
          searchable: false,
          visible: false
        },
        {
          field: 'password_confirmation',
          label: '确认密码',
          type: 'password',
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
      ],
      operates: ['add', 'edit', 'delete'],
      rules: {},
      formSize: '30%',
      visibleAssignRoleForm: false,
      assignRole: {
        fields: [
          { field: 'admin_id', label: '管理员', editable: false },
          { field: 'role_ids', label: '角色', type: 'cascader', selectList: roleLists, showAllLevels: false, props: { multiple: true, value: 'id', label: 'name', emitPath: false, checkStrictly: true }}

        ],
        rules: {
          admin_id: [{ required: true, message: '管理员ID不能为空', trigger: 'blur' }],
          role_ids: [{ validator: validateRoleIds, trigger: 'blur' }]
        },
        editRow: { admin_id: 0, role_ids: [] }
      }
    }
  },
  computed: {
    buttons() {
      return [
        {
          name: 'assign-role',
          text: '分配角色',
          icon: 'el-icon-info',
          handle: id => {
            return this.handleAssignRole(id)
          }
        }
      ]
    }
  },
  methods: {
    lists,
    update,
    del,
    add,
    handleAssignRole(id) {
      const params = {
        admin_id: id
      }
      roleLists(params).then(res => {
        this.assignRole.editRow.role_ids = res.data
      })
      this.assignRole.editRow.admin_id = id
      this.visibleAssignRoleForm = true
    },
    assignRoleSubmit(submitContent) {
      assignRole(submitContent).then(res => {
        this.assignRole.editRow.admin_id = 0
        this.visibleAssignRoleForm = false
      }).catch(err => this.$message.error(err))
    }
  }
}
</script>
