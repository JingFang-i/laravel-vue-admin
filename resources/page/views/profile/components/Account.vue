<template>
  <el-form ref="editForm">
    <el-form-item label="">
      <el-upload
        class="avatar-uploader"
        :action="uploadUrl"
        :headers="headers"
        :limit="limit"
        :show-file-list="false"
        :on-success="handleAvatarSuccess"
        :before-upload="beforeAvatarUpload"
      >
        <pan-thumb :image="getImgUrl(form.avatar)" />
        <!--        <el-image v-if="form.avatar" :src="form.avatar)" alt="头像" fit="cover" class="avatar"></el-image>-->
        <!--        <i v-else class="el-icon-plus avatar-uploader-icon"></i>-->
      </el-upload>
    </el-form-item>
    <el-form-item label="姓名">
      <el-input v-model.trim="form.name" />
    </el-form-item>
    <el-form-item label="密码">
      <el-input v-model.trim="form.password" type="password" />
    </el-form-item>
    <el-form-item label="确认密码">
      <el-input v-model.trim="form.password_confirmation" type="password" />
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="submit">更新</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
  import PanThumb from '@/components/PanThumb'
  import { getToken } from '@/utils/auth'
  import { getImgUrl } from '@/utils/helper'
  import { updateInfo } from '@/api/auth/user'
  export default {
    components: {
      PanThumb
    },
    props: {
      user: {
        type: Object,
        default: () => {
          return {
            name: '',
            avatar: ''
          }
        }
      }
    },
    data() {
      return {
        form: {
          name: '',
          avatar: '',
          password: '',
          password_confirmation: ''
        },
        limit: 1,
        headers: { Authorization: 'Bearer ' + getToken() },
        uploadUrl: process.env.VUE_APP_BASE_API + '/upload'
      }
    },
    mounted() {
      this.form = Object.assign(this.form, this.user)
    },
    methods: {
      getImgUrl,
      handleAvatarSuccess(res, file) {
        this.form.avatar = res.data.filename
      },
      beforeAvatarUpload(file) {
        const isImg = file.type.indexOf('image') !== -1
        const isLt2M = file.size / 1024 / 1024 < 2
        if (!isImg) {
          this.$message.error('上传头像图片只能是图片格式!')
          return false
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!')
          return false
        }
        return true
      },
      submit() {
        updateInfo(this.form).then(res => {
          this.$store.commit('SET_AVATAR', this.form.avatar)
          this.$store.commit('SET_NAME', this.form.name)
          this.form.password = ''
          this.form.password_confirmation = ''
          this.$message({
            message: '更新成功',
            type: 'success',
            duration: 3000
          })
        })
      }
    }
  }
</script>
<style lang="scss" scoped>
  .avatar {
    width: 100px;
    height: 100px;
    margin-left: 50px;
    border-radius: 50px;
  }
  .avatar-uploader-icon {
    margin-left: 50px;
    padding: 50px;
    border: 1px solid #ddd;
    border-radius: 10px;
  }
</style>
