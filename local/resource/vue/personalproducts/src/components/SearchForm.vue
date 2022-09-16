<template>
  <div class="product-nav">
    <form method="get" class="search-form form" name="search-product-by-name-article">
      <div class="field">
        <input type="hidden" name="is_owner" :value="owner">
        <input class="field-style" type="text" placeholder=" " name="search_field" id="search_field" maxlength="" v-model="query">
        <label class="placeholder" for="search_field">Название или артикул</label>
      </div>
      <input name="send-search" class="send-search" @click="searchProduct" value="" type=submit>
    </form>
    <div class="add-product">
      <a :href="productAdd" class="black hover">Добавить товар</a>
    </div>
  </div>
</template>

<script>
export default {
  name: "SearchForm",
  data(){
    return {
      query: '',
    }
  },
  computed: {
    isManage(){
      return this.$store.state.manage;
    },
    owner(){
      return this.isManage ? 'N' : 'Y';
    },
    productAdd(){
      return this.isManage ? '/personal/products/add/' : '/manage/products/add/';
    },
    domain(){
      return this.$store.state.domain;
    }
  },
  methods:{
    searchProduct(event){
      if (event) {
        event.preventDefault()
      }
      if (process.env.NODE_ENV === 'production') {
        /*eslint no-undef: "off"*/
        waitCheck();
      }
      this.$http.post(this.domain + '/include/ajax_manage.php', {search_field: this.query, is_owner: this.owner}).then(response => {

        // get body data
        this.$store.dispatch('setProducts', response.data.items)
        this.$store.dispatch('setPage', response.data.page)
        if(response.data.items.length < 10){
          this.$store.dispatch('setShowingNextBtn', false)
        }
        else {
          this.$store.dispatch('setShowingNextBtn', true)
        }
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