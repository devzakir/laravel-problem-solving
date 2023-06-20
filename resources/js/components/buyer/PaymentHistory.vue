<template>
    <div class="payment__history">
        <div class="inner">
            <table v-if="paymentHistories.length > 0">
                <tr>
                    <th>日付</th>
                    <th>動画タイトル</th>
                    <th>支払い形式</th>
                    <th>金額</th>
                </tr>
                <tr v-for="(item, index) in paymentHistories" :key="index">
                    <td>{{ item.paid_at | dateFormat }}</td>
                    <td>
                    <a>{{ item.movie.title | truncate(30) }}</a>
                    </td>
                    <td>{{ item.type | paymentMethod }}</td>
                    <td>
                        {{ item.price | moneyFormat }}￥
                    </td>
                </tr>
            </table>

            <div class="alert alert-secondary" role="alert" v-if="paymentHistories.length == 0">
				購入履歴がありません。
			</div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'PaymentHistory',

    methods: {
        async getPaymentHistories() {
            try {
                const { data } = await axios.get('/api/buyer/payment_history')
                
                this.paymentHistories = data.payment_history
            } catch(error) {
            }
        }
    },

    mounted() {
        this.getPaymentHistories()
    },

    data() {
        return {
            paymentHistories: []
        }
    }
}
</script>