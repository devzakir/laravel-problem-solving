<template>
  <div class="main__container">
    <div class="top_info">
      <span class="current__time">{{ currentDateTime }}現在</span>
      <span class="company_name">{{ $store.getters['recipienter_auth/user'].type == 1 ? $store.getters['recipienter_auth/user'].name : $store.getters['recipienter_auth/user'].parent.name }}</span>
      <span class="name">{{ $store.getters['recipienter_auth/user'].type == 1 ? 'マスター管理者' : $store.getters['recipienter_auth/user'].tanto_name }}様</span>
    </div>

    <div class="main__part">
      <div class="left_part">
        <!-- お知らせ -->
        <div class="news_part">
          <h2>お知らせ</h2>
          <div class="part_inner" v-if="news.length > 0">
            <div class="news__item" v-for="(item, index) in news" :key="index">
              <span class="date">{{ item.created_at | dateFormat }}</span>
              <span class="category">{{ item.type }}</span>
              <span class="type" @click="toOrderDetail(item.hash)">{{ item.hash }}</span>
              <span class="target">{{ item.from }}</span>
            </div>
          </div>
          <div class="part_inner" v-else>
            <p class="no_result">お知らせはありません</p>
          </div>
        </div>

        <div class="quick_part">
          <h2>クイックアクセス</h2>
          <div class="part_inner">
            <router-link :to="{ name: 'recipient.order1' }" class="quick_access">
              <img src="/asset/icons/recepient.svg" />
              <span>受注管理</span>
            </router-link>
            <router-link :to="{ name: 'recipient.shipping' }" class="quick_access">
              <img src="/asset/icons/output.svg" />
              <span>出荷管理</span>
            </router-link>
            <router-link :to="{ name: 'recipient.product' }" class="quick_access">
              <img src="/asset/icons/product.svg" />
              <span>商品管理</span>
            </router-link>
            <router-link :to="{ name: 'recipient.customer' }" class="quick_access">
              <img src="/asset/icons/trasaction.svg" />
              <span>取引先管理</span>
            </router-link>
          </div>
        </div>
      </div>

      <div class="right_part">
        <div class="news_part">
          <h2>本日の受注一覧</h2>
          <div class="part_inner" v-if="orders.length > 0">
            <div class="news__item" @click="toDetailOrder(item.id)" v-for="(item, index) in orders" :key="index">
              <span class="date">{{ item.ordered_at | dateFormat }}</span>
              <span class="category">新着受注</span>
              <span class="type">{{ item.hash }}</span>
              <span class="target">{{ item.user_name }}</span>
            </div>
          </div>
          <div class="part_inner" v-else>
            <p class="no_result">新着受注はありません</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import moment from 'moment'
export default {
  layout: 'recipient',
  middleware: 'recipient',
  data() {
    return {
      currentDateTime: null,
      news: [],
      orders: [],
      all_orders: []
    }
  },
  mounted() {
    this.currentDateTime = moment().format('YYYY年MM月DD日 HH:mm')
    this.setTime()
    this.init()
  },
  methods: {
    setTime() {
      setInterval(() => {
        this.currentDateTime = moment().format('YYYY年MM月DD日 HH:mm')
      }, 60000)
    },
    async toOrderDetail(hash) {
      try {
        const { data } = await axios.post('/recipienter/get_order_id', {
          hash: hash
        })
        this.$router.push({ name: 'recipient.order1.detail', query: { id: data.id } })
      } catch (error) {
      }
    },
    async init() {
      try {
        const { data } = await axios.post('/recipienter/get_dashboard_data')
        this.news = data.news
        this.orders = data.orders
      } catch (error) {
      }
    },
    toDetailOrder(id) {
      this.$router.push({ name: 'recipient.order1.detail', query: { id: id } })
    }
  },
}
</script>
<style lang="scss" scoped>
.main__container {
  flex: 1;
  padding: 20px 30px;

  .top_info {
    display: flex;
    align-items: center;
    margin-bottom: 20px;

    .current__time {
      font-size: 16px;
      margin-right: 20px;
      color: var(--text-color);
    }

    .company_name {
      font-size: 16px;
      margin-right: 40px;
      color: var(--text-color);
    }

    .name {
      font-size: 16px;
      color: var(--text-color);
    }
  }

  .main__part {
    display: flex;

    .left_part {
      flex: 1;
      margin-right: 30px;

      .quick_part {
        width: 100%;
        margin-top: 30px;

        h2 {
          font-size: 18px;
          font-weight: bold;
          color: var(--text-color);
          margin-bottom: 12px;
        }

        .part_inner {
          width: 100%;
          border: 1px solid var(--border-color);
          padding: 35px 45px;
          border-radius: 5px;
          display: flex;

          .quick_access {
            width: 120px;
            border-radius: 5px;
            background-color: var(--accent-color);
            padding-top: 17px;
            position: relative;
            color: #fff;
            margin-right: 20px;
            height: 130px;
            overflow: hidden;

            &:hover {
              color: #fff;
            }

            img {
              width: 65px;
              height: 65px;
              margin-left: auto;
              margin-right: auto;
              display: block;
            }

            span {
              text-align: center;
              background-color: var(--main-color2);
              color: #fff;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 14px;
              position: absolute;
              bottom: 0;
              left: 0;
              width: 100%;
              height: 30px;
            }
          }
        }
      }
    }

    .news_part {
      width: 100%;

      h2 {
        font-size: 18px;
        font-weight: bold;
        color: var(--text-color);
        margin-bottom: 12px;
      }

      .part_inner {
        width: 100%;
        border: 1px solid var(--border-color);
        padding: 15px 25px;
        border-radius: 5px;

        .news__item {
          width: 100%;
          display: flex;
          border-bottom: 1px solid var(--border-color);
          height: 30px;
          align-items: center;
          font-size: 14px;
          color: var(--text-color);
          margin-bottom: 0;

          .date {
            margin-right: 15px;
          }

          .category {
            margin-right: 25px;
          }

          .type {
            margin-right: 15px;
            cursor: pointer;
            color: var(--accent-color);
            text-decoration: underline;
          }
        }
      }
    }

    .right_part {
      flex: 1;
    }
  }
}
</style>