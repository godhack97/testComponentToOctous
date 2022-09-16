var appViewStoreElement;
document.addEventListener('DOMContentLoaded', function(){
  appViewStoreElement = new Vue({
    el: '#app_view_store_element',
    data: {
      element: null,
      url: window.location.pathname,
      color: false,
      productID: false,
      loading: false,
      linkProduct: '#',
    },
    mounted: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const color = urlParams.get('color');
      const ID = urlParams.get('query');

      var view = document.getElementById('elem_store_view');
      if(urlParams.get('query')){
        this.productID = view.dataset.id;//ID;
        if(color){
          history.pushState({}, 'Просмотр остатков', window.location.pathname+'?query='+this.productID+'&color='+color);
        }
        this.getElement(this.productID);
      }
      else{
        if(view.dataset.id){
          this.productID = view.dataset.id;//ID;
          if(color){
            history.pushState({}, 'Просмотр остатков', window.location.pathname+'?color='+color);
          }
          this.getElement(this.productID);
        }
        else {
          view.classList.remove('first');
          var preloading = document.getElementById('preloading');
          preloading.style.display = 'none';
        }
      }
      this.$nextTick(function () {
        // Код, который будет запущен только после отображения всех представлений
        view.classList.add('display');
      })
    },
    methods: {
      setSelectColor(VALUE_XML_ID) {
        var view = document.getElementById('elem_store_view');
        if(view.dataset.id){
          history.pushState({}, 'Просмотр остатков', window.location.pathname+'?color='+VALUE_XML_ID);
        }
        else{
          history.pushState({}, 'Просмотр остатков', window.location.pathname+'?query='+this.productID+'&color='+VALUE_XML_ID);
        }
        this.element.select_color_sizes = VALUE_XML_ID;
        for (item in this.element.color_sizes) {
          if (this.element.color_sizes[item].VALUE_XML_ID == VALUE_XML_ID) {
            this.element.select_offer_id = this.element.color_sizes[item].SIZES[0].OFFER_ID;
          }
        }
        this.getLinkProduct(this.element);
        this.element.select_stores = this.element.stores[0].ID;
      },
      setSelectOfferAndStores(offer, store) {
        this.element.select_offer_id = offer;
        this.element.select_stores = store;
      },
      getElement(id) {
        waitCheck();
        this.loading = true;
        let _this = this;
        let query = {
          c: 'arlix:store.page',
          action: 'getelement',
          mode: 'class'
        };
        let data = {
          id: id,
          SITE_ID: BX.message('SITE_ID')
        };
        $.ajax({
          type: 'POST',
          cache: false,
          url: '/bitrix/services/main/ajax.php?' + $.param(query, true),
          data: data,
          success: function (data) {
            const urlParams = new URLSearchParams(window.location.search);
            const color = urlParams.get('color');
            _this.element = data.data;
            _this.productID = id;
            if(!color){
              _this.setSelectColor(_this.element.select_color_sizes);
            }
            else{
              _this.setSelectColor(color);
            }
            _this.getLinkProduct(_this.element);
            var preloading = document.getElementById('preloading');
            preloading.style.display = 'none';
            var view = document.getElementById('elem_store_view');
            view.classList.remove('first');
            waitCheck('close');
            _this.loading = false;
          }
        });
      },
      getSizeStore(store, index) {
        return store[index];
      },
      getLinkProduct(element){
        this.linkProduct = element.url+'?setColor='+element.select_color_sizes+'&offer='+element.select_offer_id;
      },
      formatNumber(num) {
        return number_format(num, 0, ',', ' ');
      }
    }
  });
});