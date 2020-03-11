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
  />
</template>
<script>
import PowerfulTable from '@/components/PowerfulTable'
import { lists, update, del, add } from '@/api/auth/module'
export default {
  components: {
    PowerfulTable
  },
  data() {
    return {
      fields: [
        { field: 'id', label: 'ID', searchable: false, editable: false },
        { field: 'name', label: '模块名称' },
        { field: 'icon', label: 'icon', type: 'icon', searchable: false },
        { field: 'weigh', label: '权重', searchable: false },
        { field: 'status', label: '状态', type: 'switch', selectList: { '0': '不显示', '1': '显示' }, yes: 1, no: 0 }
      ],
      operates: ['add', 'edit', 'delete'],
      rules: {},
      formSize: '30%'
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
