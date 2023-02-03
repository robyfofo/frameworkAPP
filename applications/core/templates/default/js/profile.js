/* app/core/password.js v.1.3.0. 24/09/2020 */
$(document).ready(function() {

    let location_province_id = $('.selectpicker-provincie').val();

    /*
    $('.selectpicker-provincie').on('change', function(){
        location_province_id = $('.selectpicker-provincie').val()
        console.log(location_province_id); //Get the multiple values selected in an array
        //$('.selectpicker').selectpicker('refresh');
    });
    */

   $('.selectpicker-comuni').on('change', function(){
        let comuni_id = $('.selectpicker-comuni').val()

        $.ajax({
            url: siteUrl+'/ajax/geZipCodeFromDbId.php',
            async: "true",
            cache: "false",
            type: "POST",
            data: {'comuni_id':comuni_id},
            dataType: 'html'
            })
        .done(function(data) {
            console.log(data); //Get the multiple values selected in an array
            $('#zip_codeID').val(data);
        })
        .fail(function() {
            alert("Ajax failed to fetch data article for comuni cap");
        })

       
    });


    ///append data dynamically
    /*
    if (this.options.extendData) {
    var ed = this.options.extendData;
    var edl = this.options.extendData.length;
    for (i = 0; i < edl; i++) {
        this.options.data[ed[i].key] = ed[i].func()
    }
    }
    // Invoke the AJAX request.
    this.jqXHR = $.ajax(this.options);
    */
	
	var options = {
        langCode    : charset_lang,
        cache       : false,
        ajax        : {
            url     : siteUrl+'ajax/getJsonComuniFromDbId.php',
            type    : 'POST',

            dataType: 'json',
            data: function() { // This is a function that is run on every request
                return {
                   q        : '{{{q}}}',
                   province_id     : $('.selectpicker-provincie').val()
                };
            }
        },
        locale        : {
            emptyTitle: 'Select and Begin Typing'
        },
        log           : 3,
        preprocessData: function (data) {
            var i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push($.extend(true, data[i], {
                        text : data[i].nome,
                        value: data[i].id
                    }));
                }
            }
            // You must always return a valid array when processing data. The
            // data argument passed is a clone and cannot be modified directly.
            return array;
        }
    };

    $('.selectpicker').selectpicker().filter('.with-ajax').ajaxSelectPicker(options);
    $('.selectpicker').selectpicker({});
    //$('select').trigger('change');
	
});

$('.submittheform').click(function () {
	$('input:invalid').each(function () {
		// Find the tab-pane that this element is inside, and get the id
		var $closest = $(this).closest('.tab-pane');
		var id = $closest.attr('id');
		// Find the link that corresponds to the pane and have it show
		$('.nav a[href="#' + id + '"]').tab('show');
		// Only want to do it once
		return false;
	});
})