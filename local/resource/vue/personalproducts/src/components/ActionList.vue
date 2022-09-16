<template>
  <div class="navigation-action">
    <div class="js-more-products ibutton" v-if="this.showNextLoad" @click="getNextPage">Загрузить ещё</div>
  </div>
</template>

<script>
export default {
  name: "ActionList",
  data() {
    return {
      showDefaultBtn: true,
    }
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
    },
    showNextLoad(){
      return this.$store.state.showNextLoad;
    },
  },
  methods:{
    getNextPage(event){
      if (event) {
        event.preventDefault()
      }
      if (process.env.NODE_ENV === 'production') {
        /*eslint no-undef: "off"*/
        waitCheck();
      }
      this.$http.get(this.domain + '/include/ajax_manage.php?PAGEN_1=' + Number(this.page)).then(response => {
        if(response.data.items.length > 0){
          let oldProduct = this.products;
          let newProductList = oldProduct.concat(response.data.items);
          if(oldProduct.length === newProductList.length){
            this.$store.dispatch('setShowingNextBtn', false);
          }
          else{
            this.$store.dispatch('setShowingNextBtn', true);
          }
          // get body data
          this.$store.dispatch('setProducts', newProductList)
        }
        this.$store.dispatch('setPage', response.data.page)

        if (process.env.NODE_ENV === 'production') {
          /*eslint no-undef: "off"*/
          waitCheck('close');
        }
      }, response => {
        // error callback
        console.log(response)
      });
    }
  }
};
</script>