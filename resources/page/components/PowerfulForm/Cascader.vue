<template>
  <el-cascader v-model="selected" :props="props" :options="selectList" :show-all-levels="showAllLevels" clearable @change="change" />
</template>
<script>
export default {
  props: {
    options: {
      type: [Function, Array],
      default: () => []
    },
    value: {
      type: Array,
      default: () => []
    },
    props: {
      type: Object,
      default: () => ({})
    },
    showAllLevels: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      selected: [],
      selectList: []
    }
  },
  watch: {
    value: function(value) {
      this.selected = value
    }
  },
  mounted() {
    this.selected = this.value
    if (typeof this.options === 'function') {
      this.loadData()
    } else {
      this.selectList = this.options
    }
  },
  methods: {
    loadData() {
      const params = {
        is_select: 1
      }
      this.options(params).then(res => {
        this.selectList = res.data
      }).catch(err => this.$message.error(err))
    },
    change(value) {
      this.$emit('update:value', value)
    }
  }
}
</script>
