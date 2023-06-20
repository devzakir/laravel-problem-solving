<template>
    <div class="payment__info">
		<h6 class="mb-4">カード登録</h6>
		<div class="stripe-logos mb-4">
			<img src="/asset/stripe_logo/american_express.png" alt="">
			<img src="/asset/stripe_logo/discover.png" alt="">
			<img src="/asset/stripe_logo/master_card.png" alt="">
			<img src="/asset/stripe_logo/visa.png" alt="">
			<img src="/asset/stripe_logo/jcb.png" alt="">
		</div>
        <div id="card-element">
		<!-- A Stripe Element will be inserted here. -->
		</div>

		<!-- Used to display form errors. -->
		<div id="card-errors" class="has-text-danger" role="alert"></div>
	</div>
</template>
<script>
import Form from 'vform'
export default {
	name: 'CardInfo',
	
	data() {
		return  {
			stripe: undefined,
            cardElement: undefined
		}
	},

	created() {
	},

	mounted() {
		this.includeStripe('js.stripe.com/v3/', function(){
            this.initStripe();
        }.bind(this) );
	},

	methods: {
		includeStripe( URL, callback ){
            let documentTag = document, tag = 'script',
                object = documentTag.createElement(tag),
                scriptTag = documentTag.getElementsByTagName(tag)[0];
            object.src = '//' + URL;
            if (callback) { object.addEventListener('load', function (e) { callback(null, e); }, false); }
            scriptTag.parentNode.insertBefore(object, scriptTag);
		},
		
		initStripe() {
            this.stripe = Stripe('pk_test_51H15dyIJ66ekJQmdSdaUUe6NS9ofG3DW22xn8URRnGbGRxWMjUkFzGMH9c5CPgnXDJT28cAZ0p6bZYia9fqoGeEt00IMD0YLk7');
            const elements = this.stripe.elements({
                locale: 'ja',
            });
            this.cardElement = elements.create('card', {
                hidePostalCode: true,
            });

            this.cardElement.mount('#card-element');

            this.cardElement.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        }
	},

	components: {

	}
}
</script>
<style lang="scss" scoped>
.payment__info {
	h6 {
		font-size: 18px;
		font-weight: 600;
	}
}

.stripe-logos {
	display: flex;

	img {
		margin-right: 6px;
		width: 40px;
	}
}
</style>