<template>
  <!-- 搜索表单 -->
  <div class="wrap c-fs-fs">
    <div class="btn-box r-nw-sb-c">
      <i class="el-icon-search">&nbsp;&nbsp;筛选搜索</i>
      <el-button type="primary" size="mini" @click="check">查询</el-button>
    </div>
    <slot />
  </div>
</template>
<script>
export default {
  components: {},
  data() {
    return {}
  },
  computed: {
    device() {
      return this.$store.getters.device
    }
  },
  mounted() {},
  methods: {
    check() {
      this.$emit('check')
    }
  }
}

</script>
<style lang='scss' scoped>
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
    /deep/ .el-form-item {
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
