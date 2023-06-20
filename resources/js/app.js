import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
import { MdCheckbox, MdRadio, MdTable } from 'vue-material/dist/components'
import 'vue-material/dist/vue-material.min.css'
import 'vue-material/dist/theme/default.css'
import './filter/index'
import Trend from "vuetrend"
import axios from 'axios'
import VueSwal from 'vue-swal'
import Spinner from 'vue-simple-spinner'
import {ServerTable, ClientTable, Event} from 'vue-tables-2';
import SweetModal from 'sweet-modal-vue/src/plugin.js'
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css'
import { VueHammer } from 'vue2-hammer'
import ToggleButton from 'vue-js-toggle-button'
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import Paginate from 'vuejs-paginate'
import VueDraggable from 'vue-draggable'
import PayjpCheckout from 'vue-payjp-checkout'
import Modal from "@burhanahmeed/vue-modal-2";
import firebase from 'firebase/app'
import 'firebase/firestore'
import 'firebase/auth'

import '~/plugins'
import '~/components'

//AWS setting
import AWS from 'aws-sdk';

export const aws_access_key_id = 'AKIAI7YE6URKTDVYDE4Q';
export const aws_secret_access_key = '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';
export const bucket_name = 'napll';
export const region= 'ap-northeast-1';
AWS.config.update({
  accessKeyId: aws_access_key_id,
  secretAccessKey: aws_secret_access_key,
  region: region
})
export const s3 = new AWS.S3({
  apiVersion: '2006-03-01',
  params: {
    Bucket: bucket_name
  }
})

window.axios = axios;

Vue.config.productionTip = false
Vue.use(MdCheckbox)
Vue.use(MdRadio)
Vue.use(MdTable)
Vue.use(PayjpCheckout)
Vue.use(ClientTable);
Vue.use(Trend)
Vue.use(VueSwal)
Vue.use(Modal);
Vue.use(ToggleButton)
Vue.use(SweetModal)
Vue.component('paginate', Paginate)
Vue.use(Autocomplete)
Vue.use(VueGoodTablePlugin);
Vue.use(Spinner)
Vue.use(VueHammer)
Vue.use(VueDraggable)

var firebaseConfig = {
  apiKey: "AIzaSyCvBQXGKr7o4nSU31UsbiUEhTLf5Oak0to",
  authDomain: "harumoa-mine.firebaseapp.com",
  projectId: "harumoa-mine",
  storageBucket: "harumoa-mine.appspot.com",
  messagingSenderId: "855337898004",
  appId: "1:855337898004:web:308a45b0854f2d37d39701",
  measurementId: "G-CY6V9HG7CS"
};
firebase.initializeApp(firebaseConfig);
window.db = firebase.firestore();
window.auth = firebase.auth();

new Vue({
  i18n,
  store,
  router,
  ...App
})
