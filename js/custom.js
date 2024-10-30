jQuery(document).ready(function ($) {
    jQuery("[data-toggle='switch']").bootstrapSwitch();
    $('[data-toggle="checkbox"]').radiocheck();
    $('[data-toggle="radio"]').radiocheck();
    $('#switchonoff').on('switchChange.bootstrapSwitch', function(event, state) {
        console.log(this); // DOM element
        console.log(event); // jQuery event
        console.log(state); // true | false
        if(state){
            jQuery.post('', {'wpsctotop_switchonoff': 1}, function (e) {
                if (e == 'error') {
                    error('error');
                }else{
                    jQuery('#wpsctotop_circ').css("background", "#0f0");
                }
            });
        }else{
            jQuery.post('', {'wpsctotop_switchonoff': 0}, function (e) {
                if (e == 'error') {
                    error('error');
                } else {
                    jQuery('#wpsctotop_circ').css("background", "#f00");
                }
            });
        }
      });

    $(document).on('click', '.crd-icons', function () {

        obj = $(this);
        var id = obj.data('id');

        $('.crd-icons').each(function () {
            $(this).removeClass('selected');
        })

        obj.addClass('selected');
        $('#icon_id').val(id);
        //console.log(id);                
    });    

});
