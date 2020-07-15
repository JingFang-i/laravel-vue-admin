<template>
  <el-select
    :value="selected"
    :selected="selected"
    :multiple="multiple"
    placeholder="请选择"
    @change="change"
  >
    <template v-for="(item, key) in selectList">
      <el-option
        v-if="selectList instanceof Array"
        :key="key"
        :label="item[labelName]"
        :value="item[keyName]"
        :class="{
          'text-bold':
            item.children instanceof Array && item.children.length > 0
        }"
      />
      <el-option
        v-if="!(selectList instanceof Array)"
        :key="key"
        :label="item"
        :value="key"
      />
      <template v-if="item.children instanceof Array">
        <template v-for="(v, k) in item.children">
          <el-option
            :key="key + '-' + k"
            :label="
              (k !== item.children.length - 1 ? '├ ' : '└ ') + v[labelName]
            "
            :value="v[keyName]"
          />
        </template>
      </template>
    </template>
  </el-select>
</template>
<script>
export default {
  model: {
    prop: 'selected',
    event: 'change'
  },
  props: {
    resource: {
      type: Function,
      required: true
    },
    labelName: {
      type: String,
      default: 'name'
    },
    keyName: {
      type: String,
      default: 'id'
    },
    selected: {
      type: [String, Number, Array],
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
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
    this.getLists()
  },
  methods: {
    change(value) {
      this.$emit('change', value)
    },
    getLists() {
      let params = {
        is_select: 1
      }
      if (this.params) {
        params = this.params
      }
      this.resource(params)
        .then(res => {
          this.selectList = res.data
        })
        .catch(err => this.$message.error(err))
    }
  }
}
</script>
<style lang="scss" scope>
.text-bold {
  font-weight: bold;
}
</style>
