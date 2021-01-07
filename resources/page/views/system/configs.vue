<template>
  <div class="app-container">
    <el-button
        type="primary"
        size="mini"
        style="margin-bottom: 5px"
        @click="handleAdd"
    >新增配置</el-button
    >
    <el-tabs v-model="activeName" type="card">
      <el-tab-pane
          v-for="(item, index) in configGroup"
          :key="index"
          :label="item"
          :name="index"
      >
        <simple-form
            :fields="configs[index]"
            :rules="rules[index]"
            :row="row[index]"
            @submit="submit"
        />
      </el-tab-pane>
    </el-tabs>
    <el-drawer title="新增" :visible.sync="drawer" size="35%" destroy-on-close>
      <powerful-form :fields="fields" :row="editRow" @submit="save" />
    </el-drawer>
  </div>
</template>
<script>
import { lists, add, updateGroup, getConfigGroup } from '@/api/system/settings'
import SimpleForm from '@/components/SimpleForm'
import PowerfulForm from '@/components/PowerfulForm'

export default {
  components: {
    SimpleForm,
    PowerfulForm
  },
  data() {
    const typeList = {
      string: '字符串',
      number: '数字',
      text: '文本框',
      editor: '编辑器',
      switch: '开关',
      image: '单图',
      images: '多图'
    }
    return {
      activeName: 'website',
      configGroup: [],
      configs: {},
      row: {},
      fields: [
        {
          field: 'group',
          label: '配置组',
          type: 'custom-select',
          selectList: getConfigGroup,
          required: true
        },
        { field: 'title', label: '配置标题', maxlength: 50, required: true },
        { field: 'name', label: '配置变量名称', maxlength: 50, required: true },
        {
          field: 'type',
          label: '配置类型',
          type: 'select',
          selectList: typeList,
          required: true
        },
        { field: 'value', label: '配置值' },
        {
          field: 'rule',
          label: '验证规则',
          maxlength: 255,
          placeholder: '验证规则以|分割'
        },
        { field: 'tips', label: '提示说明', type: 'textarea', maxlength: 255 },
        { field: 'extend', label: '扩展数据', type: 'textarea', maxlength: 255 }
      ],
      rules: {},
      editRow: {},
      drawer: false
    }
  },
  created() {
    this.getConfigGroup()
  },
  methods: {
    handleAdd() {
      this.drawer = true
      this.editRow = {}
    },
    save(row) {
      add(row)
          .then(res => {
            this.drawer = false
            this.getConfigGroup()
          })
          .catch()
    },
    getData() {
      lists()
          .then(res => {
            this.configs = res.data
            this.generateRules()
          })
          .catch()
    },
    getConfigGroup() {
      getConfigGroup().then(response => {
        this.configGroup = response.data
        this.getData()
      })
    },
    submit(rows) {
      let params = {
        rows: rows,
        group: this.activeName
      }
      updateGroup(params)
          .then(res => {
            this.$message.success('保存成功')
            this.$store.dispatch('app/getWebsiteConfig')
          })
          .catch()
    },
    // 生成验证规则
    generateRules() {
      const row = {}
      for (let [group, data] of Object.entries(this.configs)) {
        row[group] = {}
        this.rules[group] = {}
        data.forEach(item => {
          row[group][item.name] = item.value || ''
          if (item.rule) {
            this.rules[group][item.name] = []
            item.rule.split('|').forEach(rule => {
              let ruleSymbol = rule
              if (rule.indexOf(':') !== -1) {
                ruleSymbol = rule.split(':')[0]
              }
              let ruleItem = {}
              switch (ruleSymbol) {
                case 'required':
                  ruleItem = {
                    required: true,
                    message: item.title + '不能为空',
                    trigger: 'blur'
                  }
                  break
                case 'number':
                case 'date':
                case 'array':
                case 'object':
                  ruleItem = {
                    type: ruleSymbol,
                    message: item.title + '类型必须为' + ruleSymbol,
                    trigger: 'blur'
                  }
                  break
                case 'min':
                  ruleItem.min = rule.split(':')[1]
                  ruleItem.message = item.title + '必须大于' + ruleItem.min
                  rule.trigger = 'blur'
                  break
                case 'max':
                  ruleItem.max = rule.split(':')[1]
                  if ('min' in ruleItem) {
                    ruleItem.message += '且小于' + ruleItem.max
                  } else {
                    ruleItem.message = item.title + '必须小于' + ruleItem.max
                    ruleItem.trigger = 'blur'
                  }
                  break
              }
              this.rules[group][item.name].push(ruleItem)
            })
          }
        })
      }
      this.row = row
    }
  }
}
</script>
<style lang="scss" scoped></style>
