<template>
  <div class="empty-block">
    <img :src="src" alt="sad"/>
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
    flex-direction: column;
    justify-content: center;
    width: 100%;
    color: #aaa;
    text-align: center;
    img {
      object-fit: contain;
      height: 35px;
    }
  }
</style>
