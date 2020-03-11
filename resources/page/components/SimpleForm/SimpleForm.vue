<template>
  <el-form ref="simpleForm" v-model="form" :rules="rules" label-width="80px">
    <template v-for="(item, key) in fields">
      <el-form-item :key="key" :label="item.title" :prop="item.name" label-width="12%">
        <el-input v-if="item.type === 'string'" v-model="item.value" />
        <el-input v-if="item.type === 'text'" v-model="item.value" type="textarea" />
        <vue-ueditor-wrap
          v-if="item.type === 'editor'"
          v-model="item.value"
          :config="ueditorConfig"
        />
        <el-switch
          v-if="item.type === 'switch'"
          v-model="item.value"
          active-color="#13ce66"
          inactive-color="#ff4949"
          active-value="1"
          inactive-value="0"
        />
      </el-form-item>
    </template>
    <el-form-item>
      <el-button type="primary" size="small" @click="save">保存</el-button>
    </el-form-item>
  </el-form>
</template>
<script>
import VueUeditorWrap from 'vue-ueditor-wrap'
import { getToken } from '@/utils/auth'
export default {
  components: {
    VueUeditorWrap
  },
  props: {
    rules: {
      type: Object,
      default: () => ({})
    },
    fields: {
      type: Array,
      default: () => ({})
    }
  },
  data() {
    return {
      form: {},
      formRules: {},
      ueditorConfig: {
        // 编辑器不自动被内容撑高
        autoHeightEnabled: false,
        // 初始容器高度
        initialFrameHeight: 300,
        // 初始容器宽度
        initialFrameWidth: '100%',
        serverUrl: process.env.VUE_APP_BASE_URL + '/ueditor/' + getToken(),
        // UEditor 资源文件的存放路径，如果你使用的是 vue-cli 生成的项目，通常不需要设置该选项，vue-ueditor-wrap 会自动处理常见的情况，如果需要特殊配置，参考下方的常见问题2
        UEDITOR_HOME_URL: '/plugins/ueditor/'
      }
    }
  },
  mounted() {
    this.formRules = this.rules
    if (Object.keys(this.rules).length === 0) {
      this.generateRules()
    }
  },
  methods: {
    save() {
      this.$refs['simpleForm'].validate(valid => {
        if (valid) {
          this.$emit('submit', this.fields)
        }
      })
    },
    generateRules() {
      this.fields.forEach(item => {
        if ('required' in item && item.required === true) {
          this.formRules[item.field] = []
          this.formRules[item.field].push({
            required: true,
            message: item.title + '不能为空!',
            trigger: 'change'
          })
        }
      })
    }
  }
}
</script>
