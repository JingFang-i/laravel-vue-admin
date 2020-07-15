<template>
  <el-select placeholder="请选择" :value="selected" filterable @change="change">
    <el-option
      v-for="(svg, index) in svgList"
      :key="index"
      :label="svg"
      :value="svg"
    >
      <span style="float: left">{{ svg }}</span>
      <span style="float: right; color: #8492a6; font-size: 13px"
        ><svg-icon :icon-class="svg"></svg-icon
      ></span>
    </el-option>
  </el-select>
</template>

<script>
export default {
  model: {
    prop: 'selected',
    event: 'change'
  },
  props: {
    selected: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      value: '',
      svgList: [
        '404',
        'album',
        'auth-user',
        'authorize',
        'avatar',
        'awkward',
        'bug',
        'chart',
        'clipboard',
        'component',
        'configuration',
        'dashboard',
        'dictionary',
        'documentation',
        'drag',
        'edit',
        'education',
        'email',
        'example',
        'excel',
        'exit-fullscreen',
        'eye',
        'eye-open',
        'file-black',
        'file-white',
        'form',
        'fullscreen',
        'guide',
        'icon',
        'international',
        'language',
        'link',
        'list',
        'lock',
        'message',
        'money',
        'nested',
        'password',
        'pdf',
        'people',
        'peoples',
        'qq',
        'role',
        'search',
        'shopping',
        'size',
        'skill',
        'star',
        'system',
        'tab',
        'table',
        'theme',
        'tree',
        'tree-table',
        'user',
        'wechat',
        'zip'
      ]
    }
  },
  methods: {
    change(value) {
      this.$emit('change', value)
    }
  }
}
</script>

<style scoped></style>
