<template>
  <div id="app">
    <section class="products-list">
      <search-form></search-form>
      <products-list></products-list>
      <action-list></action-list>
    </section>
  </div>
</template>

<script>
import SearchForm from './components/SearchForm.vue'
import ProductsList from './components/ProductsList.vue'
import ActionList from './components/ActionList.vue'

export default {
  name: 'App',
  components: {
    SearchForm,
    ProductsList,
    ActionList
  },
  computed:{
    page(){
      return this.$store.state.page
    },
    products(){
      return this.$store.state.products
    },
    domain(){
      return this.$store.state.domain
    }
  },
  methods:{
    async loadDevInit(){
      if (process.env.NODE_ENV === 'production') {
        /*eslint no-undef: "off"*/
        waitCheck()
      }
      await this.$http.get(this.$store.state.domain + '/include/ajax_manage.php?PAGEN_1=1' ).then(response => {
        // get body data
        this.$store.dispatch('setProducts', response.data.items)
        this.$store.dispatch('setPage', response.data.page)

        if(response.data.items.length < 10){
          this.$store.dispatch('setShowingNextBtn', false)
        }
        if (process.env.NODE_ENV === 'production') {
          /*eslint no-undef: "off"*/
          waitCheck('close')
        }
      }, response => {
        // error callback
        console.log(response)
      })
    }
  },
  beforeMount() {
    this.loadDevInit()
  }
}
</script>
