<template>
  <div class="wrap">
    <div>
      <el-button size="small" type="primary" @click="upload">点击上传</el-button>
      <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过10M</div>
    </div>

    <el-dialog
      title="上传"
      :visible="centerDialogVisible"
      width="30%"
      :modal="false"
      @close="close"
    >
      <div class="bottons-group">
        <div>
          <el-button
            v-for="(operate, key) in operatesButtons"
            :key="key"
            type="primary"
            style="margin:0 10px 0 0;border-radius:5px"
            :plain="change!==operate.definition"
            size="mini"
            @click="switchOver(operate.definition)"
          >{{ operate.text }}</el-button>
        </div>
        <div>
          <el-select v-model="pictrueId" placeholder="请选择相册" class="photoSelect">
            <el-option label="全部" value="0" />
            <el-option v-for="(i, k) in selectList" :key="k" :label="i.name" :value="i.id" />
          </el-select>
        </div>
      </div>
      <div v-if="change=='local'" class="content photoes">
        <el-upload
          class="upload-demo"
          drag
          :action="uploadUrl"
          :multiple="multiple"
          name="attachment"
          :headers="headers"
          :limit="limit"
          :accept="accept"
          :on-remove="onRemove"
          :on-success="onSuccess"
          :on-exceed="outOfLimit"
          :before-upload="beforeUpload"
          :file-list="fileLists"
          list-type="picture-card"
        >
          <div>
            <div>
              <i class="el-icon-upload" />
            </div>
            <el-button size="small" type="primary">点击选择文件</el-button>
            <div slot="tip" class="el-upload__tip">或将文件拖到这里，本次最多可选20个</div>
          </div>
        </el-upload>
      </div>
      <div v-else-if="change=='photoes'" class="content photoes">
        <div class="list">
          <span v-if="selectList1.length === 0">暂无内容!</span>
          <el-card v-for="(item, key) in selectList1" :key="key" :body-style="{ padding: '0px' }">
            <img :src="getImgUrl(item.path)" class="image">
            <div style="text-align:center">
              <span :title="item.name">{{ item.name.length > 15 ? item.name.substr(0, 15) + '...' : item.name }}</span>
            </div>
          </el-card>
        </div>
      </div>
      <div v-else class="content photoes">
        <div class="search">
          <el-input v-model="state2" placeholder="请输入图片名字" />
          <el-button
            type="primary"
            style="margin:0 10px 0 0;border-radius:5px"
            :plain="true"
            size="mini"
            @click="photoeslist(null,state2)"
          >搜索</el-button>
        </div>
        <div class="list">
          <span v-if="selectList1.length === 0">暂无内容!</span>
          <el-card v-for="(item, key) in selectList1" :key="key" :body-style="{ padding: '0px' }">
            <img :src="getImgUrl(item.path)" class="image">
            <div style="text-align:center">
              <span :title="item.name">{{ item.name.length > 15 ? item.name.substr(0, 15) + '...' : item.name }}</span>
            </div>
          </el-card>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="submit">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getToken } from '@/utils/auth'
import { getImgUrl } from '@/utils/helper'
import { Message } from 'element-ui'
import { lists } from '@/api/system/pictures'
import { lists as accessoryLists } from '@/api/system/accessory'
export default {
  name: 'Upload',
  props: {
    limit: {
      type: Number,
      default: 20
    },
    files: {
      type: String,
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
    },
    // 上传的文件类型
    accept: {
      type: String,
      default: 'image/*'
    }
  },
  data() {
    return {
      change: 'local', // 上传类型
      selectList: [], // 相册总名字
      selectList1: [], // 相册总图片
      selectList2: [], // 搜索总图片
      pictrueId: '0', // 相册名字
      state2: '', // 输入的图片名字
      operatesButtons: [
        {
          name: 'selects',
          text: '本地上传',
          definition: 'local'
        },
        {
          name: 'failed',
          text: '相册图片',
          definition: 'photoes'
        },
        {
          name: 'delete',
          text: '图片搜索',
          definition: 'search'
        }
      ],
      headers: { Authorization: 'Bearer ' + getToken() },
      imageSize: 10485760, // 10M
      uploadUrl: process.env.VUE_APP_BASE_URL + '/upload',
      fileLists: [],
      filePathLists: [],
      centerDialogVisible: false
    }
  },
  watch: {
    pictrueId: function(value) {
      this.photoeslist(value)
    }
  },
  mounted() {
    this.putLists(this.files)
  },
  methods: {
    getImgUrl,
    // 点击上传弹出模态框
    upload() {
      this.centerDialogVisible = true
      this.getLists()
    },
    putLists(files) {
      this.fileLists = []
      this.filePathLists = []
      if (!files) return false
      const arr = files.split(',')
      arr.forEach(v => {
        this.fileLists.push({ name: v, url: this.getImgUrl(v) })
        this.filePathLists.push(v)
      })
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
      if (file.response.code === 200) {
        this.filePathLists.filter(n => {
          return n !== file.response.data.filename
        })
      }
      // this.$emit("remove", file, fileList);
    },
    onSuccess(response) {
      if (response.code === 2000) {
        this.filePathLists.push(response.data.filename)
      }
    },
    // 确定按钮
    submit() {
      this.$emit('uploadImage', this.filePathLists)
      this.close()
    },
    // 关闭模态框
    close() {
      this.centerDialogVisible = false
      this.fileLists = []
      this.filePathLists = []
    },
    // 得到相册名字
    getLists() {
      const params = {
        is_select: 1
      }
      lists(params)
        .then(res => {
          this.selectList = res.data
        })
        .catch(err => this.$message.error(err))
    },
    // 相册图片
    photoeslist(id, state) {
      const params = {
        filter: id ? { album_id: id } : { name: state },
        operate: { album_id: '=', name: 'like' }
      }
      accessoryLists(params)
        .then(res => {
          this.selectList1 = res.data.data
        })
        .catch(err => this.$message.error(err))
    },
    // 切换按钮
    switchOver(type) {
      this.change = type
      if (this.change == 'photoes') {
        this.photoeslist(this.pictrueId)
      }
    }
  }
}
</script>
<style lang='scss' scoped>
.wrap {
  padding: 0 !important;
  /deep/.el-dialog__wrapper {
    .el-dialog {
      width: 933px !important;
      margin-left: 200px;
      .el-dialog__body {
        padding: 28px 44px;
      }
    }
  }
  .content {
    width: 100%;
    box-sizing: border-box;
    padding: 20px;
    margin-top: 28px;
    border-radius: 10px;
    /deep/.el-upload--picture-card {
      width: 267px;
      height: 147px;
      line-height: 0px !important;
      .el-upload-dragger {
        width: 100%;
        height: 100%;
        .el-icon-upload {
          line-height: 34px;
        }
      }
    }
  }

  .bottons-group {
    display: flex;
    justify-content: space-between;
    /deep/.photoSelect {
      .el-input {
        width: 150px;
        height: 28px;
        border-radius: 5px;
        input {
          height: 100%;
        }
        .el-input__icon {
          height: 17px;
          line-height: 20px;
        }
      }
    }
  }
  .photoes {
    border: 1px dotted rgba(199, 199, 199, 1);
    .search {
      display: flex;
      justify-content: space-between;
      padding-bottom: 20px;
      margin-bottom: 20px;
      border-bottom: 1px solid #e9e9e9;
      /deep/.el-input {
        width: 342px;
      }
    }
    .list {
      display: flex;
      flex-wrap: wrap;
      div {
        margin-right: 14px;
        margin-bottom: 13px;
        &:nth-child(5n) {
          margin-right: 0px;
        }
        img {
          width: 147px;
          height: 104px;
        }
      }
    }
  }
  .el-upload__tip {
    margin-top: 12px;
  }
}
</style>
