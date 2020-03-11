<template>
  <el-tree
    :props="props"
    node-key="id"
    show-checkbox
    :data="options"
    :default-checked-keys="defaultCheckedKeys"
    @check="handleCheck"
  />
</template>
<script>
export default {
  props: {
    data: {
      type: [Function, Array],
      required: true
    },
    props: {
      type: Object,
      default: () => ({ label: 'name', children: 'children' })
    },
    defaultCheckedKeys: {
      type: Array,
      default: () => []
    },
    checkedKeys: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      options: []
    }
  },
  mounted() {
    this.options = []
    if (typeof this.data === 'function') {
      this.loadData()
    } else {
      this.options = this.data
    }
  },
  methods: {
    loadData() {
      const params = {
        is_select: 1
      }
      this.data(params).then(res => {
        this.options = res.data
      })
    },
    handleCheck(currentNode, checkedObj) {
      const allCheckedKeys = [...checkedObj.checkedKeys, ...checkedObj.halfCheckedKeys]
      this.$emit('update:checkedKeys', allCheckedKeys)
    }
  }
}
</script>
