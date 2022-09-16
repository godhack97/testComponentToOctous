var appSearchPage;
$(document).ready(function () {
  appSearchPage = new Vue({
    el: '#appSearchPage',
    data: {
      element: null,
    },
    watch: {},
    mounted: function () {
      this.$nextTick(function () {
        // Код, который будет запущен только после отображения всех представлений
        $('.order-page').addClass('display');
      })
    },
    methods: {
      addBasket() {
        _this=this;
        waitCheck();
        let data = {
          action: 'ADD2BASKET',
          site_id: BX.message('SITE_ID'),
          id: this.element.select_offer_id,
          prop: []
        };
        for (item in this.element.offer) {
          if (this.element.offer[item].ID == this.element.select_offer_id) {
            data.prop.push({
              NAME: "Размер",
              CODE: 'SIZES',
              VALUE: this.element.offer[item].SIZES.VALUE
            });
            data.prop.push({
              NAME: "Цвет",
              CODE: 'COLOR',
              VALUE: this.element.offer[item].COLOR.VALUE
            });
          }
        }
        data.prop.push({
          NAME: "Артикул",
          CODE: 'CML2_ARTICLE',
          VALUE: this.element.article
        });
        $.ajax({
          type: "POST",
          data: data,
          url: '/ajax/add.php',
          success: function (data) {
            _this.$nextTick(function () {
              for (item in BX.Sale.BasketComponent.items) {
                BX.Sale.BasketComponent.deleteBasketItem(item);
              }
              BX.Sale.BasketComponent.initializeBasketItems();
              BX.Sale.BasketComponent.sendRequest('refreshAjax', {fullRecalculation: 'Y'});
              //waitCheck('close');
            })
          }
        });
      },
      setSelectColor(VALUE_XML_ID) {
        this.element.select_color_sizes = VALUE_XML_ID;
        for (item in this.element.color_sizes) {
          console.log('color_sizes');
          console.log(this.element.color_sizes[item]);
          if (this.element.color_sizes[item].VALUE_XML_ID == VALUE_XML_ID) {
            this.element.select_offer_id = this.element.color_sizes[item].SIZES[0].OFFER_ID;
          }
        }
        this.element.select_stores = this.element.stores[0].ID;
      },
      setSelectOfferAndStores(offer, store) {
        this.element.select_offer_id = offer;
        this.element.select_stores = store;
      },
      getElement(id) {
        waitCheck();
        let _this = this;
        let query = {
          c: 'arlix:order.page',
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
            _this.element = data.data;
            $('.order-page').removeClass('first');
            waitCheck('close');
          }
        });
      },
      getSizeStore(store, index) {
        return store[index];
      },
      formatNumber(num) {
        return number_format(num, 0, ',', ' ');
      }
    }
  });
});