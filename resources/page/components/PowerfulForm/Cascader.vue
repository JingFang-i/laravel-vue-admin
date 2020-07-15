<template>
  <el-cascader
    :value="value"
    :props="props"
    :options="selectList"
    :show-all-levels="showAllLevels"
    clearable
    @change="change"
  />
</template>
<script>
export default {
  model: {
    prop: 'value',
    event: 'change'
  },
  props: {
    options: {
      type: [Function, Array],
      default: () => []
    },
    value: {
      type: [Array, String, Number]
    },
    props: {
      type: Object,
      default: () => ({})
    },
    showAllLevels: {
      type: Boolean,
      default: true
    },
    params: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      selectList: []
    }
  },
  mounted() {
    if (typeof this.options === 'function') {
      this.loadData()
    } else {
      this.selectList = this.options
    }
  },
  methods: {
    loadData() {
      let params
      if (this.params) {
        params = this.params
      } else {
        params = {
          is_select: 1
        }
      }
      this.options(params)
        .then(res => {
          this.selectList = res.data
        })
        .catch(err => this.$message.error(err))
    },
    change(value) {
      this.$emit('change', value)
    }
  }
}
</script>
