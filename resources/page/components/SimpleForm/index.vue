<template>
  <div class="edit-wrap">
    <el-form
        ref="simpleForm"
        :model="editRow"
        :rules="formRules"
        label-width="80px"
    >
      <template v-for="(item, key) in fields">
        <el-form-item
            :key="key"
            :label="item.title"
            :prop="item.name"
            label-width="12%"
        >
          <el-input
              v-if="item.type === 'string'"
              v-model="editRow[item.name]"
          />
          <el-input
              v-if="item.type === 'number'"
              type="number"
              v-model="editRow[item.name]"
          />
          <el-input
              v-if="item.type === 'text'"
              v-model="editRow[item.name]"
              type="textarea"
          />
          <ueditor
              v-if="item.type === 'editor'"
              :value.sync="editRow[item.name]"
          />
          <el-switch
              v-if="item.type === 'switch'"
              v-model="editRow[item.name]"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0"
          />
          <upload
              v-if="item.type === 'image'"
              :multiple="false"
              :limit="1"
              v-model="editRow[item.name]"
          />
          <upload
              v-if="item.type === 'images'"
              :limit="'limit' in item ? item.limit : 5"
              :multiple="true"
              v-model="editRow[item.name]"
          />
          <span style="font-size: 12px;color: #aaa" v-if="item.tips"><i class="el-icon-info"></i>{{ item.tips }}</span>
        </el-form-item>
      </template>
      <el-form-item>
        <el-button type="primary" size="small" @click="save">保存</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
import Ueditor from '../UEditor/Ueditor'
import Upload from '@/components/PowerfulForm/Upload'
import { getToken } from '@/utils/auth'

export default {
  components: {
    Ueditor,
    Upload
  },
  props: {
    rules: {
      type: Object,
      default: () => ({})
    },
    fields: {
      type: Array,
      default: () => []
    },
    row: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formRules: {},
      editRow: {},
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
  watch: {
    row: {
      deep: true,
      handler: function(value) {
        this.editRow = Object.assign({}, this.row)
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
          this.$emit('submit', this.editRow)
        } else {
          this.$message.error('请检查表单是否填写正确')
          return false
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
<style lang="scss" scoped></style>
