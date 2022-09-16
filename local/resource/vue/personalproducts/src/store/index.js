import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
let state;

/*eslint no-undef: "off"*/
state = {
  products: [],
  manage: true,
  domain: 'https://sodamoda.ru',
  site_id: 's1',
  page: 1,
  showNextLoad: true,
}
export default new Vuex.Store({
  state: state,
  mutations: {
    set(state, {type, items}) {
      state[type] = items
    }
  },
  actions: {
    setProducts({commit}, items) {
      commit('set', {type: 'products', items: items});
    },
    setPage({commit}, items) {
      commit('set', {type: 'page', items: items});
    },
    setShowingNextBtn({commit}, items) {
      commit('set', {type: 'showNextLoad', items: items});
    },
  },
})
