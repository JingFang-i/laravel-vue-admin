<template>
  <div class="dashboard-container">
    <admin-dashboard />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import adminDashboard from './admin'
// import editorDashboard from '@/editor'

export default {
  name: 'Dashboard',
  components: { adminDashboard },
  data() {
    return {}
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  created() {
    // if (!this.roles.includes('admin')) {
    //   this.currentRole = 'editorDashboard'
    // }
  }
}
</script>
