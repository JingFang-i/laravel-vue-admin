<template>
  <div class="wrap">
    <div>
      <el-button size="small" type="primary" @click="upload"
        >点击上传</el-button
      >
      <div slot="tip" class="el-upload__tip">
        {{ tips }}
      </div>
      <div class="image-preview-list">
        <div
          v-for="(item, index) in previewImgs"
          :key="index"
          class="image-preview-item"
        >
          <img :src="item" :alt="'上传图片_' + (index + 1)" :title="item" />
          <span><i class="el-icon-delete" @click="removeImg(index)"></i></span>
        </div>
      </div>
    </div>

    <el-dialog
      title="上传"
      :visible="centerDialogVisible"
      width="50%"
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
            :plain="change !== operate.definition"
            size="mini"
            @click="switchOver(operate.definition)"
            >{{ operate.text }}</el-button
          >
        </div>
        <div>
          <el-select
            v-model="albumId"
            placeholder="请选择相册"
            class="photoSelect"
            value=""
          >
            <el-option label="全部" value="0" />
            <el-option
              v-for="(i, k) in albums"
              :key="k"
              :label="i.name"
              :value="i.id"
            />
          </el-select>
        </div>
      </div>
      <div v-if="change === 'local'" class="content photos">
        <el-upload
          class="upload-demo"
          drag
          :action="uploadUrl"
          :multiple="multiple"
          name="file"
          :files="files"
          :headers="headers"
          :data="params"
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
              <i class="el-icon-upload"></i>
            </div>
            <el-button size="small" type="primary">点击选择文件</el-button>
            <div slot="tip" class="el-upload__tip">
              或将文件拖到这里，本次最多可选20个
            </div>
          </div>
        </el-upload>
      </div>
      <div v-else-if="change === 'photos'" class="content photos">
        <div class="list">
          <empty v-if="attachments.length === 0" />
          <el-row :gutter="5" type="flex" style="width: 100%">
            <el-checkbox-group v-model="checkedImageIndex" style="width: 100%">
              <el-col
                v-for="(item, key) in attachments"
                :key="key"
                :span="6"
                style="margin-top: 5px"
              >
                <el-card :body-style="{ padding: '0px' }">
                  <el-image :src="getImgUrl(item.path)" fit="contain" lazy>
                    <div slot="error" class="image-slot">
                      <i class="el-icon-picture-outline"></i>
                    </div>
                  </el-image>
                  <div style="text-align:left;padding-left: 5px;">
                    <el-checkbox
                      :label="item.id"
                      @change="checkImage"
                      :title="item.name"
                      >{{
                        item.name.length > 10
                          ? item.name.substr(0, 10) + '...'
                          : item.name
                      }}</el-checkbox
                    >
                  </div>
                </el-card>
              </el-col>
            </el-checkbox-group>
          </el-row>
        </div>
        <el-row style="width: 100%">
          <el-col :span="24">
            <div class="pagination">
              <el-pagination
                :current-page.sync="currentPage"
                :page-size="pageSize"
                layout="prev, pager, next, jumper"
                :total="pageTotal"
                @size-change="handleSizeChange"
                @current-change="photoslist"
                style="text-align: center"
              /></div
          ></el-col>
        </el-row>
      </div>
      <div v-else class="content photos">
        <div class="search">
          <el-input v-model="keywords" placeholder="请输入图片名字" />
          <el-button
            type="primary"
            style="margin:0 10px 0 0;border-radius:5px"
            :plain="true"
            size="mini"
            @click="photoslist()"
            >搜索</el-button
          >
        </div>
        <div class="list">
          <empty v-if="attachments.length === 0" />
          <el-row :gutter="5" type="flex" style="width: 100%">
            <el-checkbox-group v-model="checkedImageIndex" style="width: 100%">
              <el-col
                v-for="(item, key) in attachments"
                :key="key"
                :span="6"
                style="margin-top: 5px"
              >
                <el-card :body-style="{ padding: '0px' }">
                  <el-image :src="getImgUrl(item.path)" fit="contain" lazy>
                    <div slot="error" class="image-slot">
                      <i class="el-icon-picture-outline"></i>
                    </div>
                  </el-image>
                  <div style="text-align:left;padding-left: 5px;">
                    <el-checkbox :label="item.id" @change="checkImage">{{
                      item.name.length > 10
                        ? item.name.substr(0, 10) + '...'
                        : item.name
                    }}</el-checkbox>
                  </div>
                </el-card>
              </el-col>
            </el-checkbox-group>
          </el-row>
        </div>
        <div class="pagination">
          <el-pagination
            :current-page.sync="currentPage"
            :page-size="pageSize"
            layout="prev, pager, next, jumper"
            :total="pageTotal"
            @size-change="handleSizeChange"
            @current-change="photoslist"
            style="text-align: center"
          />
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
import Empty from '@/components/Empty'
export default {
  name: 'Upload',
  components: {
    Empty
  },
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
      default: 'image/*'
    },
    tips: {
      type: String,
      default: '只能上传jpg/png文件，且不超过10M'
    }
  },
  data() {
    return {
      change: 'local', // 上传类型
      albums: [], // 相册列表
      attachments: [], // 图片列表
      previewImgs: [], // 预览图片
      checkedImageIndex: [], // 选中的图片索引
      albumId: '0', // 相册id
      keywords: '', // 输入的图片名字
      pageSize: 10,
      pageTotal: 0,
      currentPage: 1,
      operatesButtons: [
        {
          name: 'local_upload',
          text: '本地上传',
          definition: 'local'
        },
        {
          name: 'attachments',
          text: '相册图片',
          definition: 'photos'
        },
        {
          name: 'search_attachments',
          text: '图片搜索',
          definition: 'search'
        }
      ],
      params: { album_id: 0 },
      headers: { Authorization: 'Bearer ' + getToken() },
      imageSize: 10485760, // 10M
      uploadUrl: process.env.VUE_APP_BASE_API + '/upload',
      filePathLists: [],
      centerDialogVisible: false
    }
  },
  watch: {
    albumId: function(value) {
      this.photoslist()
      this.params.album_id = value
    },
    files: function(value) {
      this.init()
    }
  },
  created() {
    this.init()
  },
  computed: {
    fileLists: function() {
      let files = this.files
      if (typeof files === 'string') {
        files = files !== '' ? files.split(',') : []
      }
      if (!files) {
        files = [];
      }
      let fileLists = []
      files.forEach(v => {
        fileLists.push({ name: v, url: this.getImgUrl(v) })
      })
      return fileLists
    }
  },
  methods: {
    getImgUrl,
    init() {
      let files = this.files
      if (typeof this.files === 'string') {
        files = files === '' ? [] : files.split(',')
      }
      if (!files) {
        files = [];
      }
      this.filePathLists = files
      this.previewImgs = files
        .map(url => (url ? getImgUrl(url) : ''))
        .filter(url => url)
    },
    handleSizeChange(pageSize) {
      this.pageSize = pageSize
    },
    // 点击上传弹出模态框
    upload() {
      this.centerDialogVisible = true
      this.getLists()
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
      this.filePathLists = files
    },
    onSuccess(response) {
      if (response.code === 200) {
        this.filePathLists.push(response.data.filename)
      } else {
        this.$message.error(response.msg)
      }
    },
    // 确定按钮
    submit() {
      if (this.filePathLists.length > this.limit) {
        Message.error('图片不能超过' + this.limit + '张')
        return false
      }
      if (this.multiple) {
        this.$emit('success', this.filePathLists)
      } else {
        this.$emit('success', this.filePathLists[0])
      }
      this.close()
    },
    // 关闭模态框
    close() {
      this.centerDialogVisible = false
      this.filePathLists = []
    },
    // 得到相册名字
    getLists() {
      const params = {
        is_select: 1
      }
      lists(params)
        .then(res => {
          this.albums = res.data
        })
        .catch(err => this.$message.error(err))
    },
    // 相册图片
    photoslist() {
      const params = {
        filter: {
          album_id: this.albumId,
          name: this.keywords,
          mime_type: 'image'
        },
        operate: { album_id: '=', name: 'like', mime_type: 'like' },
        page: this.currentPage,
        page_size: this.pageSize
      }
      accessoryLists(params)
        .then(res => {
          this.pageTotal = res.data.meta.pagination.total
          this.attachments = res.data.data
        })
        .catch(err => this.$message.error(err))
    },
    // 切换按钮
    switchOver(type) {
      this.change = type
      if (this.change === 'photos') {
        this.attachments = []
        this.photoslist()
      }
    },
    removeImg(key) {
      if (this.multiple) {
        this.filePathLists.splice(key, 1)
        this.$emit('success', this.filePathLists)
      } else {
        this.filePathLists = []
        this.previewImgs = []
        this.$emit('success', '')
      }
    },
    checkImage() {
      const checkedImages = []
      this.attachments.forEach(item => {
        if (this.checkedImageIndex.indexOf(item.id) !== -1) {
          checkedImages.push(item.path)
        }
      })
      this.filePathLists = checkedImages
    }
  }
}
</script>
<style lang="scss" scoped>
.wrap {
  .image-preview-list {
    width: 40%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: flex-start;
    .image-preview-item {
      position: relative;
      width: 100px;
      height: 100px;
      margin-right: 5px;
      margin-top: 5px;
      border-radius: 3px;
      &:hover {
        opacity: 0.8;
        filter: alpha(opacity=80);
      }
      span {
        position: absolute;
        display: inline-block;
        top: 0;
        left: 0;
        height: 100px;
        line-height: 100px;
        font-size: 30px;
        width: 100%;
        text-align: center;
        opacity: 0;
        filter: alpha(opacity=0);
        &:hover {
          opacity: 1;
          filter: alpha(opacity=100);
          cursor: pointer;
        }
        i {
          color: red;
        }
      }
      img {
        width: 100px;
        height: 100px;
        object-fit: contain;
      }
    }
  }
  .content {
    width: 100%;
    box-sizing: border-box;
    padding: 20px;
    margin-top: 28px;
    border-radius: 10px;
    ::v-deep .el-upload-list--picture-card img {
      object-fit: contain;
    }
    ::v-deep .el-upload--picture-card {
      width: 267px;
      height: 147px;
      line-height: 0 !important;
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
    ::v-deep.photoSelect {
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
  .photos {
    border: 1px dotted rgba(199, 199, 199, 1);
    .search {
      display: flex;
      justify-content: space-between;
      padding-bottom: 20px;
      margin-bottom: 20px;
      border-bottom: 1px solid #e9e9e9;
      ::v-deep.el-input {
        width: 342px;
      }
    }
    .list {
      display: flex;
      flex-wrap: wrap;
      overflow: auto;
      height: 360px;
      ::v-deep.el-image {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        img {
          height: 130px;
          object-fit: contain;
        }
      }
    }
  }
  .el-upload__tip {
    margin-top: 12px;
  }
}
</style>
