<template>
  <div class="app-container">
    <el-collapse-transition>
      <search-box v-if="showSearchBox" class="search-box" @search="doSearch">
        <el-form class="form-box r-w-fs-c" label-position="left" size="mini" label-width="auto">
          <el-row type="flex">
            <template v-for="(item, key) in fields">
              <el-col v-if="item.searchable !== false" :key="key" :lg="6" :md="8" :sm="12" :xs="24" style="margin-top: 3px;">
                <el-form-item :label="item.label">
                  <el-select
                    v-if="['select', 'switch', 'radio'].indexOf(item.type) !== -1"
                    v-model="searchForm[item.field]"
                    :placeholder="item.placeholder"
                  >
                    <el-option v-for="(i, k) in item.selectList" :key="k" :label="i" :value="k" />
                  </el-select>
                  <!-- <el-date-picker
                    v-if="item.type === 'date'"
                    type="date"
                    :placeholder="item.placeholder"
                    v-model="item.query"
                  ></el-date-picker>-->
                  <el-date-picker
                    v-if="item.type === 'date'"
                    v-model="searchForm[item.field]"
                    type="datetimerange"
                    size="mini"
                    :picker-options="pickerOptions"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                    align="left"
                  />
                  <custom-select
                    v-if="item.type === 'custom-select'"
                    :resource="item.selectList"
                    :params="item.params"
                    :label-name="'labelName' in item ? item.labelName : 'name'"
                    :multiple="'multiple' in item ? item.multiple : false"
                    :selected.sync="searchForm[item.field]"
                  />
                  <group-select
                    v-if="item.type === 'group-select'"
                    :resource="item.selectList"
                    :label-name="'labelName' in item ? item.labelName : 'name'"
                    :multiple="'multiple' in item ? item.multiple : false"
                    :selected.sync="searchForm[item.field]"
                  />
                  <el-input
                    v-else-if="['date', 'select', 'custom-select', 'group-select', 'switch', 'radio'].indexOf(item.type) === -1"
                    v-model="searchForm[item.field]"
                    :placeholder="item.placeholder"
                  />
                </el-form-item>
              </el-col>
            </template>
          </el-row>
        </el-form>
      </search-box>
    </el-collapse-transition>
    <div class="tools">
      <el-button
        type="primary"
        size="mini"
        :loading="refreshLoading"
        icon="el-icon-refresh"
        @click="refresh"
      >刷新</el-button>
    </div>
    <div class="search r-w-fs-c">
      <el-input
        v-model="keywords"
        size="mini"
        :placeholder="quickSearchPlaceholder"
        style="width:200px;margin:5px 20px 5px 0"
      />
      <!--      <el-button type="primary" size="mini" icon="el-icon-search" @click="quickSearch"></el-button>-->
      <el-button type="primary" size="mini" icon="el-icon-box" @click="moreQuery">高级查询</el-button>

      <el-button
        v-if="operates.includes('add') && checkPermission(permissionRules.add)"
        type="primary"
        size="mini"
        icon="el-icon-plus"
        @click="handleAdd"
      >新增</el-button>
    </div>
    <div class="page-header">
      <slot />
    </div>
    <div class="table-box">
      <el-table
        ref="multipleTable"
        v-loading="tableLoading"
        :data="rows"
        tooltip-effect="dark"
        style="width: 100%"
        :max-height="maxHeight"
        :show-overflow-tooltip="true"
        row-key="id"
        border
        highlight-current-row
        :default-expand-all="defaultExpandAll"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" align="center" />
        <template v-for="(item, index) in fields">
          <el-table-column
            v-if="item.visible !== false && (!('type' in item) || columnType.indexOf(item.type) !== -1)"
            :key="index"
            :prop="item.field"
            :label="item.label"
            :width="item.width ? item.width : (item.field === 'id' ? 100 : null)"
            :align="item.align ? item.align : 'center'"
            header-align="center"
          />
          <el-table-column
            v-else-if="item.visible !== false && typeof item.formatter === 'function'"
            :key="index"
            :prop="item.field"
            :label="item.label"
            :formatter="item.formatter"
            :width="item.width"
            :align="item.align ? item.align : 'center'"
            header-align="center"
          />
          <el-table-column
            v-else-if="item.visible !== false"
            :key="index"
            :prop="item.field"
            :label="item.label"
            :width="item.width"
            :align="item.align ? item.align : 'center'"
            header-align="center"
          >
            <template slot-scope="scope">
              <el-rate
                v-if="item.type === 'rate'"
                v-model="scope.row[item.field]"
                :disabled="true"
                :colors="colors"
              />
              <el-switch
                v-if="item.type === 'switch'"
                v-model="scope.row[item.field]"
                active-color="#13ce66"
                inactive-color="#ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="toggle(scope.row.id, item.field, scope.row[item.field])"
              />
              <el-image
                v-if="item.type === 'image'"
                style="width: 80px; height: 80px"
                :src="getImgUrl(scope.row[item.field])"
                fit="cover"
                :preview-src-list="[getImgUrl(scope.row[item.field])]"
              />
              <el-image
                v-if="item.type === 'images'"
                style="width: 80px; height: 80px"
                :src="getImgUrl(scope.row[item.field].split(',')[0])||getImgUrl(scope.row[item.field])"
                fit="cover"
                :preview-src-list="scope.row[item.field].split(',').map(item => getImgUrl(item))"
              />
              <el-avatar
                v-if="item.type === 'avatar'"
                :size="60"
                :src="getImgUrl(scope.row[item.field])"
              >
                <img src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png" alt="">
              </el-avatar>
              <span
                v-if="item.type === 'price'"
                :style="item.style"
                :class="item.class"
              >¥{{ scope.row[item.field] }}</span>
              <status-tag
                v-if="item.type === 'status'"
                :status-list="item.selectList"
                :status="scope.row[item.field]"
              />
              <pre v-if="item.type === 'code'" style="overflow:auto;"><code>{{ JSON.stringify(scope.row[item.field], null, 4).replace(/\"/g, "") }}</code></pre>
              <span
                v-if="selectType.indexOf(item.type) !== -1"
              >{{ item.field + '_text' in scope.row ? scope.row[item.field + '_text'] : scope.row[item.field] }}</span>
              <el-link v-if="item.type === 'url'" type="success">{{ scope.row[item.field] }}</el-link>
            </template>
          </el-table-column>
        </template>
        <el-table-column v-if="operates.length!==0" label="操作" width="250">
          <template slot-scope="scope">
            <div class="r-nw-fs-c">
              <template v-for="(operate, key) in operatesButtons">
                <el-button
                  v-if="key < 2 && operate.name === 'drag'"
                  type="primary"
                  size="mini">
                  <svg-icon class="drag-handler" icon-class="drag" />
                </el-button>
                <el-button
                  v-else-if="key < 2 && operate.popover === false"
                  :key="key"
                  style="margin: 0 5px 0 0;float:left;"
                  :size="'size' in operate ? operate.size : 'mini'"
                  :type="'type' in operate ? operate.type : 'primary'"
                  :icon="operate.icon"
                  @click="operate.handle(scope.row.id, scope.row, operate.popover)"
                >
                  {{ operate.text }}
                </el-button>
                <el-popover
                  v-else-if="key < 2 && operate.popover === true"
                  v-model="popoverStatus['operate_' + scope.$index]"
                  placement="bottom"
                  style="float: left;margin-right: 5px;"
                >
                  <p>{{ operate.popoverOptions.content ? operate.popoverOptions.content : '确定要操作吗？' }}</p>
                  <div style="text-align: right; margin: 0">
                    <el-button size="mini" type="text" @click="cancelPopover('operate_' + scope.$index)">取消</el-button>
                    <el-button type="primary" size="mini" @click="operate.handle(scope.row.id, scope.row, true)">确定</el-button>
                  </div>
                  <el-button
                    slot="reference"
                    :icon="operate.icon"
                    :size="'size' in operate ? operate.size : 'mini'"
                    :type="'type' in operate ? operate.type : 'primary'"
                  >{{ operate.text }}</el-button>
                </el-popover>
              </template>
              <el-dropdown
                v-if="operatesButtons.length > 2"
                trigger="click"
                style="float:left;"
                @command="operateCommand"
              >
                <el-button type="primary" size="mini">
                  <i class="el-icon-more"></i>
                  <i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                  <template v-for="(item, index) in operatesButtons">
                    <el-dropdown-item
                      v-if="index > 1"
                      :key="index"
                      :command="{id: scope.row.id, row: scope.row, handle: item.handle}"
                    >{{ item.text }}</el-dropdown-item>
                  </template>
                </el-dropdown-menu>
              </el-dropdown>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-drawer
      :title="formTitle"
      :size="size"
      :visible.sync="drawer"
      destroy-on-close
      class="my-drawer"
    >
      <slot name="form" :fields="fields" :rules="rules" :row="editRow">
        <powerful-form
          v-if="operates.indexOf('edit') !== -1"
          :fields="fields"
          :rules="rules"
          :row="editRow"
          @submit="submit"
        />
      </slot>
    </el-drawer>
    <div class="page-box r-w-sb-c">
      <el-dropdown v-if="!disableBatch" trigger="click" @command="handleCommand">
        <el-button type="primary" size="mini">
          批量操作
          <i class="el-icon-arrow-down el-icon--right" />
        </el-button>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item v-if="deleteBatch && checkPermission(permissionRules.deleteBatch)" command="delete">批量删除</el-dropdown-item>
          <!--          TODO 批量操作待完成-->
          <template v-for="(o,k) in operation">
            <el-dropdown-item :key="k" :command="o">{{ o.lable }}</el-dropdown-item>
          </template>
        </el-dropdown-menu>
      </el-dropdown>
      <el-pagination
        background
        hide-on-single-page
        :pager-count="pagerCount"
        :small="device!=='desktop'"
        :current-page.sync="currentPage"
        :page-size="pageSize"
        :layout="layout"
        :total="total"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>
<script>
  import SearchBox from './SearchBox'
  import PowerfulForm from '@/components/PowerfulForm'
  import CustomSelect from '@/components/PowerfulForm/CustomSelect'
  import StatusTag from './StatusTag'
  import Sortable from 'sortablejs'
  import { getImgUrl } from '@/utils/helper'
  import { mapGetters } from 'vuex'
  import checkPermission from '@/utils/permission'

  export default {
    components: {
      SearchBox,
      PowerfulForm,
      CustomSelect,
      StatusTag
    },
    props: {
      fields: {
        type: Array,
        required: true
      },
      resource: {
        type: Function,
        required: true
      },
      update: {
        type: Function,
        default: null
      },
      updateBatch: {
        type: Function,
        default: null
      },
      deleteBatch: {
        type: Function,
        default: null
      },
      add: {
        type: Function,
        default: null
      },
      del: {
        type: Function,
        default: null
      },
      sort: {
        type: Function,
        default: null
      },
      operates: {
        type: Array,
        default: () => []
      },
      // 如果此属性存在，则每次编辑表单会调用此接口填充数据
      queryRow: {
        type: Function,
        default: null
      },
      // 定义操作按钮
      buttons: {
        type: Array,
        default: () => []
      },
      // 表单验证规则
      rules: {
        type: Object,
        default: () => ({})
      },
      formSize: {
        type: String,
        default: ''
      },
      show: {
        type: Boolean,
        default: false
      },
      // 如果在外部需要刷新列表，则需要将此属性设置为true
      needRefresh: {
        type: Boolean,
        default: false
      },
      detail: {
        type: Function,
        default: null
      },
      // 批量操作
      operation: {
        type: Array,
        default: () => []
      },
      // 快速查询字段
      quickSearchField: {
        type: String,
        default: 'id'
      },
      // 快速查询操作
      quickSearchOperate: {
        type: String,
        default: '='
      },
      quickSearchPlaceholder: {
        type: String,
        default: '根据ID快速查询'
      },
      defaultExpandAll: {
        type: Boolean,
        default: false
      },
      deletePopover: {
        type: Boolean,
        default: true
      },
      disableBatch: {
        type: Boolean,
        default: false
      },
      permissionRules: {
        type: Object,
        default: () => ({})
      }
    },
    data() {
      return {
        // 数据
        queryParams: {
          filter: {},
          operate: {}
        },
        defaultOperateButtons: [
          {
            name: 'drag',
          },
          {
            name: 'detail',
            text: '查看',
            icon: 'el-icon-info',
            popover: false,
            handle: id => {
              return this.handleDetail(id)
            }
          },
          {
            name: 'edit',
            text: '编辑',
            icon: 'el-icon-edit-outline',
            popover: false,
            handle: (id, row) => {
              return this.handleEdit(id, row)
            }
          },
          {
            name: 'delete',
            text: '删除',
            icon: 'el-icon-delete-solid',
            type: 'danger',
            popover: this.deletePopover,
            popoverOptions: {
              visible: false,
              content: '确定要删除吗？'
            },
            handle: (id, row, isPopover) => {
              return this.handleDelete(id, row, isPopover)
            }
          }
        ],
        popoverStatus: {},
        currentPopover: '',
        pickerOptions: {
          shortcuts: [
            {
              text: '最近一周',
              onClick(picker) {
                const end = new Date()
                const start = new Date()
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                picker.$emit('pick', [start, end])
              }
            },
            {
              text: '最近一个月',
              onClick(picker) {
                const end = new Date()
                const start = new Date()
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
                picker.$emit('pick', [start, end])
              }
            },
            {
              text: '最近三个月',
              onClick(picker) {
                const end = new Date()
                const start = new Date()
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
                picker.$emit('pick', [start, end])
              }
            }
          ]
        },
        searchForm: {},
        rows: [],
        editRow: {},
        columnType: ['text', 'date', 'textarea', 'icon', 'number'],
        selectType: ['select', 'custom-select', 'group-select'],
        total: 0,
        pageSize: 10,
        currentPage: 1,
        pagerCount: 5,
        formTitle: '',
        keywords: '',
        colors: ['#CA3024', '#CA3024', '#CA3024'], // 评分颜色
        seletedArr: [], // 选中的行数,
        changeRow: [], // 需要改变的变量值
        sortable: null,
        throttle: false,
        showSearchBox: false,
        tableLoading: false,
        drawer: false,
        refreshLoading: false,
        isRefresh: false
      }
    },
    computed: {
      ...mapGetters(['device']),
      layout() {
        return this.device === 'desktop'
          ? 'prev, pager, next, jumper'
          : 'prev, pager, next'
      },
      operatesButtons() {
        return this.defaultOperateButtons.filter(item => {
          return this.operates.includes(item.name)
        }).concat(this.buttons).filter(item => {
          return this.checkPermission(this.permissionRules[item.name])
        })
      },
      size() {
        return this.formSize
          ? this.formSize
          : this.device === 'desktop'
            ? '40%'
            : '90%'
      },
      maxHeight() {
        return document.documentElement.clientHeight - 300
      }
    },
    watch: {
      show: function(value) {
        if (!value) {
          this.drawer = value
        }
      },
      needRefresh: function(value) {
        if (value) {
          this.isRefresh = value
          this.refresh()
        }
      },
      drawer: function(value) {
        this.$emit('update:show', value)
      },
      keywords: function(value) {
        if (this.throttle) {
          this.throttle = false
          setTimeout(this.quickSearch, 500)
        }
      }
    },
    mounted() {
      this.changeHeight()
      window.onresize = () => {
        this.changeHeight()
      }
      this.getData()
    },
    destroyed() {
      window.onresize = null
    },
    methods: {
      getImgUrl,
      checkPermission,
      _initPopoverStatus(length) {
        for (let i = 0; i < length; i++) {
          this.$set(this.popoverStatus, 'operate_' + i, false)
        }
      },
      cancelPopover(key) {
        this.$set(this.popoverStatus, key, false)
      },
      // 批量操作
      handleCommand(commond) {
        if (this.seletedArr.length === 0) {
          this.$message.error('请选择要操作的数据')
        } else {
          const params = {
            ids: this.seletedArr
          }
          if (commond !== 'delete') {
            params.data = {
              [commond.name]: commond.value
            }
          }
          const methods =
            commond === 'delete' ? this.deleteBatch : this.updateBatch
          if (!(methods instanceof Function)) {
            this.$message.error('缺少操作方法，请先定义操作的方法')
            return
          }
          this.$confirm('确定要操作吗？').then(() => {
            methods(params)
              .then(res => {
                this.$message.success(
                  commond === 'delete' ? '删除成功' : '更新成功'
                )
                this.getData()
              })
              .catch(err => this.$message.error())
          })
        }
      },
      // 改变switch开关
      toggle(id, lable, value) {
        const params = {
          ids: [id],
          data: {
            [lable]: value
          }
        }
        if (!(this.updateBatch instanceof Function)) {
          this.$message.error('缺少更新操作方法，请先定义更新的方法')
          return false
        }
        this.updateBatch(params)
          .then(res => {
            this.getData()
          })
          .catch()
      },
      // 控制表单高度
      changeHeight() {
        this.$nextTick(() => {
          if (document.querySelector('.search-box')) {
            const windowh = document.documentElement.clientHeight
            const searchBoxH = document.querySelector('.search-box').clientHeight
            this.height = windowh - 260 - searchBoxH
          }
        })
      },
      // 根据条件查询
      doSearch() {
        this.fields.forEach(item => {
          if (item.field in this.searchForm && this.searchForm[item.field]) {
            this.queryParams.filter[item.field] = this.searchForm[item.field]
            this.queryParams.operate[item.field] =
              'operate' in item && item.operate ? item.operate : '='
          }
        })
        this.getData()
      },
      moreQuery() {
        this.showSearchBox = !this.showSearchBox
      },
      operateCommand({ id, row, handle }) {
        handle.call(this, id, row)
      },
      // 多选事件
      handleSelectionChange(e) {
        this.seletedArr = e.map(item => {
          return item.id
        })
      },
      // 分页
      handleCurrentChange(e) {
        this.currentPage = e
        this.getData()
      },
      // 新增
      handleAdd() {
        this.formTitle = '新增'
        this.drawer = true
        this.editRow = {}
      },
      // 详情
      handleDetail(id) {
        if (this.detail) {
          // this.detail.call(this, id)
          this.detail(id)
        } else {
          // TODO
          console.log('detail', id)
        }
      },
      // 编辑
      handleEdit(id, row) {
        this.formTitle = '编辑'

        if (this.queryRow) {
          this.queryRow(id).then(res => {
            this.editRow = res.data
            this.drawer = true
          })
        } else {
          this.editRow = Object.assign({}, row)
          this.drawer = true
        }
      },
      // 删除
      handleDelete(id, row, isPopover) {
        if (isPopover) {
          this.del(parseInt(id))
            .then(res => {
              this.getData()
            })
            .catch()
        } else {
          this.$confirm('确定要删除吗?')
            .then(() => {
              this.del(parseInt(id))
                .then(res => {
                  this.getData()
                })
                .catch()
            })
            .catch(err => console.log(err))
        }
      },
      // 快速查询 默认为ID查询
      quickSearch() {
        if (this.keywords) {
          this.queryParams.filter[this.quickSearchField] = this.keywords
          this.queryParams.operate[this.quickSearchField] = this.quickSearchOperate
        } else {
          this.queryParams.filter = {}
          this.queryParams.operate = {}
        }
        this.getData()
      },
      refresh() {
        this.refreshLoading = true
        this.queryParams.filter = {}
        this.queryParams.operate = {}
        this.getData()
        this.isRefresh = false
        this.$emit('update:needRefresh', false)
      },
      // 获取数据
      async getData() {
        // 带上查询参数
        const params = {
          page: this.currentPage,
          ...this.queryParams
        }
        this.tableLoading = true
        try {
          const { data } = await this.resource(params)
          if (data instanceof Array) {
            this.rows = data
          } else {
            this.rows = data.data
            this.total = data.meta.pagination.total
            this.pageSize = data.meta.pagination.per_page
          }
          this._initPopoverStatus(this.rows.length)
          this.throttle = true
        } catch (err){
          this.$message.error('发生错误')
        }
        // .then(res => {
        //   if (res.data instanceof Array) {
        //     this.rows = res.data
        //   } else {
        //     this.rows = res.data.data
        //     this.total = res.data.meta.pagination.total
        //     this.pageSize = res.data.meta.pagination.per_page
        //   }
        // })
        // .catch(err => {)
        // .finally(() => {
        //  this.tableLoading = false
        //   this.refreshLoading = false
        // })

        this.tableLoading = false
        this.refreshLoading = false
        this.$nextTick(() => {
          this.setSort()
        })
      },
      // 提交表单
      submit(row) {
        const action = parseInt(row.id)
          ? this.update(parseInt(row.id), row)
          : this.add(row)
        action
          .then(res => {
            this.drawer = false
            this.getData()
            this.editRow = {}
          })
          .catch()
      },
      setSort() {
        const el = this.$refs.multipleTable.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
        this.sortable = Sortable.create(el, {
          ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
          setData: function(dataTransfer) {
            // to avoid Firefox bug
            // Detail see : https://github.com/RubaXa/Sortable/issues/1012
            dataTransfer.setData('Text', '')
          },
          onEnd: evt => {
            const params = {
              old_id: this.rows[evt.oldIndex].id,
              new_id: this.rows[evt.newIndex].id,
            }
            this.sort(params).then(res => {
              this.$message.success('更新成功')
            }).catch()
          }
        })
      }
    }
  }
</script>
<style lang='scss' scoped>
  .app-container {
    .page-box {
      height: 110px;
      background: white;
      padding-top: 10px;
      border-radius: 0 0 10px 10px;
      .el-pagination {
        text-align: center;
      }
    }
    .search {
      float: right;
      ::v-deep.photoSelect {
        input {
          width: 200px;
          height: 28px;
        }
      }
    }
    .el-form-item {
      ::v-deep.el-date-editor--datetimerange.el-input, .el-date-editor--datetimerange.el-input__inner {
        width: 100%;
      }
    }
    .tools {
      float: left;
      margin: 10px 0 10px 0;
    }
    .page-header {
      float: left;
      margin-bottom: 10px;
      width: 100%;
    }
  }
</style>
