(window.webpackJsonp=window.webpackJsonp||[]).push([[45],{297:function(e,t,n){var a=n(502);"string"==typeof a&&(a=[[e.i,a,""]]);var r={hmr:!0,transform:void 0,insertInto:void 0};n(6)(a,r);a.locals&&(e.exports=a.locals)},501:function(e,t,n){"use strict";n(297)},502:function(e,t,n){(e.exports=n(5)(!1)).push([e.i,".create__container[data-v-e5b23cfa] {\n  padding: 20px 30px;\n}\n.create__container .contact__info[data-v-e5b23cfa] {\n  width: -webkit-fit-content;\n  width: -moz-fit-content;\n  width: fit-content;\n  padding: 12px 14px;\n  border: 1px solid var(--border-color);\n  border-radius: 5px;\n  background-color: #fff;\n  margin-left: 50px;\n  margin-top: 20px;\n}\n.create__container .contact_heading[data-v-e5b23cfa] {\n  align-items: center;\n  margin-bottom: 14px;\n}\n.create__container .contact_heading .company_name[data-v-e5b23cfa] {\n  margin-left: 30px;\n  font-size: 12px;\n}\n.create__container .contact__form[data-v-e5b23cfa] {\n  padding-left: 20px;\n}\n.create__container .contact__form .form__row[data-v-e5b23cfa] {\n  margin-bottom: 4px;\n  display: flex;\n  align-items: center;\n}\n.create__container .contact__form .form__row label[data-v-e5b23cfa] {\n  font-size: 12px;\n  align-items: center;\n  width: 65px;\n  display: flex;\n}\n.create__container .contact__form .form__row label .must[data-v-e5b23cfa] {\n  width: 26px;\n  height: 12px;\n  font-size: 10px;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  background-color: #D34555;\n  color: #fff;\n  margin-left: 3px;\n}\n.create__container .contact__form .form__row p[data-v-e5b23cfa] {\n  font-size: 12px;\n  flex: 1;\n}\n.create__container .contact__form .form__row p input[data-v-e5b23cfa], .create__container .contact__form .form__row p select[data-v-e5b23cfa] {\n  width: 100%;\n  height: 30px;\n  border: 1px solid var(--border-color);\n  padding-left: 10px;\n  padding-right: 10px;\n  font-size: 12px;\n  width: 316px;\n}\n.create__container .message__part[data-v-e5b23cfa] {\n  width: 800px;\n  border: 1px solid var(--border-color);\n  margin-left: 50px;\n  margin-top: 24px;\n  padding: 25px 32px;\n  background-color: #fff;\n}\n.create__container .message__part .to_client[data-v-e5b23cfa] {\n  font-size: 12px;\n  margin-bottom: 20px;\n}\n.create__container .message__part .message_input[data-v-e5b23cfa] {\n  display: block;\n  width: 100%;\n  height: 200px;\n  border: 1px solid var(--border-color);\n  padding: 4px 8px;\n  font-size: 12px;\n  margin-bottom: 26px;\n  resize: none;\n}\n.create__container .message__part .condition_input[data-v-e5b23cfa] {\n  display: block;\n  width: 100%;\n  height: 240px;\n  border: 1px solid var(--border-color);\n  padding: 4px 8px;\n  font-size: 12px;\n  margin-bottom: 26px;\n  resize: none;\n}\n.create__container .actions[data-v-e5b23cfa] {\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  padding-top: 30px;\n}\n.create__container .actions button[data-v-e5b23cfa] {\n  width: 200px;\n  height: 35px;\n  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  font-size: 14px;\n}\n.create__container .actions button[data-v-e5b23cfa]:first-of-type {\n  margin-right: 50px;\n  background-color: #fff;\n  border: 1px solid var(--border-color);\n}\n.create__container .actions button[data-v-e5b23cfa]:last-of-type {\n  background-color: var(--accent-color);\n  color: #fff;\n  border: 0;\n}\n.textarea[data-v-e5b23cfa] {\n  position: relative;\n}\n.textarea textarea[data-v-e5b23cfa] {\n  background: transparent;\n  position: relative;\n}\n.textarea .placeholder[data-v-e5b23cfa] {\n  position: absolute;\n  left: 8px;\n  top: 4px;\n  font-size: 12px;\n  z-index: 0;\n  opacity: 0.7;\n}",""])},88:function(e,t,n){"use strict";n.r(t);var a=n(0),r=n.n(a);function o(e,t,n,a,r,o,i){try{var s=e[o](i),c=s.value}catch(e){return void n(e)}s.done?t(c):Promise.resolve(c).then(a,r)}function i(e){return function(){var t=this,n=arguments;return new Promise((function(a,r){var i=e.apply(t,n);function s(e){o(i,a,r,s,c,"next",e)}function c(e){o(i,a,r,s,c,"throw",e)}s(void 0)}))}}var s={layout:"recipient",data:function(){return{form:{from:null,title:null,message:null,address:null,client_id:null,order_id:null},order_form:null,step:1,pdf:null,toggle:!0}},mounted:function(){this.init()},methods:{init:function(){var e=this;return i(r.a.mark((function t(){var n,a;return r.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,axios.post("/recipienter/get_order_detail",{id:e.$route.query.id});case 2:n=t.sent,a=n.data,e.order_form=a.order_form,e.form.client_id=e.order_form.user_id,e.form.order_id=e.order_form.id;case 7:case"end":return t.stop()}}),t)})))()},back:function(){2==this.step?this.step=1:this.$router.back()},next:function(){this.form.from&&this.form.title&&this.form.message&&this.form.address?1==this.step?this.$refs.pdf_selector.click():2==this.step&&this.sendMessage():this.$swal("","必須情報を入力してください")},selectedPDF:function(e){var t=this;return i(r.a.mark((function n(){var a;return r.a.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:(a=e.target.files[0])&&(t.pdf=a,t.step=2);case 2:case"end":return n.stop()}}),n)})))()},sendMessage:function(){var e=this;return i(r.a.mark((function t(){var n,a,o;return r.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(e.pdf){t.next=3;break}return e.$swal("","請求書を選択して下さい"),t.abrupt("return");case 3:return(n=new FormData).append("from",e.form.from),n.append("title",e.form.title),n.append("message",e.form.message),n.append("address",e.form.address),n.append("client_id",e.form.client_id),n.append("order_id",e.form.order_id),n.append("pdf",e.pdf),t.prev=11,t.next=14,axios.post("/recipienter/send_invoice_message",n,{headers:{"Content-Type":"multipart/form-data"}});case 14:a=t.sent,o=a.data,e.$swal("","送信しました。"),o.flag&&e.$router.back(),t.next=22;break;case 20:t.prev=20,t.t0=t.catch(11);case 22:case"end":return t.stop()}}),t,null,[[11,20]])})))()},keyupChange:function(){0!==this.form.address.length?this.toggle=!1:this.toggle=!0}}},c=(n(501),n(3)),d=Object(c.a)(s,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"create__container"},[n("h2",{staticClass:"page__title"},[e._v("発注者に連絡")]),e._v(" "),n("div",{staticClass:"contact__info"},[n("div",{staticClass:"d-flex contact_heading"},[n("h2",{staticClass:"page__title"},[e._v("取引先情報")]),e._v(" "),n("span",{staticClass:"company_name"},[e._v(e._s(e.order_form?e.order_form.user.com_name:""))])]),e._v(" "),n("div",{staticClass:"contact__form"},[n("div",{staticClass:"form__row"},[n("label",[e._v("差出人名")]),e._v(" "),n("p",[n("select",{directives:[{name:"model",rawName:"v-model",value:e.form.from,expression:"form.from"}],attrs:{disabled:2==e.step},on:{change:function(t){var n=Array.prototype.filter.call(t.target.options,(function(e){return e.selected})).map((function(e){return"_value"in e?e._value:e.value}));e.$set(e.form,"from",t.target.multiple?n:n[0])}}},[n("option",{domProps:{value:e.$store.getters["recipienter_auth/user"].id}},[e._v(e._s(e.$store.getters["recipienter_auth/user"].name)+"　"+e._s(1==e.$store.getters["recipienter_auth/user"].type?"マスターアカウント":e.$store.getters["recipienter_auth/user"].tanto_name))])])])]),e._v(" "),n("div",{staticClass:"form__row"},[n("label",[e._v("宛先")]),e._v(" "),n("p",[e._v(e._s(e._f("''")(e.order_form.user.com_name))+"　　　"+e._s(e.order_form.user.email))])]),e._v(" "),n("div",{staticClass:"form__row"},[e._m(0),e._v(" "),n("p",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.form.title,expression:"form.title"}],attrs:{type:"text",placeholder:"【ご連絡】株式会社〇〇（注文番号：0000000-111）",disabled:2==e.step},domProps:{value:e.form.title},on:{input:function(t){t.target.composing||e.$set(e.form,"title",t.target.value)}}})])])])]),e._v(" "),n("div",{staticClass:"message__part"},[n("p",{staticClass:"to_client"},[e._v("\n        "+e._s(e.order_form.user.com_name)),n("br"),e._v("\n        "+e._s(e.order_form.user.name)+"様\n      ")]),e._v(" "),n("textarea",{directives:[{name:"model",rawName:"v-model",value:e.form.message,expression:"form.message"}],staticClass:"message_input",attrs:{placeholder:"メッセージ記入欄（在庫、納期等の連絡など）",disabled:2==e.step},domProps:{value:e.form.message},on:{input:function(t){t.target.composing||e.$set(e.form,"message",t.target.value)}}}),e._v(" "),n("div",{staticClass:"textarea"},[n("textarea",{directives:[{name:"model",rawName:"v-model",value:e.form.address,expression:"form.address"}],staticClass:"message_input",attrs:{disabled:2==e.step},domProps:{value:e.form.address},on:{keyup:e.keyupChange,input:function(t){t.target.composing||e.$set(e.form,"address",t.target.value)}}}),e._v(" "),e.toggle?n("p",{staticClass:"placeholder"},[e._v("\n    —————————————————————————"),n("br"),e._v("\n    株式会社〇〇"),n("br"),e._v("\n    〇〇 テスト"),n("br"),e._v("\n    info@conecle.com"),n("br"),e._v(" "),n("br"),e._v("\n    〒520-1111"),n("br"),e._v("\n    大阪府大阪市北区〇〇町1-1"),n("br"),e._v("\n    TEL:06-7777-0000"),n("br"),e._v("\n    —————————————————————————\n  ")]):e._e()])]),e._v(" "),n("div",{staticClass:"actions"},[n("button",{on:{click:e.back}},[e._v("戻る")]),e._v(" "),n("button",{on:{click:e.next}},[e._v(e._s(1==e.step?"PDFを選択して確認画面へ":"送信"))])]),e._v(" "),n("input",{ref:"pdf_selector",staticClass:"dis-none",attrs:{type:"file",accept:"application/pdf"},on:{change:e.selectedPDF}})])}),[function(){var e=this.$createElement,t=this._self._c||e;return t("label",[this._v("件名"),t("span",{staticClass:"must"},[this._v("必須")])])}],!1,null,"e5b23cfa",null);t.default=d.exports}}]);