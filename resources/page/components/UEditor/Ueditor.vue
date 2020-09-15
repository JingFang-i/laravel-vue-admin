<template>
  <vue-ueditor-wrap v-model="content" @ready="ready" :config="ueditorConfig" />
</template>

<script>
import VueUeditorWrap from 'vue-ueditor-wrap'
export default {
  name: 'Ueditor',
  components: {
    VueUeditorWrap
  },
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      content: '',
      ueditorConfig: {
        // 编辑器不自动被内容撑高
        autoHeightEnabled: false,
        autoFloatEnabled: false,
        // 初始容器高度
        initialFrameHeight: 300,
        // 初始容器宽度
        initialFrameWidth: '100%',
        serverUrl: process.env.VUE_APP_BASE_API + '/ueditor',
        // UEditor 资源文件的存放路径，如果你使用的是 vue-cli 生成的项目，通常不需要设置该选项，vue-ueditor-wrap 会自动处理常见的情况，如果需要特殊配置，参考下方的常见问题2
        UEDITOR_HOME_URL: '/plugins/ueditor/'
      }
    }
  },
  watch: {
    content: function(value) {
      this.$emit('update:value', value)
    }
  },
  methods: {
    ready() {
      this.$nextTick(function() {
        this.content = this.value
      })
    }
  }
}
</script>
