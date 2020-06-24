<template>
  <div class="pictures-list app-container">
    <el-button
      type="primary"
      size="medium"
      icon="el-icon-back"
      style="margin-bottom: 10px;"
      @click="$router.go(-1)"
    >返回</el-button>
    <div class="content">
      <div class="bottons-group">
        <el-button
          v-for="(operate, key) in operationsButtons"
          :key="key"
          type="primary"
          style="margin:0 10px 0 0;border-radius:5px"
          size="mini"
          @click="operate.handle()"
        >{{ operate.text }}</el-button>
      </div>
      <div class="pictures-group">
        <empty v-if="list.length === 0"></empty>
        <div v-for="(item, key) in list" class="pictures-item" :key="key" @click="checkClick(item)">
          <el-card :body-style="{ padding: '0px' }" class="list">
            <img :src="getImgUrl(item.path)" style="width:232px;height:158px" :alt="item.name" :title="item.name">
            <div class="card-font">
              <span
                style="inline-block;width:60px;height:20px;overflow: hidden;text-overflow: ellipsis"
              >{{ item.name }}</span>
              <span
                style="inline-block;width:90px;height:20px;overflow: hidden;"
              >{{ item.created_at }}</span>
            </div>
            <img v-if="item.whetherClick" src="@/assets/images/checked-icon.png" alt="勾选" class="checked">
          </el-card>
        </div>
        <div class="pagination">
          <el-pagination
            :current-page.sync="currentPage"
            :page-size="pageSize"
            layout="prev, pager, next, jumper"
            :total="pageTotal"
            @size-change="handleSizeChange"
            @current-change="getList"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { getImgUrl } from '@/utils/helper'
import {
  lists as accessoryLists,
  del as accessoryDelete
} from '@/api/system/accessory'
import Empty from '@/components/Empty'

export default {
  components: {
    Empty
  },
  data() {
    return {
      list: [], // 图片列表
      ids: [], // 选择的图片
      pageSize: 10,
      pageTotal: 0,
      currentPage: 1,
      operationsButtons: [
        {
          name: 'select_all',
          text: '全选',
          handle: () => {
            return this.checkAll()
          }
        },
        {
          name: 'cancel',
          text: '取消',
          handle: id => {
            return this.cancel(id)
          }
        },
        {
          name: 'delete',
          text: '删除',
          handle: id => {
            return this.delete(id)
          }
        }
      ]
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getImgUrl,
    handleSizeChange(pageSize) {
      this.pageSize = pageSize
    },
    getList() {
      const pictureId = this.$route.query.id
      let params
      if (pictureId === 'all') {
        params = {
          type: 'image'
        }
      } else {
        params = {
          filter: { album_id: pictureId },
          operate: { album_id: '=' },
          page_size: this.pageSize,
          page: this.currentPage
        }
      }
      accessoryLists(params)
        .then(res => {
          this.pageSize = res.data.meta.pagination.per_page
          this.pageTotal = res.data.meta.pagination.total
          this.currentPage = res.data.meta.pagination.current_page
          if (res.data.data.length > 0) {
            this.list = res.data.data.map(n => {
              n.whetherClick = false
              return n
            })
          }
        })
        .catch(err => {
          console.log(err)
        })
    },
    /**
     * @description 单击选择
     */
    checkClick(item) {
      item.whetherClick = !item.whetherClick
    },
    /**
     * 列表查询
     * @description 全选
     */
    checkAll() {
      this.list = this.list.map(n => {
        n.whetherClick = true
        return n
      })
    },
    /**
     * 列表查询
     * @description 取消
     */
    cancel() {
      this.list = this.list.map(n => {
        n.whetherClick = false
        return n
      })
    },
    /**
     * 列表查询
     * @description 删除
     */
    delete() {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.ids = this.list.filter(n => n.whetherClick === true).map(n => n.id)
        accessoryDelete({
          ids: this.ids
        }).then(res => {
          this.page = 1
          this.getList()
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        }).catch(err => {})
      }).catch(() => {})
    }
  }
}
</script>
<style lang="scss" scoped>
.pictures-list {
  box-sizing: border-box;
  .bottons-group {
    margin-bottom: 30px;
  }
  .pictures-group {
    display: flex;
    flex-wrap: wrap;
    overflow: auto;
    height: 70vh;
    .pictures-item {
      position: relative;
      width: 231px;
      height: 205px;
      margin-right: 38px;
      margin-bottom: 19px;
      .card-font {
        padding: 14px;
        display: flex;
        justify-content: space-between;
      }
      .checked {
        position: absolute;
        width: 30px;
        height: 30px;
        right: 0;
        bottom: 0;
        z-index: 999;
      }
    }
    .pagination {
      width: 100%;
      text-align: center;
    }
  }
}
.content {
  box-sizing: border-box;
  padding: 26px;
}
</style>
