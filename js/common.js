console.log('Common JS parser Start!');



jQuery(document).on('click', '.parse_cat', function(e) {

    jQuery('.log').prop('style', 'display:block');
   jQuery('.parse_cat').prop('disabled', true);
    jQuery('.log').html('Выполняется загрузка данных... Подождите!');
      jQuery.ajax({
        url: am_common_data.url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'am_parse_cats',
        },
        success: function(data) {
         jQuery('.step2__area').prop('style', 'display:block');  
          jQuery('.log').hide(500);

          for (var key in data) {
            jQuery('.cats_select').append('<option value="'+data[key][1]+'">'+data[key][0]+'</option>');
          }

         console.log("Входящие данные --->>", data);
          swal("Категории загружены!", "Перейдите к шагу 2", "success");
        }
      });
})




jQuery(document).on('change', '.cats_select', function(e) {
let value_option = (e.target.options[e.target.options.selectedIndex].value);

  jQuery('.cats_select').prop('disabled', 'disabled');
  jQuery('.log_subcat').html('Выполняется загрузка данных... Подождите!');
  console.log(value_option);
    jQuery.ajax({
      url: am_common_data.url,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'am_parse_subcats',
        subcat_link: value_option
      },
      success: function(data) {
        jQuery('.step3__area').prop('style', 'display:block');  
        jQuery('.log_subcat').hide(500);
        for (var key in data) {
          jQuery('.subcats_select').append('<option value="'+data[key][1]+'">'+data[key][0]+'</option>');
        }
       console.log("Входящие данные --->>", data);
        swal("Подкатегории загружены!", "Перейдите к шагу 3", "success");
      }
    });

})

jQuery(document).on('change', '.subcats_select', function(e) {
  let value_option = (e.target.options[e.target.options.selectedIndex].value);
    jQuery('.subcats_select').prop('disabled', 'disabled');
    jQuery('.log_subcat_sel').html('Подтвердите выгрузку товаров!');
    swal("Подтвердите выгрузку товаров", "Нажмите на кнопку выгрузки", "warning");
    jQuery('.parser__button').prop('style', 'display: block !important');
  })


  jQuery(document).on('click', '.parser__button', function(e) {
    let value_option_index = jQuery('.subcats_select')[0].options.selectedIndex
    let linkValue = jQuery('.subcats_select')[0].options[value_option_index].value;
    jQuery('.loader').show(150);

 jQuery.ajax({
        url: am_common_data.url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'am_parse',
          linkValue: linkValue
        },
        success: function(data) {
          jQuery('.step2__area').prop('style', 'display:block');  
          jQuery('.parser__button').hide(300);
          jQuery('.log').hide(500);
          jQuery ('.save_csv').show(500);
          jQuery ('.save_csv').attr('href', data);
          jQuery('.loader').hide(250);
          jQuery('.one_more').show();
         console.log("Входящие данные --->>", data);
          swal("Парсинг закончен!",null, "success");
        }
      });


      jQuery(document).on('click', '.one_more', function(e) {
        location.reload();
      })
  
    })