import Vue from 'vue'
import {HTTP} from './http-common'
import App from './App.vue'
import store from './store'

Vue.prototype.$http = HTTP;

Vue.config.productionTip = false

new Vue({
  store,
  http: {
    options: {
      emulateJSON: true,
      emulateHTTP: true
    }
  },
  render: h => h(App)
}).$mount('#app')
