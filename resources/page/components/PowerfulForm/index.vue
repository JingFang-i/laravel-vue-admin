<template>
  <div class="edit-wrap">
    <el-form
      ref="form"
      :rules="formRules"
      class="c-fs-fs"
      :model="editRow"
      :validate-on-rule-change="false"
      :status-icon="true"
      label-width="25%"
    >
      <template v-for="(item, index) in fields">
        <el-input
          v-if="item.type === 'hidden'"
          type="hidden"
          v-model="editRow[item.field]"
        ></el-input>
        <el-form-item
          v-if="
            typeof item.editable === 'function'
              ? item.editable.call(this, editRow)
              : item.editable !== false && item.type !== 'hidden'
          "
          :key="index"
          :label="item.label"
          :prop="item.field"
          style="width:80%"
        >
          <el-input
            v-if="!('type' in item) || ['text', 'url'].includes(item.type)"
            v-model="editRow[item.field]"
            :maxlength="item.maxlength"
          />
          <el-select
            v-if="
              'type' in item &&
                (item.type === 'select' || item.type === 'status')
            "
            v-model="editRow[item.field]"
            :placeholder="item.placeholder"
          >
            <el-option
              v-for="(i, k) in item.selectList"
              :key="k"
              :label="i"
              :value="isNaN(k) ? k : parseInt(k)"
            />
          </el-select>
          <el-date-picker
            v-if="'type' in item && item.type === 'date'"
            v-model="editRow[item.field]"
            type="date"
            :format="dateFormat"
            :value-format="dateFormat"
            :placeholder="item.placeholder"
          />
          <el-date-picker
            v-if="'type' in item && item.type === 'datetime'"
            v-model="editRow[item.field]"
            type="datetime"
            :format="datetimeFormat"
            :value-format="datetimeFormat"
            :placeholder="item.placeholder"
          />
          <el-switch
            v-if="'type' in item && item.type === 'switch'"
            v-model="editRow[item.field]"
            active-color="#13ce66"
            inactive-color="#ff4949"
            :active-value="'yes' in item ? item.yes : 1"
            :inactive-value="'no' in item ? item.no : 0"
          />
          <el-input-number
            v-if="'type' in item && item.type === 'number'"
            v-model="editRow[item.field]"
            :min="'min' in item ? 0 : item.min"
            :max="'max' in item ? 100 : item.max"
            label="item.label"
          />
          <custom-select
            v-if="item.type === 'custom-select'"
            :resource="item.selectList"
            :params="item.params"
            :label-name="'labelName' in item ? item.labelName : 'name'"
            :key-name="'keyName' in item ? item.keyName : 'id'"
            :multiple="'multiple' in item ? item.multiple : false"
            v-model="editRow[item.field]"
          />
          <cascader
            v-if="item.type === 'cascader'"
            :options="item.selectList"
            :props="item.props"
            v-model="editRow[item.field]"
            :show-all-levels="item.showAllLevels"
            :params="item.params"
          />
          <tree
            v-if="item.type === 'tree'"
            :data="item.selectList"
            :props="'props' in item ? item.props : undefined"
            :default-checked-keys="item.defaultCheckedKeys"
            :checked-keys.sync="editRow[item.field]"
          />
          <group-select
            v-if="item.type === 'group-select'"
            :resource="item.selectList"
            :label-name="'labelName' in item ? item.labelName : 'name'"
            :multiple="'multiple' in item ? item.multiple : false"
            :selected.sync="editRow[item.field]"
          />
          <key-value-table
            v-if="item.type === 'key-value'"
            :data.sync="editRow[item.field]"
          />
          <el-input
            v-if="item.type === 'textarea'"
            v-model="editRow[item.field]"
            type="textarea"
            :autosize="item.autosize"
            :maxlength="item.maxlength"
          />
          <svg-select
            v-if="item.type === 'icon'"
            v-model="editRow[item.field]"
          ></svg-select>
          <el-input
            v-if="item.type === 'password'"
            v-model="editRow[item.field]"
            type="password"
            :label="item.label"
            :placeholder="item.placeholder"
          />
          <upload
            v-if="item.type === 'image' || item.type === 'avatar'"
            :multiple="false"
            :limit="1"
            :tips="item.tips"
            v-model="editRow[item.field]"
          />
          <upload
            v-if="item.type === 'images'"
            :limit="'limit' in item ? item.limit : 5"
            :multiple="true"
            :tips="item.tips"
            v-model="editRow[item.field]"
          />
          <upload-file
            v-if="item.type === 'file'"
            :limit="1"
            :multiple="false"
            :tips="item.tips"
            :accept="item.accept ? item.accept : ''"
            v-model="editRow[item.field]"
          ></upload-file>
          <upload-file
            v-if="item.type === 'files'"
            :limit="'limit' in item ? item.limit : 5"
            :multiple="true"
            :tips="item.tips"
            :accept="item.accept ? item.accept : ''"
            v-model="editRow[item.field]"
          ></upload-file>
          <ueditor
            v-if="item.type === 'editor'"
            :value.sync="editRow[item.field]"
          />
          <tinymce
            v-if="item.type === 'tinymce'"
            :v-model="editRow[item.field]"
          ></tinymce>
          <el-input
            v-if="item.type === 'price'"
            type="number"
            v-model="editRow[item.field]"
          >
            <template slot="prepend"
              >¥</template
            >
          </el-input>

          <slot :item="item" :row="editRow"></slot>
        </el-form-item>
      </template>
      <el-form-item style="width:90%">
        <el-button
          class="button"
          type="primary"
          size="mini"
          @click="submit()"
          >{{
            buttonTitle ? buttonTitle : row.id ? '确定修改' : '确定添加'
          }}</el-button
        >
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
import Upload from './Upload'
import UploadFile from './UploadFile'
import CustomSelect from './CustomSelect'
import GroupSelect from './GroupSelect'
import KeyValueTable from './KeyValueTable'
import Cascader from './Cascader'
import Tree from './Tree'
import Ueditor from '../UEditor/Ueditor'
import Tinymce from '../Tinymce'
import SvgSelect from '../SvgIcon/SvgSelect'

export default {
  components: {
    Upload,
    UploadFile,
    CustomSelect,
    GroupSelect,
    KeyValueTable,
    Cascader,
    Tree,
    SvgSelect,
    Ueditor,
    Tinymce
  },
  props: {
    fields: {
      type: Array,
      required: true
    },
    row: {
      type: Object,
      required: true
    },
    rules: {
      type: Object,
      default: () => ({})
    },
    buttonTitle: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      formRules: {},
      editRow: this.row,
      dateFormat: 'yyyy-MM-dd',
      datetimeFormat: 'yyyy-MM-dd HH:mm:ss',
      ueditorConfig: {
        // 编辑器不自动被内容撑高
        autoHeightEnabled: false,
        // 初始容器高度
        initialFrameHeight: 300,
        // 初始容器宽度
        initialFrameWidth: '100%',
        serverUrl: process.env.VUE_APP_BASE_URL + '/ueditor',
        // UEditor 资源文件的存放路径，如果你使用的是 vue-cli 生成的项目，通常不需要设置该选项，vue-ueditor-wrap 会自动处理常见的情况，如果需要特殊配置，参考下方的常见问题2
        UEDITOR_HOME_URL: '/plugins/ueditor/'
      }
    }
  },
  watch: {
    editRowClone: {
      deep: true,
      handler: function(val, oldVal) {
        let changedValues = []
        for (let [field, value] of Object.entries(val)) {
          if (value !== oldVal[field]) {
            changedValues.push({
              field: field,
              value: value
            })
          }
        }
        this.$emit('change', {
          changedValues: changedValues,
          editRow: this.editRow
        })
      }
    },
    row: {
      deep: true,
      handler: function(value, oldVal) {
        this.editRow = this.row
      }
    }
  },
  mounted() {
    this.formRules = this.rules
    // 如果没有传入验证规则，则生成简单验证规则
    if (Object.keys(this.rules).length === 0) {
      this.generateRules()
    }
  },
  computed: {
    // 因为数据同源, 所以侦听到的结果一样, 所以这里需要用到计算属性
    editRowClone() {
      return Object.assign({}, this.editRow)
    }
  },
  methods: {
    submit() {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.$emit('submit', this.editRow)
          // this.$refs['form'].resetFields();
        } else {
          return false
        }
      })
    },
    // 当未定义rules时，可根据fields中的required属性生成验证规则
    generateRules() {
      const rules = {}
      this.fields.forEach(item => {
        if ('required' in item && item.required === true) {
          rules[item.field] = []
          rules[item.field].push({
            required: true,
            message: item.label + '不能为空!',
            trigger: 'blur'
          })
        }
      })
      this.formRules = rules
    }
  }
}
</script>
<style lang="scss" scoped>
.edit-wrap {
  .button {
    float: right;
  }
}
</style>
