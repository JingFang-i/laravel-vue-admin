<template>
  <div class="app-container">
    <el-collapse-transition>
      <search-box
          v-if="showSearchBox"
          class="search-box"
          @search="doSearch"
          @reset="reset"
          style="box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1); margin-bottom: 5px"
      >
        <el-form
            class="form-box r-w-fs-c"
            label-position="left"
            size="mini"
            label-width="auto"
        >
          <el-row>
            <template v-for="(item, key) in fields">
              <el-col
                  v-if="item.searchable !== false"
                  :key="key"
                  :lg="6"
                  :md="8"
                  :sm="12"
                  :xs="24"
                  style="margin-top: 8px"
              >
                <el-form-item :label="item.label">
                  <el-select
                      v-if="
                      ['select', 'switch', 'radio', 'status'].includes(
                        item.type
                      )
                    "
                      v-model="searchForm[item.field]"
                      :placeholder="item.placeholder"
                  >
                    <el-option
                        v-for="(i, k) in item.selectList"
                        :key="k"
                        :label="i"
                        :value="k"
                    />
                  </el-select>
                  <!-- <el-date-picker
                        v-if="item.type === 'date'"
                        type="date"
                        :placeholder="item.placeholder"
                        v-model="item.query"
                      ></el-date-picker>-->
                  <el-date-picker
                      v-if="item.type === 'date' || item.type === 'datetime'"
                      v-model="searchForm[item.field]"
                      type="datetimerange"
                      size="mini"
                      :picker-options="pickerOptions"
                      range-separator="至"
                      start-placeholder="开始日期"
                      end-placeholder="结束日期"
                      :format="datetimeFormat"
                      :value-format="datetimeFormat"
                      align="left"
                  />
                  <custom-select
                      v-if="item.type === 'custom-select'"
                      :resource="item.selectList"
                      :params="item.params"
                      :label-name="'labelName' in item ? item.labelName : 'name'"
                      :multiple="'multiple' in item ? item.multiple : false"
                      v-model="searchForm[item.field]"
                  />
                  <group-select
                      v-if="item.type === 'group-select'"
                      :resource="item.selectList"
                      :label-name="'labelName' in item ? item.labelName : 'name'"
                      :multiple="'multiple' in item ? item.multiple : false"
                      :selected.sync="searchForm[item.field]"
                  />
                  <el-input
                      v-else-if="
                      ![
                        'date',
                        'select',
                        'custom-select',
                        'group-select',
                        'switch',
                        'radio',
                        'datetime',
                        'status',
                        'areas'
                      ].includes(item.type)
                    "
                      v-model="searchForm[item.field]"
                      :placeholder="item.placeholder"
                  />
                  <slot name="search-input" :item="item" :search-form="searchForm"></slot>
                </el-form-item>
              </el-col>
            </template>
          </el-row>
        </el-form>
      </search-box>
    </el-collapse-transition>
    <el-card class="clearfix">
      <div class="tools">
        <el-button
            type="primary"
            size="mini"
            :loading="refreshLoading"
            icon="el-icon-refresh"
            @click="refresh"
        >刷新</el-button
        >
        <el-dropdown
            v-if="!disableBatch"
            trigger="click"
            @command="handleCommand"
        >
          <el-button size="mini" type="primary"
          >批量操作<i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <template v-for="(o, k) in batchOperations">
              <el-dropdown-item :key="k" :command="o"
              ><i :class="o.icon ? o.icon : 'el-icon-magic-stick'"></i
              >{{ o.label }}</el-dropdown-item
              >
            </template>
            <el-dropdown-item
                v-if="deleteBatch && checkPermission(permissionRules.deleteBatch)"
                command="delete"
            ><i class="el-icon-delete"></i>批量删除</el-dropdown-item
            >
          </el-dropdown-menu>
        </el-dropdown>
        <slot name="tools"></slot>
      </div>
      <div class="search r-w-fs-c">
        <el-input
            v-if="enableSearch"
            v-model="keywords"
            size="mini"
            :placeholder="quickSearchPlaceholder"
            style="width: 200px; margin: 5px 20px 5px 0"
            :clearable="true"
        />
        <!--      <el-button type="primary" size="mini" icon="el-icon-search" @click="quickSearch"></el-button>-->
        <el-button
            v-if="enableSearch"
            type="primary"
            size="mini"
            icon="el-icon-box"
            @click="moreQuery"
        >高级查询</el-button
        >

        <el-button
            v-if="
            operations.includes('add') && checkPermission(permissionRules.add)
          "
            type="primary"
            size="mini"
            icon="el-icon-plus"
            @click="handleAdd"
        >新增</el-button
        >
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
            resizable
            :stripe="true"
            highlight-current-row
            :default-expand-all="defaultExpandAll"
            @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55" align="center" />
          <template v-for="(item, index) in fields">
            <el-table-column
                v-if="
                ((typeof item.visible === 'function' &&
                  item.visible.call(this)) ||
                  item.visible !== false) &&
                (!('type' in item) || columnType.includes(item.type)) &&
                typeof item.formatter !== 'function'
              "
                :key="index"
                :prop="item.field"
                :label="item.label"
                :width="
                item.width ? item.width : item.field === 'id' ? 100 : null
              "
                :align="item.align ? item.align : 'center'"
                header-align="center"
            />

            <el-table-column
                v-else-if="
                ((typeof item.visible === 'function' &&
                  item.visible.call(this)) ||
                  item.visible !== false) &&
                typeof item.formatter === 'function'
              "
                :key="index"
                :prop="item.field"
                :label="item.label"
                :formatter="item.formatter"
                :width="item.width"
                :align="item.align ? item.align : 'center'"
                header-align="center"
            />
            <el-table-column
                v-else-if="
                (typeof item.visible === 'function' &&
                  item.visible.call(this)) ||
                item.visible !== false
              "
                :key="index"
                :prop="item.field"
                :label="item.label"
                :width="item.width"
                :align="item.align ? item.align : 'center'"
                header-align="center"
            >
              <template slot-scope="scope">
                <span
                    v-if="item.template"
                    v-html="item.template(scope.row)"
                ></span>
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
                    :active-value="item.active !== undefined ? item.active : 1"
                    :inactive-value="item.inactive !== undefined ? item.inactive : 0"
                    @change="
                    toggle(scope.row.id, item.field, scope.row[item.field])
                  "
                />
                <el-image
                    v-if="item.type === 'image'"
                    style="width: 50px; height: 50px"
                    :src="
                    scope.row[item.field]
                      ? getImgUrl(scope.row[item.field])
                      : ''
                  "
                    fit="contain"
                    :preview-src-list="[getImgUrl(scope.row[item.field])]"
                />
                <el-image
                    v-if="item.type === 'images'"
                    style="width: 50px; height: 50px"
                    :src="
                    scope.row[item.field]
                      ? getImgUrl(scope.row[item.field][0])
                      : ''
                  "
                    fit="contain"
                    :preview-src-list="
                    scope.row[item.field]
                      ? scope.row[item.field].map((path) => getImgUrl(path))
                      : []
                  "
                />
                <el-link
                    v-if="item.type === 'file'"
                    type="info"
                    icon="el-icon-files"
                    :href="
                    scope.row[item.field]
                      ? getImgUrl(scope.row[item.field])
                      : ''
                  "
                    target="_blank"
                >下载</el-link
                >
                <el-avatar
                    v-if="item.type === 'avatar'"
                    :size="60"
                    :src="
                    scope.row[item.field]
                      ? getImgUrl(scope.row[item.field])
                      : ''
                  "
                >
                  <img
                      src="https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png"
                      alt=""
                  />
                </el-avatar>
                <span
                    v-if="item.type === 'price'"
                    :style="item.style"
                    :class="item.class"
                >¥{{ scope.row[item.field] }}</span
                >
                <status-tag
                    v-if="item.type === 'status'"
                    :status-list="item.selectList"
                    :status="scope.row[item.field]"
                />

                <pre
                    v-if="item.type === 'json'"
                    style="overflow: auto"
                ><code>{{ JSON.stringify(scope.row[item.field], null, 4).replace(/\"/g, "") }}</code></pre>
                <span v-if="selectType.includes(item.type)">{{
                    item.field + '_text' in scope.row
                        ? scope.row[item.field + '_text']
                        : scope.row[item.field]
                  }}</span>

                <el-link v-if="item.type === 'url'" type="success">{{
                    scope.row[item.field]
                  }}</el-link>
                <slot
                    name="table-column"
                    :item="item"
                    :value="scope.row[item.field]"
                    :row="scope.row"
                ></slot>
              </template>
            </el-table-column>
          </template>
          <el-table-column
              v-if="operations.length !== 0"
              label="操作"
              width="250"
          >
            <template slot-scope="scope">
              <div class="r-nw-fs-c">
                <template v-for="(operate, key) in operationButtons">
                  <el-button
                      v-if="key < 2 && operate.name === 'sort'"
                      type="primary"
                      style="margin: 0 5px 0 0; float: left"
                      size="mini"
                  >
                    <svg-icon class="drag-handler" icon-class="drag" />
                  </el-button>
                  <el-button
                      v-else-if="
                      key < 2 &&
                      operate.popover === false &&
                      (typeof operate.visible === 'function'
                        ? operate.visible.call(this, scope.row)
                        : operate.visible !== false)
                    "
                      :key="key"
                      style="margin: 0 5px 0 0; float: left"
                      :size="'size' in operate ? operate.size : 'mini'"
                      :type="'type' in operate ? operate.type : 'primary'"
                      :icon="operate.icon"
                      @click="
                      operate.handle.call(
                        this,
                        scope.row.id,
                        scope.row,
                        operate.popover
                      )
                    "
                  >
                    {{ operate.text }}
                  </el-button>
                  <el-popconfirm
                      v-else-if="
                      key < 2 &&
                      operate.popover === true &&
                      (typeof operate.visible === 'function'
                        ? operate.visible.call(this, scope.row)
                        : operate.visible !== false)
                    "
                      :title="
                      operate.popoverOptions && operate.popoverOptions.content
                        ? operate.popoverOptions.content
                        : '确定要操作吗？'
                    "
                      @onConfirm="
                      operate.handle.call(this, scope.row.id, scope.row, true)
                    "
                  >
                    <el-button
                        slot="reference"
                        :icon="operate.icon"
                        :size="'size' in operate ? operate.size : 'mini'"
                        :type="'type' in operate ? operate.type : 'primary'"
                        style="margin: 0 5px 0 0; float: left"
                    >{{ operate.text }}</el-button
                    >
                  </el-popconfirm>
                </template>
                <el-dropdown
                    v-if="operationButtons.length > 2"
                    trigger="click"
                    style="float: left"
                    @command="operateCommand"
                >
                  <el-button type="primary" size="mini">
                    <i class="el-icon-more" />
                    <i class="el-icon-arrow-down el-icon--right" />
                  </el-button>
                  <el-dropdown-menu slot="dropdown">
                    <template v-for="(item, index) in operationButtons">
                      <el-dropdown-item
                          v-if="
                          index > 1 &&
                          (typeof item.visible === 'function'
                            ? item.visible.call(this, scope.row)
                            : item.visible !== false)
                        "
                          :key="index"
                          :command="{
                          id: scope.row.id,
                          row: scope.row,
                          handle: item.handle,
                        }"
                      >{{ item.text }}</el-dropdown-item
                      >
                    </template>
                  </el-dropdown-menu>
                </el-dropdown>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>
    <el-drawer
        :title="formTitle"
        :size="size"
        :append-to-body="true"
        :visible.sync="drawer"
        destroy-on-close
    >
      <powerful-form
          v-if="operations.includes('add') || operations.includes('edit')"
          :fields="fields"
          :rules="rules"
          :row="editRow"
          @submit="submit"
          @change="rowChange"
      >
        <template v-slot:default="{ item, row }">
          <slot name="form-item" :item="item" :row="row"></slot>
        </template>
      </powerful-form>
    </el-drawer>
    <div class="page-box r-w-sb-c">
      <el-pagination
          background
          hide-on-single-page
          :pager-count="pagerCount"
          :small="device !== 'desktop'"
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
import SearchBox from './SearchBox';
import PowerfulForm from '@/components/PowerfulForm';
import CustomSelect from '@/components/PowerfulForm/CustomSelect';
import StatusTag from './StatusTag';
import Sortable from 'sortablejs';
import { getImgUrl } from '@/utils/helper';
import { mapGetters } from 'vuex';
import checkPermission from '@/utils/permission';

export default {
  components: {
    SearchBox,
    PowerfulForm,
    CustomSelect,
    StatusTag,
  },
  props: {
    fields: {
      type: Array,
      required: true,
    },
    resource: {
      type: Function,
      required: true,
    },
    update: {
      type: Function,
      default: null,
    },
    updateBatch: {
      type: Function,
      default: null,
    },
    deleteBatch: {
      type: Function,
      default: null,
    },
    add: {
      type: Function,
      default: null,
    },
    del: {
      type: Function,
      default: null,
    },
    sort: {
      type: Function,
      default: null,
    },
    operations: {
      type: Array,
      default: () => [],
    },
    // 如果此属性存在，则每次编辑表单会调用此接口填充数据
    queryRow: {
      type: Function,
      default: null,
    },
    // 定义操作按钮
    buttons: {
      type: Array,
      default: () => [],
    },
    // 表单验证规则
    rules: {
      type: Object,
      default: () => ({}),
    },
    formSize: {
      type: String,
      default: '',
    },
    show: {
      type: Boolean,
      default: false,
    },
    // 如果在外部需要刷新列表，则需要将此属性设置为true
    needRefresh: {
      type: Boolean,
      default: false,
    },
    detail: {
      type: Function,
      default: null,
    },
    // 批量操作
    batchOperations: {
      type: Array,
      default: () => [],
    },
    enableSearch: {
      type: Boolean,
      default: true,
    },
    // 快速查询字段
    quickSearchField: {
      type: String,
      default: 'id',
    },
    // 快速查询操作
    quickSearchOperation: {
      type: String,
      default: '=',
    },
    quickSearchPlaceholder: {
      type: String,
      default: '根据ID快速查询',
    },
    defaultExpandAll: {
      type: Boolean,
      default: false,
    },
    deletePopover: {
      type: Boolean,
      default: true,
    },
    disableBatch: {
      type: Boolean,
      default: false,
    },
    permissionRules: {
      type: Object,
      default: () => ({}),
    },
    addHandle: {
      type: Function,
      default: null,
    },
    defaultQueryParams: {
      type: Object,
      default: null,
    },
    buttonVisibleCall: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      defaultOperateButtons: [
        {
          name: 'sort',
        },
        {
          name: 'detail',
          text: '查看',
          icon: 'el-icon-info',
          popover: false,
          handle: (id, row) => {
            return this.handleDetail(id, row);
          },
        },
        {
          name: 'edit',
          text: '编辑',
          icon: 'el-icon-edit-outline',
          popover: false,
          handle: (id, row) => {
            return this.handleEdit(id, row);
          },
        },
        {
          name: 'del',
          text: '删除',
          icon: 'el-icon-delete-solid',
          type: 'danger',
          popover: this.deletePopover,
          popoverOptions: {
            visible: false,
            content: '确定要删除吗？',
          },
          handle: (id, row, isPopover) => {
            return this.handleDelete(id, row, isPopover);
          },
        },
      ],
      popoverStatus: {},
      currentPopover: '',
      datetimeFormat: 'yyyy-MM-dd HH:mm:ss',
      pickerOptions: {
        shortcuts: [
          {
            text: '最近一周',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            },
          },
          {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            },
          },
          {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            },
          },
        ],
      },
      searchForm: {},
      rows: [],
      callbacks: [],
      editRow: {},
      queryParams: {
        filter: {},
        operate: {},
      },
      columnType: ['text', 'date', 'datetime', 'textarea', 'icon', 'number'],
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
      formType: 1, // 表单是新增还是编辑 1=新增　2=编辑
      sortable: null,
      throttle: false,
      showSearchBox: false,
      tableLoading: false,
      drawer: this.show,
      refreshLoading: false,
      isRefresh: false,
    };
  },
  computed: {
    ...mapGetters(['device']),
    layout() {
      return this.device === 'desktop'
          ? 'prev, pager, next, jumper'
          : 'prev, pager, next';
    },
    operationButtons() {
      let operations = this.defaultOperateButtons.filter((item) => {
        return this.operations.includes(item.name);
      });

      let replaceOperations = [];
      this.buttons.forEach((button) => {
        operations.forEach((item, index) => {
          if (item.name === button.name) {
            replaceOperations.push(button.name);
            operations.splice(index, 1, button);
          }
        });
      });

      this.buttons.forEach((item) => {

        if (!replaceOperations.includes(item.name)) {
          operations.push(item);
        }
      });
      operations = operations.filter((item) => {
        return this.checkPermission(this.permissionRules[item.name]);
      });
      operations = operations.map(item => {
        if (item.name in this.buttonVisibleCall) {
          item.visible = this.buttonVisibleCall[item.name]
        }
        return item
      })
      return operations;
    },
    size() {
      return this.formSize
          ? this.formSize
          : this.device === 'desktop'
              ? '40%'
              : '90%';
    },
    maxHeight() {
      return document.documentElement.clientHeight - 300;
    },
  },
  watch: {
    show: function (value) {
      this.drawer = value
      if (value) {
        this.$nextTick(function () {
          this.initEditRow(false);
        })
      }
    },
    needRefresh: function (value) {
      if (value) {
        this.isRefresh = value;
        this.refresh();
      }
    },
    drawer: function (value) {
      this.$emit('update:show', value);
    },
    keywords: function (value) {
      if (this.throttle) {
        this.throttle = false;
        setTimeout(this.quickSearch, 500);
      }
    },
  },
  created() {
    // this.initEditRow(true);
  },
  mounted() {
    this.changeHeight();
    window.onresize = () => {
      this.changeHeight();
    };
    this.$nextTick(function () {
      this.getData();
    });
  },
  destroyed() {
    window.onresize = null;
  },
  methods: {
    getImgUrl,
    checkPermission,
    _initPopoverStatus(length) {
      for (let i = 0; i < length; i++) {
        this.$set(this.popoverStatus, 'operate_' + i, false);
      }
    },
    cancelPopover(key) {
      this.$set(this.popoverStatus, key, false);
    },
    // 当表单值改变时, 调用回调方法
    rowChange({ changedValues, editRow }) {
      changedValues.forEach((item) => {
        if (this.callbacks[item.field]) {
          this.callbacks[item.field].call(this, item.value, editRow);
        }
      });
    },
    // 批量操作
    handleCommand(command) {
      if (this.seletedArr.length === 0) {
        this.$message.error('请选择要操作的数据');
      } else {
        const params = {
          ids: this.seletedArr,
        };
        if (command !== 'delete') {
          if (command.custom === true) {
            command.handle.call(this, this.seletedArr);
            return;
          } else {
            params.data = {
              [command.name]: command.value,
            };
          }
        }
        const methods =
            command === 'delete' ? this.deleteBatch : this.updateBatch;
        if (!(methods instanceof Function)) {
          this.$message.error('缺少操作方法，请先定义操作的方法');
          return;
        }
        this.$confirm('确定要操作吗？').then(() => {
          methods(params)
              .then((res) => {
                this.$message.success(
                    command === 'delete' ? '删除成功' : '更新成功'
                );
                this.getData();
              })
              .catch((err) => this.$message.error());
        });
      }
    },
    // 改变switch开关
    toggle(id, lable, value) {
      const params = {
        ids: [id],
        data: {
          [lable]: value,
        },
      };
      if (!(this.updateBatch instanceof Function)) {
        this.$message.error('缺少更新操作方法，请先定义更新的方法');
        return false;
      }
      this.updateBatch(params)
          .then((res) => {
            this.getData();
          })
          .catch();
    },
    // 控制表单高度
    changeHeight() {
      this.$nextTick(() => {
        if (document.querySelector('.search-box')) {
          const windowh = document.documentElement.clientHeight;
          const searchBoxH = document.querySelector('.search-box').clientHeight;
          this.height = windowh - 260 - searchBoxH;
        }
      });
    },
    // 根据条件查询
    doSearch() {
      this.fields.forEach((item) => {
        if (item.field in this.searchForm && this.searchForm[item.field] !== '' && this.searchForm[item.field] !== null) {
          this.queryParams.filter[item.field] = this.searchForm[item.field]
          this.queryParams.operate[item.field] =
            'operate' in item && item.operate ? item.operate : '='
          if (['date', 'datetime'].includes(item.type)) {
            this.queryParams.operate[item.field] = 'range'
          }
        }
      });
      this.currentPage = 1;
      this.getData();
    },
    reset() {
      this.currentPage = 1;
      this.searchForm = {};
      this.refresh();
    },
    moreQuery() {
      this.showSearchBox = !this.showSearchBox;
    },
    operateCommand({ id, row, handle }) {
      handle.call(this, id, row);
    },
    // 多选事件
    handleSelectionChange(e) {
      this.seletedArr = e.map((item) => {
        return item.id;
      });
      this.$emit('selection-change', e);
    },
    // 分页
    handleCurrentChange(e) {
      this.currentPage = e;
      this.getData();
    },
    // 新增
    handleAdd() {
      this.formType = 1;
      if (this.addHandle) {
        this.addHandle();
      } else {
        this.formTitle = '新增';
        this.drawer = true;
      }
      this.initEditRow(false);
    },
    initEditRow(needResetCallbacks) {
      let expectFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
      let editRow = {};
      this.fields.forEach((item) => {
        if (typeof item.editable === 'function' || item.editable === false) {
          return false;
        }
        if (needResetCallbacks) {
          if (item.callback && typeof item.callback === 'function') {
            this.callbacks[item.field] = item.callback;
          }
        }
        if (!expectFields.includes(item.field)) {
          if (item.default) {
            editRow[item.field] = item.default;
          } else {
            editRow[item.field] = '';
          }
        }
      });
      this.editRow = editRow;
    },
    // 详情
    handleDetail(id, row) {
      if (this.detail) {
        this.detail(id, row);
      } else {
        // TODO
        console.log('detail', id);
      }
    },
    // 编辑
    handleEdit(id, row) {
      this.formTitle = '编辑';
      this.formType = 2;

      if (this.queryRow) {
        this.queryRow(id).then((res) => {
          this.editRow = res.data;
          this.drawer = true;
        });
      } else {
        this.editRow = Object.assign({}, row);
        this.drawer = true;
      }
    },
    // 删除
    handleDelete(id, row, isPopover) {
      if (isPopover) {
        this.del(parseInt(id))
            .then((res) => {
              this.getData();
            })
            .catch();
      } else {
        this.$confirm('确定要删除吗?')
            .then(() => {
              this.del(parseInt(id))
                  .then((res) => {
                    this.getData();
                  })
                  .catch();
            })
            .catch((err) => console.log(err));
      }
    },
    // 快速查询 默认为ID查询
    quickSearch() {
      if (this.keywords) {
        this.queryParams.filter[this.quickSearchField] = this.keywords;
        this.queryParams.operate[
            this.quickSearchField
            ] = this.quickSearchOperation;
      } else {
        this.queryParams.filter = {};
        this.queryParams.operate = {};
      }
      this.getData();
    },
    refresh() {
      this.refreshLoading = true;
      this.queryParams.filter = {};
      this.queryParams.operate = {};
      this.getData();
      this.isRefresh = false;
      this.$emit('update:needRefresh', false);
    },
    // 获取数据
    async getData() {
      // 带上查询参数
      let params = {
        page: this.currentPage,
        ...this.queryParams,
      };
      if (this.defaultQueryParams) {
        for (let [field, value] of Object.entries(this.defaultQueryParams)) {
          if (field === 'filter') {
            params.filter = Object.assign(
                params.filter,
                this.defaultQueryParams.filter
            );
          } else if (field === 'operate') {
            params.operate = Object.assign(
                params.operate,
                this.defaultQueryParams.operate
            );
          } else {
            params[field] = value;
          }
        }
      }
      this.tableLoading = true;
      try {
        const { data } = await this.resource(params);
        if (data instanceof Array) {
          this.rows = data;
          this.total = 0;
        } else {
          this.rows = data.data;
          this.total = data.meta.pagination.total;
          this.pageSize = data.meta.pagination.per_page;
        }
        // this._initPopoverStatus(this.rows.length)
        this.throttle = true;
      } catch (err) {}

      this.tableLoading = false;
      this.refreshLoading = false;
      if (this.operations.includes('sort')) {
        this.$nextTick(() => {
          this.setSort();
        });
      }
    },
    // 提交表单
    submit(row) {
      const action = parseInt(row.id)
          ? this.update(parseInt(row.id), row)
          : this.add(row);
      action
          .then((res) => {
            this.drawer = false;
            this.getData();
            this.editRow = {};
          })
          .catch();
    },
    setSort() {
      const el = this.$refs.multipleTable.$el.querySelectorAll(
          '.el-table__body-wrapper > table > tbody'
      )[0];
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
        setData: function (dataTransfer) {
          // to avoid Firefox bug
          // Detail see : https://github.com/RubaXa/Sortable/issues/1012
          dataTransfer.setData('Text', '');
        },
        onEnd: (evt) => {
          if (evt.oldIndex === evt.newIndex) {
            return false;
          }
          const params = {
            old_id: this.rows[evt.oldIndex].id,
            new_id: this.rows[evt.newIndex].id,
          };
          this.sort(params)
              .then((res) => {
                this.refresh();
                this.$message.success('更新成功');
              })
              .catch();
        },
      });
    },
  },
};
</script>
<style lang="scss" scoped>
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
    ::v-deep.el-date-editor--datetimerange.el-input,
    .el-date-editor--datetimerange.el-input__inner {
      width: 100%;
    }
  }
  .tools {
    display: flex;
    float: left;
    flex-direction: row;
    > * {
      margin-right: 10px;
    }
  }
  .page-header {
    float: left;
    margin-bottom: 10px;
    width: 100%;
  }
}
</style>
