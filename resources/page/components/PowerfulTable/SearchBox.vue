<template>
  <!-- 搜索表单 -->
  <div class="wrap c-fs-fs clearfix">
    <div class="btn-box r-nw-sb-c">
      <i class="el-icon-search">&nbsp;&nbsp;筛选搜索</i>
    </div>
    <slot />
    <el-button
      type="primary"
      size="mini"
      style="float: right;margin: 0 10px 10px 0"
      @click="doSearch"
      >查询</el-button
    >
    <el-button
      type="default"
      size="mini"
      style="float: right;margin-right: 5px;margin: 0 10px 10px 0"
      @click="reset"
      >重置</el-button
    >
  </div>
</template>
<script>
export default {
  methods: {
    doSearch() {
      this.$emit('search')
    },
    reset() {
      this.$emit('reset')
    }
  }
}
</script>
<style lang="scss" scoped>
.wrap {
  width: 100%;
  height: auto;
  border-radius: 10px;
  .btn-box {
    width: 100%;
    padding: 10px 30px;
    .el-button {
      float: right;
    }
  }
  .form-box {
    width: 100%;
    padding: 5px 50px;
    ::v-deep .el-form-item {
      margin: 0 10px;
    }
  }
  @media screen and(max-width: 1024px) {
    .form-box {
      padding: 5px 20px;
    }
  }
}
</style>
