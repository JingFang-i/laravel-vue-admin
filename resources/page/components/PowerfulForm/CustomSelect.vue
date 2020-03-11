<template>
  <el-select v-model="currentSelected" :multiple="multiple" placeholder="请选择" @change="change">
    <template v-for="(item, key) in selectList">
      <el-option
        :key="key"
        :label="item[labelName]"
        :value="item.id"
        :class="{'text-bold': item.children instanceof Array && item.children.length > 0 ? true : false}"
      />
      <template v-if="item.children instanceof Array">
        <template v-for="(v, k) in item.children">
          <el-option
            :key="key + '-' + k"
            :label="(k !== item.children.length - 1 ? '├ ' : '└ ')+ v[labelName]"
            :value="v.id"
          />
        </template>
      </template>
    </template>
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
      default: 'name'
    },
    selected: {
      type: [String, Number],
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
    },
    params: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      selectList: [],
      currentSelected: ''
    }
  },
  mounted() {
    this.getLists()
    this.$nextTick(function() {
      this.currentSelected = this.selected ? this.selected : ''
    })
  },
  methods: {
    change() {
      this.$emit('update:selected', this.currentSelected)
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
