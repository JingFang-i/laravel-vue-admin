<template>
  <div class="block">
    <el-timeline>
      <el-timeline-item v-for="(item,index) of timeline" :key="index" :timestamp="item.created_at" placement="top">
        <el-card>
          <h4>{{ item.title }}</h4>
          <p><strong>{{ item.content.method }}</strong>&nbsp;&nbsp;{{ item.content.url }}</p>
          <p>{{ item.content.name }}</p>
        </el-card>
      </el-timeline-item>
    </el-timeline>
  </div>
</template>

<script>
import { lists } from '@/api/system/admin-log'
export default {
  data() {
    return {
      timeline: []
    }
  },
  mounted() {
    this.getAdminLog()
  },
  methods: {
    getAdminLog() {
      const params = {
        filter: { admin_id: this.$store.getters.userId },
        operate: { admin_id: '=' }
      }
      lists(params).then(res => {
        this.timeline = res.data.data
      })
    }
  }
}
</script>
