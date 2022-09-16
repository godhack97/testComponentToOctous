var arrSlider = [];
var stran = document.querySelector('.shutter');
var temp = 0.3; //время анимации в секундах
var timeAnimate = 0.2;//время анимации в секундах
var p3 = Power3.easeOut;
var dg,dgss,dist,pos, pp, pc, sumHeight;


$(function() {

  var pdl = $('.product-detail').length;
  var ws = $('.product-detail').css('width');
  var wd = $('.current').position();
  var sost = 'niz';
  var win_w = window.innerWidth;

  $('.current').prev().addClass('prev').css('left', '-100vw');
  $('.current').next().addClass('next');
  $('.current > .info').addClass('shutter');

  //  console.log('start '+ pdl +', '+ ws +', '+ wd);
  //  console.log(wd);
  // console.log($('.product-detail:first').hasClass('current'));
  
  if(isMobile())
  {
    sHutTer();
    pOs_Shuter_co();
  }
  
  function tONext()
  {
    return false;
    
    console.log('function tONext()');
    TweenMax.to('.current', timeAnimate, {
      left: '-' + ws + '',
      onStart: bacSL
    });

    TweenMax.to('.next', timeAnimate, {
      left: "0px",
      onComplete: lEftReClas
    });
    
    dg.disable();
  }


  function tOPrive() 
  {
    return false;
    
    console.log('function tOPrive()');
    TweenMax.to('.current', timeAnimate, {
      left: '+' + ws + ''
    });
    
    TweenMax.to('.prev', timeAnimate, {
      left: "0px",
      onComplete: rIftReClas
    });
    
    dg.disable();
  }


  function lEftReClas() 
  {
    console.log('function lEftReClas()');
    $('.prev').removeClass('prev');
    $('.shutter').removeClass('shutter');
    $('.current').removeClass('current');
    $('.next').addClass('current');
    $('.next').removeClass('next');

    $('.current').prev().addClass('prev');
    $('.current>.info').addClass('shutter');
    $('.current').next().addClass('next');
    
    dg.enable();
    sHutTer();
  }

  function rIftReClas() 
  {
    console.log('function rIftReClas()');
    $('.shutter').removeClass('shutter');
    $('.next').removeClass('next');
    $('.current').removeClass('current');
    $('.prev').addClass('current');
    $('.prev').removeClass('prev');

    $('.current').prev().addClass('prev');
    $('.current>.info').addClass('shutter');
    $('.current').next().addClass('next');
    
    dg.enable();
    sHutTer();
  }


  function Ulo() 
  {
    console.log('function Ulo()');
    if ($('.product-detail:last').hasClass('current')) {
      // console.log('last');
      bacSL();
    } else {
      tONext();
      bacSL()
    }
  }


  function deUlo() 
  {
    console.log('function deUlo()');
    if ($('.product-detail:first').hasClass('current')) {
      //console.log('first');
      bacSL()
    } else {
      tOPrive();
      bacSL()
    }
  }


  function bacSL() 
  {
    console.log('function bacSL()');
    TweenMax.to('.products-drag', timeAnimate, {
      x: 0
    });
  }


  if(isMobile())
  {
    dg = {
      disable: function(){},
      enable: function(){},
    }; //new Draggable(".products-drag", {
//    dg_BACK = new Draggable(".products-drag", {
//      type: "x",
//      throwProps: true,
//      zIndexBoost: false,
//      onDrag: function() {
//        console.log('products-drag onDrag '+ this.x);
//      },
//      onDragEnd: function() {
//        console.log('products-drag onDragEnd '+ this.x);
//        if (this.x <= -30) {
//          Ulo();
//          switchProduct(1);
//        } else
//        if (this.x >= 30) {
//          deUlo(); 
//          switchProduct(0);
//        } else {
//          bacSL();
//        }
//
//      }
//    });
  }


  $(window).resize(function() {
    if (isMobile())
      dg.enable();
    else
      dg.disable();
  });


  function sHutTer()
  {
    console.log('function sHutTer()');
    
    stran = document.querySelector('.shutter');
    win_w = window.innerWidth;
    dist = stran.offsetTop;

    //console.log(dist);
    window.addEventListener('resize', function(){
      win_w = window.innerWidth;
      if(win_w<=768){
        dist = stran.offsetTop;
        pos = parseInt(dist / 4);
      }
    } , false );

    pos = parseInt(dist / 4);


    dgss = new Draggable(".current .shutter", {
      type: "top",
      deltaY: dist,
      throwProps: true,
      edgeResistance: 0.65,
      zIndexBoost: false,
      onDragEnd: function() {
        
        pp = dist - stran.offsetTop;
        pc = stran.offsetTop;


        console.log('onDragEnd _ def '+ sost +' '+ pp +' '+ pos +' '+ pc);
        
        if (sost == 'niz') 
        {
          if (pp < pos) {
            toBottom();
            sost='niz';
          } else {
            toTop();
            sost='verh';
          }
        }
        else 
        {
          if (pc > pos) {
            toBottom();
            sost='niz';
          } else {
            toTop();
            sost='verh';
          }
        }
      }
    });
  }


  $('#products-wrap').on('click', '.js-shutter-close', function(){
    toBottom();
  });
  

});


function toTop() 
{
  console.log('function toTop()');
  
  TweenMax.to(stran, temp, { top: '0', ease: p3, zIndex: 1300 });
  sost = 'verh';
  $('.current > .shutter').addClass('active');
  dg.disable();
  //console.log(1);
 
  //$('.products-drag').removeAttr('style');
  //$('header').addClass('hide-mobile-icons');
  $('.products-drag').addClass('hide-mobile-icons');
  $('#basket-mini-wr, .mobile-arrow-back').addClass('hidden');
  
  setTimeout(function(){
    //$('.current > .shutter').removeAttr('style').css({top: 0}).css('background-color', '#f9e4c7');
  }, 1000);
}


function toBottom() 
{
  console.log('function toBottom()');
  
  TweenMax.to(stran, temp, { top: dist + 'px', ease: p3, zIndex: 10 });
  sost = 'niz';
  $('.current > .shutter').removeClass('active');
  dg.enable();
  // console.log(dg);
  
  // $('header').removeClass('hide-mobile-icons');
  $('.products-drag').removeClass('hide-mobile-icons');
  $('#basket-mini-wr, .mobile-arrow-back').removeClass('hidden');
}


function pOs_Shuter_co()
{
  console.log('function pOs_Shuter_co()');
  
  if(isMobile() && parseInt($('.current .info h1').height(), 10) > 16)
  {
    sumHeight = 0;
    sumHeight += parseInt($('.current .info .indent').css('padding-top'), 10);
    sumHeight += parseInt($('.current .info .indent h1').outerHeight(true), 10);
    sumHeight += parseInt($('.current .info .indent .price').outerHeight(true), 10);
    sumHeight += parseInt($('.current .info .indent .mobile-buy .ibutton').outerHeight(true), 10) + 5;
    // console.log(sumHeight);
    TweenMax.set('.shutter', {top: ($(window).height() - sumHeight) +'px'});
  }
}


/**
 * При первой загрузки добавляем слева и справа по товару
 */
function startProduct()
{
  if(!isMobile())
    return;
  
  console.log('function startProduct()');
  
  $.ajax({
    type: 'POST',
    url: '/include/get_product.php',
    data: {
      direction: 'start',
      product_id: $('.product-detail.current').data('id'),
      section_id: $('#products-wrap .wrapper-products').data('section')
    },
    dataType: 'json',
    success: function(result) {

      //console.log(result);
      
      if(result.start.left_id != 'N')
      {
        $.post('/include/get_product_id.php', { id: result.start.left_id },function(data){
          
          $('#products-wrap .wrapper-products').prepend(data);
          $('#products-wrap .wrapper-products').find('script').remove();
          $('.current').prev().addClass('prev').css('left', '-100vw');
          
          var selectorSlider = '.product-'+ result.start.left_id +' .image-slider-color';
          
          if($(selectorSlider).length)
          {
            arSliderMainColors[result.start.left_id] = [];
            
            $(selectorSlider).each(function(index){
              var color = $(this).attr('data-color');
              sliderMainParams.container = selectorSlider +'.color-'+ color;
              arSliderMainColors[result.start.left_id][color] = tns(sliderMainParams);

              var info = arSliderMainColors[result.start.left_id][color].getInfo();
              $('#'+ info.container.offsetParent.id).addClass('color-'+ color);
              if(index == 0)
                $('#'+ info.container.offsetParent.id).addClass('active');


              // Если долистали до последнего слайда и ещё раз свайпаем вверх - скроллим контент вверх
              arSlideNumber[color] = 0;
              arSliderMainColors[result.start.left_id][color].events.on('touchStart', function() {
                arSlideNumber[color] = arSliderMainColors[result.start.left_id][color].getInfo().index;
              });

              arSliderMainColors[result.start.left_id][color].events.on('touchEnd', function() {
                if ((arSliderMainColors[result.start.left_id][color].getInfo().index === arSlideNumber[color]) && (arSlideNumber[color] != 0))
                  toTop();
              });

            });
          }
          
        });
      }
      
      if(result.start.right_id != 'N')
      {
        $.post('/include/get_product_id.php', { id: result.start.right_id },function(data){
          
          $('#products-wrap .wrapper-products').append(data);
          $('#products-wrap .wrapper-products').find('script').remove();
          $('.current').next().addClass('next');
          
          var selectorSlider = '.product-'+ result.start.right_id +' .image-slider-color';
          
          if($(selectorSlider).length)
          {
            arSliderMainColors[result.start.right_id] = [];
            
            $(selectorSlider).each(function(index){
              var color = $(this).attr('data-color');
              sliderMainParams.container = selectorSlider +'.color-'+ color;
              arSliderMainColors[result.start.right_id][color] = tns(sliderMainParams);

              var info = arSliderMainColors[result.start.right_id][color].getInfo();
              $('#'+ info.container.offsetParent.id).addClass('color-'+ color);
              if(index == 0)
                $('#'+ info.container.offsetParent.id).addClass('active');


              // Если долистали до последнего слайда и ещё раз свайпаем вверх - скроллим контент вверх
              arSlideNumber[color] = 0;
              arSliderMainColors[result.start.right_id][color].events.on('touchStart', function() {
                arSlideNumber[color] = arSliderMainColors[result.start.right_id][color].getInfo().index;
              });

              arSliderMainColors[result.start.right_id][color].events.on('touchEnd', function() {
                if ((arSliderMainColors[result.start.right_id][color].getInfo().index === arSlideNumber[color]) && (arSlideNumber[color] != 0))
                  toTop();
              });

            });
          }
          
        });
      }
      
    }
  });
}


/**
 * При свайпе переключаемся на другой товар.
 * Функция добавляет следующий товар.
 */
function switchProduct(dir)
{
  console.log('function switchProduct('+ dir +')');
  

  var direction = 'right';
  if(dir == 1)
    direction = 'left';


  // Определяем сколько товаров уже есть в указанном направлении
  var dirCounts = 0;
  if(direction == 'left')
    dirCounts = $('.product-detail.current').nextAll('.product-detail').length;
  else
    dirCounts = $('.product-detail.current').prevAll('.product-detail').length;

  console.log('function switchProduct('+ dir +') '+ dirCounts );
  
  // Блокируем добавление если это не крайний товар и если это конец раздела
  if(dirCounts > 1 || isNaN(parseInt($('.product-detail.'+ (direction == 'left' ? 'next' : 'prev') ).data('id'))))
    return;

  console.log('function switchProduct('+ dir +') ajax go '+ $('.product-detail.'+ (direction == 'left' ? 'next' : 'prev') ).data('id'));
  
  // Получаем следующий товар
  $.ajax({
    type: 'POST',
    url: '/include/get_product.php',
    data: {
      direction: direction,
      product_id: $('.product-detail.'+ (direction == 'left' ? 'next' : 'prev') ).data('id'),
      section_id: $('#products-wrap .wrapper-products').data('section')
    },
    dataType: 'json',
    success: function(result) {

      console.log('function switchProduct('+ dir +') result');
      console.log(result);
     
      //      if(result.html == 'N')
      //        return;
      
      if(parseInt(result.id) > 0)
      {
        $.post('/include/get_product_id.php', { id: result.id }, function(data){

          if(direction == 'left')
          {
            $('#products-wrap .wrapper-products').append(data);
            $('#products-wrap .wrapper-products').find('script').remove();
            $('.current').next().addClass('next');
          }
          else
          {
            $('#products-wrap .wrapper-products').prepend(data);
            $('#products-wrap .wrapper-products').find('script').remove();
            $('.current').prev().addClass('prev').css('left', '-100vw');
          }

          var selectorSlider = '.product-'+ result.id +' .image-slider-color';

          if($(selectorSlider).length)
          {
            arSliderMainColors[result.id] = [];

            $(selectorSlider).each(function(index){
              var color = $(this).attr('data-color');
              sliderMainParams.container = selectorSlider +'.color-'+ color;
              arSliderMainColors[result.id][color] = tns(sliderMainParams);

              var info = arSliderMainColors[result.id][color].getInfo();
              $('#'+ info.container.offsetParent.id).addClass('color-'+ color);
              if(index == 0)
                $('#'+ info.container.offsetParent.id).addClass('active');


              // Если долистали до последнего слайда и ещё раз свайпаем вверх - скроллим контент вверх
              arSlideNumber[color] = 0;
              arSliderMainColors[result.id][color].events.on('touchStart', function() {
                arSlideNumber[color] = arSliderMainColors[result.id][color].getInfo().index;
              });

              arSliderMainColors[result.id][color].events.on('touchEnd', function() {
                if ((arSliderMainColors[result.id][color].getInfo().index === arSlideNumber[color]) && (arSlideNumber[color] != 0))
                  toTop();
              });

            });
          }

        });
      }

      //      $.post('/include/get_product_id.php', { id: result.id },function(data){
      //        //console.log(data);
      //        if(direction == 'left')
      //        {
      //          $('#products-wrap .wrapper-products').append(data);
      //          $('#products-wrap .wrapper-products').find('script').remove();
      //          $('.current').next().addClass('next');
      //        }
      //        else
      //        {
      //          $('#products-wrap .wrapper-products').prepend(data);
      //          $('#products-wrap .wrapper-products').find('script').remove();
      //          $('.current').prev().addClass('prev').css('left', '-100vw');
      //        }
      //        
      //        arrSlider[result.id] = tns({
      //          container: '.image-slider-'+ result.id,
      //          items: 1,
      //          axis: 'vertical',
      //          controls: false,
      //          slideBy: 'page',
      //          autoplay: false,
      //          mouseDrag: true,
      //          preventScrollOnTouch: 'force',
      //          loop: false,
      //        });
      //        
      //        // Если долистали до последнего слайда и ещё раз свайпаем вверх - скроллим шторку вверх
      //        var slideNumber = 0;
      //        arrSlider[result.id].events.on('touchStart', function() {
      //          slideNumber = arrSlider[result.id].getInfo().index;
      //        });
      //
      //        arrSlider[result.id].events.on('touchEnd', function() {
      //          if ((arrSlider[result.id].getInfo().index === slideNumber) && (slideNumber != 0))
      //            toTop();
      //        });
      //      });

    }
  });

}


