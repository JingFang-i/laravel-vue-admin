<template>
  <div class="key-value-table">
    <el-button type="primary" size="mini" @click="handleAdd">添加一项</el-button>
    <el-table :data="formatedData">
      <el-table-column prop="key" label="键">
        <template v-slot="scope">
          <el-input v-model="scope.row.key" />
        </template>
      </el-table-column>
      <el-table-column prop="value" label="值">
        <template v-slot="scope">
          <el-input v-model="scope.row.value" />
        </template>
      </el-table-column>
      <el-table-column label="操作">
        <template v-slot="scope">
          <el-button
            v-if="!scope.row.id && scope.$index != 0"
            type="primary"
            size="mini"
            @click="handleDel(scope.$index)"
          >删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
export default {
  props: {
    data: {
      type: [Array, Object],
      required: true,
      default: () => []
    }
  },
  data() {
    return {
      formatedData: []
    }
  },
  watch: {
    formatedData: {
      deep: true,
      handler: function(value) {
        const updateData = {}
        value.forEach(item => {
          if (item.key) {
            updateData[item.key] = item.value
          }
        })
        this.$emit('update:data', updateData)
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.formatData()
    })
  },
  methods: {
    handleAdd() {
      this.formatedData.push({ key: '', value: '' })
    },
    handleDel(index) {
      this.formatedData.splice(index, 1)
    },
    formatData() {
      const data = []
      if (this.data instanceof Array) {
        this.data.forEach((item, index) => {
          data.push({ key: index, value: item })
        })
      } else if (this.data instanceof Object) {
        for (const value of Object.entries(this.data)) {
          data.push({ key: value[0], value: value[1] })
        }
      }
      this.formatedData = data
      this.handleAdd()
    }
  }
}
</script>
<style lang="scss" scope>
.key-value-table {
  ::v-deep {
    .el-table__header-wrapper {
      border-radius: 10px 10px 0px 0px;
    }
    .has-gutter {
      .is-leaf {
        height: 53px;
        text-align: center;
      }
    }
    .el-table__row td {
      text-align: center;
    }
  }
}
</style>
