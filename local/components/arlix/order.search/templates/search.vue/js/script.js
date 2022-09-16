var appSearch;
$(document).ready(function () {
  appSearch = new Vue({
    el: '#appSearch',
    data: {
      hide:true,
      list:[],
      query:""
    },
    watch: {
      query: function (val) {
        this.search();
      }
    },
    mounted: function () {
      this.$nextTick(function () {
        // Код, который будет запущен только после отображения всех представлений
        $('.order-page').addClass('display');
      })
    },
    methods: {
      sendId(id){
        appSearchPage.getElement(id);
        this.hide=true;
      },
      search() {
        let _this = this;
        if(_this.query.length > 3)
        {
          waitCheck();
          
          let query = {
            c: 'arlix:order.search',
            action: 'search',
            mode: 'class'
          };
          let data = {
            query: _this.query,
            SITE_ID: BX.message('SITE_ID')
          };
          $.ajax({
            type: 'POST',
            cache: false,
            url: '/bitrix/services/main/ajax.php?' + $.param(query, true),
            data: data,
            success: function (data) {
              _this.list=data.data;
              _this.hide=false;
              waitCheck('close');
            }
          });
        }
      },
      formatNumber(num){
        return number_format(num, 0, ',', ' ');
      }
    }
  })
  ;
});