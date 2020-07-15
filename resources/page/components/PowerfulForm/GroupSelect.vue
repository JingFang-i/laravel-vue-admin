<template>
  <el-select v-model="value" :multiple="multiple" placeholder="请选择" @change="change">
    <el-option-group v-for="group in options" :key="group.id" :label="group.name">
      <el-option
        v-for="item in group.children"
        :key="group.id + '-' + item.id"
        :label="item.name"
        :value="item.id"
      />
    </el-option-group>
  </el-select>
</template>
<script>
export default {
  props: {
    resource: {
      type: Function,
      required: true
    },
    labelName: {
      type: String,
      default: () => 'name'
    },
    selected: {
      type: String,
      default: () => ''
    },
    multiple: {
      type: Boolean,
      default: () => false
    }
  },
  data() {
    return {
      value: '',
      options: []
    }
  },
  mounted() {
    this.currentSelected = this.selected ? this.selected : ''
    this.getLists()
  },
  methods: {
    change() {
      this.$emit('update:selected', this.value)
    },
    getLists() {
      const params = {
        is_select: 1
      }
      this.resource(params)
        .then(res => {
          this.options = res.data
        })
        .catch(err => this.$message.error(err))
    }
  }
}
</script>
