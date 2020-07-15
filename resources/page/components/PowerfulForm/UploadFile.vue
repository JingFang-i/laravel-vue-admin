<template>
  <el-upload
    class="upload-demo"
    drag
    :action="uploadUrl"
    :multiple="multiple"
    :files="files"
    name="file"
    :headers="headers"
    :data="params"
    :limit="limit"
    :accept="accept"
    :on-remove="onRemove"
    :on-success="onSuccess"
    :on-exceed="outOfLimit"
    :before-upload="beforeUpload"
    :file-list="fileLists"
  >
    <i class="el-icon-upload"></i>
    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
    <div class="el-upload__tip" slot="tip">
      {{ tips }}
    </div>
  </el-upload>
</template>

<script>
import { Message } from 'element-ui'
import { getImgUrl } from '../../utils/helper'
import { getToken } from '../../utils/auth'

export default {
  name: 'UploadFile',
  model: {
    prop: 'files',
    event: 'success'
  },
  props: {
    limit: {
      type: Number,
      default: 20
    },
    files: {
      type: [String, Array],
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
    },
    // 上传的文件类型
    accept: {
      type: String,
      accept: 'application/*'
    },
    tips: {
      type: String,
      default: '只能上传jpg/png文件，且不超过10M'
    }
  },
  data() {
    return {
      params: { album_id: 0 },
      headers: { Authorization: 'Bearer ' + getToken() },
      imageSize: 104857600, // 100M
      uploadUrl: process.env.VUE_APP_BASE_API + '/upload',
      fileLists: [],
      uploadFiles: []
    }
  },
  watch: {
    uploadFiles: function(value) {
      if (this.limit === 1) {
        this.$emit('success', value[0])
      } else {
        this.$emit('success', value)
      }
    }
  },
  mounted() {
    this.parsedFiles()
  },
  methods: {
    parsedFiles() {
      let files = this.files
      let fileLists = []
      if (typeof files === 'string') {
        files = files.split(',')
      }
      files.forEach(path => {
        if (path !== '') {
          fileLists.push({
            name: path,
            url: getImgUrl(path)
          })
        }
      })
      this.fileLists = fileLists
    },
    beforeUpload(file) {
      if (file.size > this.imageSize) {
        Message.error('图片大小超过限制！')
        return false
      }
      return true
    },
    outOfLimit() {
      Message.error('图片数量超出限制!')
    },
    onRemove(file, fileList) {
      const files = []
      fileList.forEach(item => {
        if ('response' in item && item.response.code === 200) {
          files.push(item.response.data.filename)
        } else {
          files.push(item.name)
        }
      })
      this.uploadFiles = files
    },
    onSuccess(response) {
      if (response.code === 200) {
        this.uploadFiles.push(response.data.filename)
      } else {
        this.$message.error(response.msg)
      }
    },
    onError(err) {
      console.log(err)
    }
  }
}
</script>
