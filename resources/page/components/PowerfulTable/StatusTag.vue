<template>
  <el-tag
    :type="type"
    effect="dark"
  >
    {{ statusText }}
  </el-tag>
</template>
<script>
export default {
  props: {
    status: {
      type: Number,
      default: () => null
    },
    statusList: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      statusType: ['info', 'success', '', 'danger', 'warning']
    }
  },
  computed: {
    type() {
      if (this.statusType.hasOwnProperty(this.status)) {
        return this.statusType[this.status]
      } else {
        return this.statusType[Math.floor(Math.random() * 5)]
      }
    },
    statusText() {
      if (this.statusList.hasOwnProperty(this.status)) {
        return this.statusList[this.status]
      } else {
        return '无'
      }
    }
  }
}
</script>
