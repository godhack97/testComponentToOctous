<template>
  <table class="basket-table">
    <thead>
    <tr class="head">
      <th class="image">Товар</th>
      <th class="desc">Название</th>
      <th class="summa">Артикул</th>
      <th class="control">Удалить</th>
    </tr>
    </thead>
    <tbody id="product_list">
      <tr class="item-row" v-for="product in products" :key="product.id">
        <td class="image">
          <a :href="productEditLink(product.id)" class="image hover" :style="getPreviewThumb(product.preview_photo)" target="_blank"></a>
        </td>
        <td class="desc">
          <div class="name">
            <a class="hover" :href="productEditLink(product.id)">{{ product.name }}</a>
          </div>
          <div class="props">
            <div class="prop">{{ product.colors }}</div>
            <div class="prop">{{ product.sizes }}</div>
          </div>
        </td>
        <td>{{ product.article }}</td>
        <td class="control">
          <a class="delete hover delete-product" data-entity="basket-item-delete" :data-product-id="product.id">
            <img :src="domain + '/local/templates/sodamoda/images/icons/times.svg'">
          </a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "ProductsList",
  computed: {
    isManage(){
      return this.$store.state.manage;
    },
    owner(){
      return this.isManage ? 'N' : 'Y';
    },
    products(){
      return this.$store.state.products;
    },
    domain(){
      return this.$store.state.domain;
    },
    showNextLoad(){
      return this.$store.state.showNextLoad;
    },
  },
  methods:{
    getPreviewThumb(url){
      if(url){
        return {backgroundImage: 'url(' + this.domain + url + ')'}
      }
      else{
        return {}
      }
    },
    productEditLink(id){
      return this.$store.state.domain + this.isManage ? '/personal/products/edit/'+id+'/' : '/manage/products/edit/'+id+'/';
    },
    deleteProduct(id){
      if(confirm('Вы уверены, что хотите удалить этот товар?')){
        this.$http.post(this.$store.state.domain + '/include/ajax_delete_product.php', {product_id: id, site_id: this.$store.state.site_id}).then(() => {
          // get body data
          if(result.status)
          {
            if (process.env.NODE_ENV === 'production') {
              /*eslint no-undef: "off"*/
              popupsClose()
              setTimeout(() =>{
                popupOpenMessage('Продукт успешно удалён!')
              }, 500)
            }
          }
          else
          {
            if(result.validation_messages)
              setTimeout(() =>{
                popupOpenMessage(result['validation_messages'])
              }, 500)
          }

          waitCheck('close');
        }, response => {
          // error callback
          console.log(response)
        });
      }
    }
  }
};
</script>