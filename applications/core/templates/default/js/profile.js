/* admin/core/profile.js v.7.0.0. 09/02/2022 */
var location_nations_id;
var location_province_id;
var location_comuni_id;
var provincia_alt;
var comune_alt;

setvalidatefields();

$(document).ready(function() {	

    location_nations_id = selected_location_nations_id;
	location_province_id = selected_location_province_id;
    location_comuni_id = selected_location_comuni_id;

    console.log('location_province_id: '+location_province_id);

    $('#location_nations_idID').selectpicker('val', location_nations_id);

    $('#location_province_idID').selectpicker('val', location_province_id);

    $('#location_comuni_idID').selectpicker('val', location_comuni_id);
    
    manageNations();
    manageProvince();
    manageComuni();
    createSelectComuni();
    manageComuni();

    $('.selectpicker-nations').on('change',function(event) {
        location_nations_id = $('#location_nations_idID').val(); 
        manageNations();
        manageProvince();
        manageComuni();
    });

    $('.selectpicker-province').on('change',function(event) {
        location_province_id = $('.selectpicker-province').val();
        manageProvince();
        createSelectComuni();
        manageComuni();
    });

    $('.selectpicker-comuni').on('change', function(){
        location_comuni_id = $('.selectpicker-comuni').val();
        manageComuni();
        if (location_comuni_id > 0) getComuneDetail();
    });

    // controllo esistenza ajax email
    $('#emailID').change(function(){

        var email = $('#emailID').val();
        var id = $('#idID').val();

        $.ajax({
            url             : siteUrl+'ajax/users/checkIfEmailExistsInDb.php',
            type            : "POST",
            async           : false,
            cache           : false,
            data            : 'email='+email+'&id='+id,
            dataType        : 'json'

        })
        .done(function(result) {
            let html = '';
            if (result.error == 0) {
                html = '<span style="color:green;">'+result.message+'</span>';
            } else {
                html = '<span style="color:red;">'+result.message+'</span>';
            }
            $('#emailMessageID').html(html);
        })
        .fail(function() {
            showJavascriptAlert("Errore ajax controllo esistenza email");
        })
    });

    // controllo form
    $('.submittheform').click(function () {

        var l = Ladda.create(document.querySelector('#applyFormID'));
        l.start();

        $('form input').removeClass('input-no-validate');
        $('form select').removeClass('input-no-validate');

        if (false == controlloTabHTML5() ) { 
            return false; 
        }

        if ( controllaNazioni() == false) { return false; }
        if ( controllaProvincie() == false) { return false; }
        if ( controllaComuni() == false ) { return false; }
        
        
        if ( checkEmailExists() == false) { return false; }
 
        if (false == validateFieldByID(validatefields)) { 
            return false; 
        } else {
        }
        return true;

    });

});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
});

function createSelectComuni()
{
    let location_province_id = $('#location_province_idID').val(); 
    let location_comuni_id = $('.selectpicker-comuni').val();

    //console.log('createSeectComuni -> #location_comuni_id: '+location_comuni_id);
    //console.log('createSelectComuni -> #location_province_idID: '+location_province_id);

	$.ajax({
		url             : siteUrl+'ajax/users/getJsonComuniFromDbId.php',
		async           : false,
		cache           : false,
		type            : 'POST',
		data            : {
			location_province_id     : location_province_id
		},
		dataType: 'json'
	})
	.done(function(data) {
		let selectOptions = '';
		let selected= '';
		$.each(data, function( index, value ) {
			selected = '';
			if (value.id === location_comuni_id ) selected = ' selected="selected"';
			selectOptions = selectOptions + '<option'+ selected + ' value="' + value.id + '">' + value.nome +'</option>';
		});
		$('#location_comuni_idID').find('option').remove().end().append(selectOptions);
		$('#location_comuni_idID').val(location_comuni_id);
		$('#location_comuni_idID').selectpicker('refresh');      
	})
	.fail(function() {
		showJavascriptAlert("Errore ajax lettura comuni");
	})
}

function manageNations() {
    let location_nations_id = $('#location_nations_idID').val();

    console.log('mannat -> location_province_id: '+location_province_id);

    $('#labelprovincia_altID').removeClass('responsive-text-right');
    $('#labelcomune_altID').removeClass('responsive-text-right');
    if (location_nations_id == 116) { 
        console.log('mannat -> italia ');
        $('#location_province_idID').selectpicker('val', location_province_id);
        $('#provincia_altID').val('');
        $('#location_comuni_idID').selectpicker('val', location_comuni_id);
        $('#comune_altID').val('');
    } else {
        $('#location_province_idID').selectpicker('val', 0);
        $('#provincia_altID').val(default_provincia_alt);
        $('#location_comuni_idID').selectpicker('val', 0);
        $('#comune_altID').val(default_comune_alt);
    }
}

function manageProvince() {
    $('#labelprovincia_altID').removeClass('responsive-text-right');

    if (location_province_id > 0) {
        $('#labelprovincia_altID').hide();
        $('#divfieldprovincia_altID').hide();
        $('#divcontrolfieldprovincia_altID').hide();
        $('#location_province_idID').val(location_province_id);

        $('#provincia_altID').val('');
        $('#location_comuni_idID').val(location_comuni_id);
        $('#comune_altID').val('');
    } 
    if (location_province_id == 0) {
        $('#labelprovincia_altID').addClass('responsive-text-right');
        $('#labelprovincia_altID').show();
        $('#divfieldprovincia_altID').show();
        $('#divcontrolfieldprovincia_altID').show();
        $('#provincia_altID').val(default_provincia_alt);   
        $('#location_comuni_idID').val(0);
        $('#comune_altID').val(default_comune_alt);
    }
}

function manageComuni() {
    $('#labelcomune_altID').removeClass('responsive-text-right');
    if (location_comuni_id === null) location_comuni_id = 0;
    if (location_comuni_id > 0) {
        $('#labelcomune_altID').hide();
        $('#divfieldcomune_altID').hide();
        $('#divcontrolfieldcomune_altID').hide();

        $('#comune_altID').val('');
    }
    if (location_comuni_id == 0) {
        $('#labelcomune_altID').addClass('responsive-text-right');
        $('#labelcomune_altID').show();
        $('#divfieldcomune_altID').show();
        $('#divcontrolfieldcomune_altID').show();
        $('#comune_altID').val(default_comune_alt);
    }
}

function getComuneDetail()
{
    let location_comuni_id = $('.selectpicker-comuni').val()
    $.ajax({
        url: siteUrl+'ajax/users/getComuneDetailsFromDbId.php',
        async: false,
        cache: false,
        type: "POST",
        data: {'location_comuni_id':location_comuni_id},
        dataType: 'json'
    })
    .done(function(data) {
        $('#zip_codeID').val(data.cap);
        $('#location_comuni_idID').selectpicker('refresh');
        manageComuni();
    })
    .fail(function() {
        showJavascriptAlert("Errore ajax lettura dettaglio comune");
    })
}

// specifiche
function controllaNazioni()
{
    location_nations_id = $('#location_nations_idID').val();
    if (location_nations_id == null) location_nations_id = 0;
    if (location_nations_id == 0) {
        controlloTabHTML5HideTabs()
        controlloTabHTML5ShowTabs('anagrafica','location_nations_id');
        showJavascriptAlert(messages['Devi inserire una nazione!']);
        return false
    } else {
       return true
    }
}

function controllaProvincie()
{
    location_province_id = $('#location_province_idID').val();
    provincia_alt = $('#provincia_altID').val();
    if (location_province_id == 0 && provincia_alt == '') {
        controlloTabHTML5HideTabs()
        controlloTabHTML5ShowTabs('anagrafica','location_provincie_id');
        showJavascriptAlert(messages['Devi inserire una provincia!']);
        return false
    } else {
       return true
    }
}

function controllaComuni()
{
    location_comuni_id = $('#location_comuni_idID').val();
    if (location_comuni_id === null) location_comuni_id = 0;
    comune_alt = $('#comune_altID').val();
    if (location_comuni_id == 0 && comune_alt == '') {
        controlloTabHTML5HideTabs()
        controlloTabHTML5ShowTabs('anagrafica','location_comuni_id');
        showJavascriptAlert(messages['Devi inserire un comune!']);
        return false
    } else {
       return true
    }
}

function checkEmailExists() {
    var email = $('#emailID').val();
    var id = $('#idID').val();
    $.ajax({
        url             : siteUrl+'ajax/users/checkIfEmailExistsInDb.php',
        type            : "POST",
        async           : false,
        cache           : false,
        data            : 'email='+email+'&id='+id,
        dataType        : 'json'
    })
    .done(function(result) {
        let html = '';
        if (result.error > 0) {
            showJavascriptAlert(mess.message);
            return false;
        }
        return true;
    })
    .fail(function() {
        showJavascriptAlert("Errore ajax controllo esistenza email");
    })
};

// params [id tabs] [tipo validazione] [nome campo da validare] [valori riferimento] [index array messaggio]
function setvalidatefields() {
    let x = 0;
    validatefields[x] = ['datibase','isemail','emailID','0','0'];
    x++;
	validatefields[x] = ['datibase','maxchar','emailID','255',1];

    x++;
	validatefields[x] = ['datibase','maxchar','nameID','50',0];
    x++;
	validatefields[x] = ['datibase','maxchar','surnameID','50'],0;
    x++;
	validatefields[x] = ['datibase','maxchar','streetID','100'],0;
    x++;
	validatefields[x] = ['datibase','maxchar','zip_codeID','10',0];

    x++;
    validatefields[x] = ['datibase','telephone','telephoneID','20',0];
    x++;
    validatefields[x] = ['datibase','maxchar','telephoneID','20',1];

    x++;
    validatefields[x] = ['datibase','telephone','mobileID','20',0];
    x++;
    validatefields[x] = ['datibase','maxchar','mobileID','20',1];

    x++;
    validatefields[x] = ['datibase','telephone','faxID','20',0];
    x++;
    validatefields[x] = ['datibase','maxchar','faxID','20',1];

    x++;
    validatefields[x] = ['datibase','maxchar','skypeID','20',1];
}
