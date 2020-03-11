<template>
  <div class="edit-wrap">
    <el-form ref="form" :rules="formRules" class="c-fs-fs" :model="editRow" :validate-on-rule-change="false" :status-icon="true" label-width="25%">
      <template v-for="(item, index) in fields">
        <el-form-item
          v-if="item.editable !== false"
          :key="index"
          :label="item.label"
          :prop="item.field"
          style="width:80%"
        >
          <el-input
            v-if="!('type' in item) || ['text', 'url'].indexOf(item.type) !== -1"
            v-model="editRow[item.field]"
            :maxlength="item.maxlength"
          />
          <el-select
            v-if="'type' in item && item.type === 'select'"
            v-model="editRow[item.field]"
            :placeholder="item.placeholder"
            @change="selectChange"
          >
            <el-option v-for="(i, k) in item.selectList" :key="k" :label="i" :value="isNaN(k) ? k : parseInt(k)" />
          </el-select>
          <el-date-picker
            v-if="'type' in item && item.type === 'date'"
            v-model="editRow[item.field]"
            type="date"
            :placeholder="item.placeholder"
          />
          <el-switch
            v-if="'type' in item && item.type === 'switch'"
            v-model="editRow[item.field]"
            active-color="#13ce66"
            inactive-color="#ff4949"
            :active-text="item.yes in item.selectList ? '是' : item.selectList[item.yes]"
            :inactive-text="item.no in item.selectList? '否' : item.selectList[item.no]"
            :active-value="item.yes"
            :inactive-value="item.no"
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
            :multiple="'multiple' in item ? item.multiple : false"
            :selected.sync="editRow[item.field]"
          />
          <!-- <el-cascader
            v-if="item.type === 'cascader'"
            v-model="editRow[item.field]"
            :props="{multiple: 'multiple' in item ? item.multiple : false}"
            :options="item.selectLists"
          /> -->
          <cascader
            v-if="item.type === 'cascader'"
            :options="item.selectList"
            :props="item.props"
            :value.sync="editRow[item.field]"
            :show-all-levels="item.showAllLevels"
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
          <key-value-table v-if="item.type === 'key-value'" :data.sync="editRow[item.field]" />
          <el-input
            v-if="item.type === 'textarea'"
            v-model="editRow[item.field]"
            type="textarea"
            :autosize="item.autosize"
            :maxlength="item.maxlength"
          />
          <!-- <icon-picker v-if="item.type === 'icon'" v-model="editRow[item.field]" /> -->
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
            :files.sync="editRow[item.field]"
          />
          <upload v-if="item.type === 'images'" :multiple="true" :files.sync="editRow[item.field]" />

        </el-form-item>
      </template>
      <el-form-item style="width:90%">
        <el-button
          class="button"
          type="primary"
          size="mini"
          @click="submit()"
        >{{ buttonTitle ? buttonTitle : ( row.id ? '确定修改' : '确定添加' ) }}</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
import Upload from './Upload'
import CustomSelect from './CustomSelect'
import GroupSelect from './GroupSelect'
import KeyValueTable from './KeyValueTable'
import Cascader from './Cascader'
import Tree from './Tree'

export default {
  components: {
    Upload,
    CustomSelect,
    GroupSelect,
    KeyValueTable,
    Cascader,
    Tree
  },
  props: {
    fields: {
      type: Array,
      required: true
    },
    row: {
      type: Object,
      default: () => ({})
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
      editRow: {}
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
    // 如果没有传入验证规则，则生成简单验证规则
    if (Object.keys(this.rules).length === 0) {
      this.generateRules()
    }
    this.formRules = {}
    this.editRow = {}
    this.editRow = Object.assign({}, this.row)
  },
  methods: {
    selectChange(value) {
      console.log(typeof value)
    },
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
<style lang='scss' scoped>
.edit-wrap {
  .button {
    float: right;
  }
}
</style>
