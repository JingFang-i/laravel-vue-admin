<template>
  <div class="empty-block">
    <el-image :src="src" alt="sad" fit="contain" />
    <span>没有找到相关数据</span>
  </div>
</template>
<script>
export default {
  data() {
    return {
      src: require('@/assets/images/sad.gif')
    }
  }
}
</script>
<style lang="scss" scoped>

  .empty-block {
    display: flex;
    flex-direction: row;
    justify-content: center;
    width: 100%;
    height: 200px;
    color: #aaa;
    text-align: center;
    .el-image {
      height: 100%;
    }
    span {
      height: 100%;
      line-height: 200px;
    }

  }
</style>
