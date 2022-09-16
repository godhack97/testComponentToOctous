$(function()
{
  window.files = [];
  // Изменять name по выбору select'а
  $('.new-select').on('change', function()
  {
    if( $(this).hasClass('size-select')){
      var selVal = $(this).val();
      var selID = $(this).attr('id');
      if(selID){
        $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
      }
    }
    if( $(this).hasClass('color-select')){
      var selVal = $(this).val();
      var selID = $(this).attr('id');
      if(selID){
        selID = selID.split('-')[1];
        $(this).parents('.color-block').find('.sizes-element select').attr('name', "sizes["+selVal+"][]");
        $(this).parents('.color-block').find('.upload-field').attr('id', selVal);
        $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
        $(this).parents('.color-block').find('.active-element input').attr('name', "active["+selVal+"]");
      }
    }
    //$('.file-' + selID).attr('name', selVal + '[]');
  });
  $('#article').on('change', function(){
    let formData;
    if($(this).val() !== ''){
      waitCheck();
      $.ajax({
        type: 'GET',
        url: '/include/ajax_search_product.php?article=' + $(this).val(),
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
          console.log(textStatus);
          console.log(errorThrown);
        },
        success: function (result) {
          if(result.status)
          {
            popupsClose();
            // setTimeout(function(){
            popupOpenMessage('Данный артикул уже используется. Пожалуйста, введите другой.');
            $('#create-product').prop('disabled', true);
            // }, 500);

            //
          }
          else{
            $('#create-product').prop('disabled', false);
          }

          waitCheck('close');
        }
      });
    }
  });
  // Сохранение/редактирование
  $('form[name=save_product]').submit(function ()
  {
    if($('body').hasClass('wait')) return;
    if($('#section_id').val() != 0){
      if(document.save_product.reportValidity()){

        waitCheck();
        let form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('site_id', BX.message('SITE_ID'));
        window.files.forEach(function(item) {
          formData.append(item.key + '[]', item.image);
        });

        $.ajax({
          type: 'POST',
          url: '/include/ajax_save_product.php',
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          data: formData,
          dataType: 'json',
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest);
            console.log(textStatus);
            console.log(errorThrown);
          },
          success: function (result) {
            console.log(result);
            if(result.status)
            {
              popupsClose();
              // setTimeout(function(){
              popupOpenMessage('Данные успешно сохранены');
              // }, 500);
              if(form.data('type-form') == 'add'){
                window.location.href = form.data('return');
              }
              if(form.data('type-form') == 'edit'){
                setTimeout(function() {
                  window.location.reload();
                }, 1000);
              }
              //
            }
            else
            {
              if(result.validation_messages) {
                setTimeout(function () {
                  popupOpenMessage(result['validation_messages']);
                  window.location.reload();
                }, 500);
              }
              else{
                setTimeout(function () {
                  popupOpenMessage(result['messages']);
                }, 500);
              }

              formResultDisplay(form, result.message);
            }

            waitCheck('close');
          }
        });
      }
    }
    else{
      popupOpenMessage('Выберите раздел');
    }

    return false;
  });

  // Удаление
  $('.delete-product').on('click', function()
  {
    var productID = $(this).val();

    waitCheck();

    var formData = new FormData();
    formData.append('product_id', productID);
    formData.append('site_id', BX.message('SITE_ID'));

    if(confirm('Вы уверены, что хотите удалить этот товар?'))
    {
      $.ajax({
        type: 'POST',
        url: '/include/ajax_delete_product.php',
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
          console.log(textStatus);
          console.log(errorThrown);
        },
        success: function (result) {
          console.log(result);
          if(result.status)
          {
            popupsClose();
            // setTimeout(function(){
            // popupOpenMessage('Продукт успешно удалён!');
            // }, 500);

            window.location.href = '/personal/products/';
          }
          else
          {
            if(result.validation_messages)
              setTimeout(function(){
                popupOpenMessage(result['validation_messages']);
              }, 500);

            console.log(result.message);
          }

          waitCheck('close');
        }
      });
    }
  });

  // Отмена (вместо удалить, когда создание нового)
  $('.cancel-product').on('click', function()
  {
    window.location.href = '/personal/products/';
  });

});

// *************** NEW SCRIPTS *************** //


function initSelect2() {
  $(".new-select").select2();
  $('.new-select').on('select2:select', function (e) {
    $(this).closest(".select-field").addClass("selected");
  }).on('select2:unselect', function (e) {
    $(this).closest(".select-field").find(".select2-selection__rendered").each(function(){
      var current_item = $(this);
      if(current_item[0].innerHTML !== "") {
        if($($(current_item[0]).children()[0]).hasClass('select2-search--inline')){
          $(this).closest(".select-field").removeClass("selected");
        }
        else{
          if(!$(this).closest(".select-field").hasClass("selected")){
            $(this).closest(".select-field").addClass("selected");
          }
        }
      }
      else{
        $(this).closest(".select-field").removeClass("selected");
      }
    })
  });

  $(".select2-selection__rendered").each(function(){
    var current_item = $(this);
    if(current_item[0].innerHTML !== "") {
      if($($(current_item[0]).children()[0]).hasClass('select2-search--inline')){
        $(this).closest(".select-field").removeClass("selected");
      }
      else{
        if(!$(this).closest(".select-field").hasClass("selected")){
          $(this).closest(".select-field").addClass("selected");
        }
      }
      $(this).closest(".select-field").addClass("selected");
    }
    else{
      $(this).closest(".select-field").removeClass("selected");
    }
  })
}

function cloneMaterial() {
  $(document).on('click','.section-editing .add-material',function () {
    $(".section-editing .characteristics-wrapper .characteristics-content .characteristics-item.characteristics-add select").prepend("<option class='empty'>—</option>");
    var get_value = $(".section-editing .characteristics-wrapper .characteristics-content .characteristics-item.characteristics-add .field input").attr("value");
    $(".section-editing .characteristics-wrapper .characteristics-content .characteristics-item.characteristics-add .field input").attr("value", "");
    var new_material = $(this).closest('.characteristics-content').find(".characteristics-add").clone();
    $(".section-editing .characteristics-wrapper .characteristics-content .characteristics-item.characteristics-add .field input").attr("value", ""+get_value+"");
    $(".section-editing .characteristics-wrapper .characteristics-content .characteristics-item.characteristics-add select option.empty").remove();
    $(this).closest(".characteristics-content").append(new_material);
    new_material[0].classList.add("cloned");
    new_material[0].classList.remove("characteristics-add");
    $(new_material[0]).prop('selectedIndex',0);
    $('.characteristics-wrapper .cloned .select2').remove();
    $('.characteristics-wrapper .characteristics-add .select2').remove();
    $('.characteristics-wrapper .characteristics-add .new-select').select2();
    $('.characteristics-wrapper .cloned .new-select').select2();
  });
}

function initFormAccept() {
  $(".bx-auth-profile.profile_new .partner-wrapper .accept-btn").click(function(){
    $(this).closest(".partner-wrapper").addClass("accepted");
  });
}

function removeMaterial() {
  $(document).on('click','.section-editing .remove-characteristics',function () {
    $(this).closest(".characteristics-item").remove();
  });
}

function iniUploadImages() {
  $(document).on('change', '.section-editing .upload-field', function(){
    var target_item = $(this),
      image_wrapper = target_item.closest('.color-element').find('.images');
    if (window.File && window.FileReader && window.FileList && window.Blob){
      var data = target_item[0].files;
      var dateNow = new Date();
      $.each(data, function(index, file){
        let keyFileList = target_item.attr('id')+'_'+dateNow.getTime()
        window.files.push({key: keyFileList, id: target_item.attr('id'), image: file});

        if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){
          var fRead = new FileReader();
          fRead.onload = (function(file){
            return function(e) {
              var img = $('<a class="img" data-fancybox="gallery" href="'+e.target.result+'"><img src="'+e.target.result+'" /><span class="remove-image" data-type="uploaded" data-key="'+keyFileList+'"></span></a>');
              // var img = $('<img/>').attr('src', e.target.result);
              image_wrapper.append(img);
            };
          })(file);
          fRead.readAsDataURL(file);
        }
      });
    }else{
      alert("Your browser doesn't support File API!");
    }
  });
}

function cloneColorsWrapper() {
  $(document).on('click','.section-editing .colors-wrapper .add-color',function () {
    let sourceColor =  $(this).closest('.colors-wrapper').find(".color-block.color-element-add");
    let sourceColorContainer = sourceColor.closest(".colors-wrapper")

    let clonedColorElem = $(this).closest('.colors-wrapper').find(".color-block.color-element-add").clone();
    let clonedColor = $(clonedColorElem[0]);
    sourceColor.addClass("cloned");
    sourceColor.removeClass("color-element-add");
    clonedColor.closest('.color-block').find('.images .img').remove();

    sourceColorContainer.append(clonedColorElem);

    let colorOldID = clonedColor.closest('.colors-wrapper').find('.color-element-add .color-element .new-select');
    let sizeOldID = clonedColor.closest('.colors-wrapper').find('.color-element-add .sizes-element .new-select');
    let colorSplitID = colorOldID.attr('id').split('-');
    let sizeSplitID = sizeOldID.attr('id').split('-');

    colorOldID.attr('id', colorSplitID[0] + '-' + Number(parseInt(colorSplitID[1]) + 1));
    colorOldID.attr('data-select2-id', colorSplitID[0] + '-' + Number(parseInt(colorSplitID[1]) + 1));
    sizeOldID.attr('id', sizeSplitID[0] + '-' + Number(parseInt(sizeSplitID[1]) + 1));
    sizeOldID.attr('data-select2-id', sizeSplitID[0] + '-' + Number(parseInt(sizeSplitID[1]) + 1));
    sourceColor.closest('.color-block').find('.select2').remove();
    clonedColor.closest('.color-block').find('.select2').remove();
    clonedColor.closest('.color-block').find('input').prop('checked', false);
    clonedColor.closest('.color-block').find('select option').each(function(item){
      $(this).prop('selected', false);
    });
    sourceColorContainer.find('.select2').remove();
    sourceColorContainer.find('.new-select').select2();
    //clonedColor.closest('.colors-wrapper').find('.new-select').select2();
    //sourceColor.closest('.colors-wrapper').find('.new-select').select2();

    let selectChildChecked = clonedColor.find('.sizes-element .select2-selection__rendered').children()[0];
    if($(selectChildChecked).hasClass('select2-search--inline')){
      $(selectChildChecked).closest(".select-field").removeClass("selected");
    }
    else{
      if(!$(selectChildChecked).closest(".select-field").hasClass("selected")){
        $(selectChildChecked).closest(".select-field").addClass("selected");
      }
    }
    clonedColor.closest('.color-block').find('.new-select').on('change', function()
    {
      if( $(this).hasClass('size-select')){
        var selVal = $(this).val();
        var selID = $(this).attr('id');
        if(selID){
          $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
        }
      }
      if( $(this).hasClass('color-select')){
        var selVal = $(this).val();
        var selID = $(this).attr('id');
        if(selID){
          selID = selID.split('-')[1];
          $(this).parents('.color-block').find('.sizes-element select').attr('name', "sizes["+selVal+"][]");
          $(this).parents('.color-block').find('.upload-field').attr('id', selVal);
          $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
          $(this).parents('.color-block').find('.active-element input').attr('name', "active["+selVal+"][]");
        }
      }
    });
    //$(".section-editing .colors-wrapper .color-block.color-element-add select").prepend("<option class='empty'>—</option>");
    //var new_material = $(this).closest('.colors-wrapper').find(".color-block.color-element-add").clone();
    //var clonedColor = $(new_material[0]).find(".color-element-add");
    //var clonedSizes = $(new_material[0]).find(".sizes-element-add");
    //$(new_material[0]).addClass("cloned").removeClass("color-element-add");
    //clonedSizes.addClass("cloned").removeClass("sizes-element-add");
    //$(new_material[0]).find(".color-element-add").remove("color-element-add");
    //var new_material = $(this).closest('.colors-wrapper').find(".color-element-add").clone();
    //$(".section-editing .colors-wrapper .color-block.color-element-add select option.empty").remove();
    //$(".section-editing .colors-wrapper .color-element.sizes-element-add select option.empty").remove();
    //new_material.find("a").remove();
    //$(this).closest(".colors-wrapper").append(new_material);
    //new_material[0].classList.add("cloned");
    //new_material[0].classList.remove("color-element-add");


    //let oldID = $('.colors-wrapper .color-element-add .color-element .new-select').attr('id');
    //let oldIDSize = $('.colors-wrapper .color-element-add .sizes-element .new-select').attr('id');
    //let splittedID = oldID.split('-');
    //let splittedIDSize = oldIDSize.split('-');
    //$('.colors-wrapper .color-element-add .color-element .new-select').attr('id', splittedID[0] + '-' + Number(parseInt(splittedID[1]) + 1));
    //$('.colors-wrapper .color-element-add .sizes-element .new-select').attr('id', splittedIDSize[0] + '-' + Number(parseInt(splittedIDSize[1]) + 1));

    //$('.colors-wrapper .cloned .select2').remove();
    //$('.colors-wrapper .color-element-add .color-element .select2').remove();
    //$('.colors-wrapper .color-element-add .color-element .new-select').select2();
    //$('.colors-wrapper .color-element-add .sizes-element .select2').remove();
    //$('.colors-wrapper .color-element-add .sizes-element .new-select').select2();
    //$('.colors-wrapper .cloned .new-select').select2();

    //$('.colors-wrapper .cloned .new-select').trigger('change');
    //$('.colors-wrapper .cloned .new-select').on('change', function()
    //{
    //  if( $(this).hasClass('size-select')){
    //    var selVal = $(this).val();
    //    var selID = $(this).attr('id');
    //    if(selID){
    //      $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
    //    }
    //  }
    //  if( $(this).hasClass('color-select')){
    //    var selVal = $(this).val();
    //    var selID = $(this).attr('id');
    //   if(selID){
    //      selID = selID.split('-')[1];
    //      $(this).parents('.color-block').find('.sizes-element select').attr('name', "sizes["+selVal+"][]");
    //      $(this).parents('.color-block').find('.upload-field').attr('id', selVal);
    //      $("#"+ $(this).attr('id') +" option[value='"+ selVal +"']").prop('selected', true);
    //      $(this).parents('.color-block').find('.active-element input').attr('name', "active["+selVal+"][]");
    //    }
    //  }
    //});
  });
}

function initTextArea() {
  $('textarea').each(function () {
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
  }).on('input', function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
    if($(this).val() == "") {
      this.style.height = '37px';
    }
  });
}
function removeImage(){
  $(document).on('click', '.remove-image', function(){
    $(this).closest("a").remove();
    if($(this).data('type') === 'uploaded'){
      let indexFile = window.files.findIndex(function(item){
        return item.key === $(this).data('key');
      });
      window.files.splice(indexFile, 1);
    }
    return false;
  });
}
$( window ).on( "load", function() {
  //initFormAccept();
  if($(".new-select").length) {
    initSelect2();
    cloneMaterial();
    removeMaterial();
    iniUploadImages();
    cloneColorsWrapper();
    initTextArea();
    removeImage();
  }
});