/* app/orders/formOrders.js v.1.3.0. 02/10/2020 */
var requestSent = false;
$(document).ready(function() {	

    $('#warehouse_products_idID').change(function () {
		let id = parseInt( $('#warehouse_products_idID').val() );
		console.log(id);
		if (id > 0) {

			$.ajax({
				url: siteUrl+'ajax/getJsonProductFromDbId.php',
				async: "true",
				cache: "false",
				type: "POST",
				data: {'id':id},
				dataType: 'json'
			})
			.done(function(data) {
				let contenthtml = '<div>' + data.content + '</div>';
				let contenttext = $(contenthtml).text();
				let content = data.title + '\n' + contenttext;
				$('#art_contentID').val(content);
				$('#id_articleID').val(id);
				$('#art_price_unityID').val(data.price_unity);
				$('#art_taxID').val(data.tax);
				refreshValuesFromPrice();
			})
			.fail(function() {
				alert("Ajax failed to fetch data article for module");
			})
		}     
        //$('#cityID').val(nome);
    });

   var options = {
	langCode    : charset_lang,
	cache       : true,
	ajax        : {
		url     : siteUrl+'ajax/getJsonProductsFromDb.php',
		type    : 'POST',

		dataType: 'json',
		// Use "{{{q}}}" as a placeholder and Ajax Bootstrap Select will
		// automatically replace it with the value of the search query.
		data    : {
			q: '{{{q}}}'
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
					text : data[i].title,
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
$('select.after-init').append('<option value="6336" data-subtext="" selected="selected">San Michele Mondovì</option>').selectpicker('refresh');
$('select').trigger('change');

	// imposta il flag quando si modifica un campo articolo
	$('.form-article') .on('change',function(event) {
		$('#submitArtFormID').prop('class','btn btn-danger btn-sm');
		$('#article_editedID').val(1);
	})

	checkNumberOrder();
	
	$('#dateinsDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defDateins,
		format: 'L',
		icons: {
			time: 'fas fa-clock',
            date: 'fas fa-calendar',
        	up: 'fas fa-chevron-up',
       		down: 'fas fa-chevron-down',
       		previous: 'fas fa-chevron-left',
    		next: 'fas fa-chevron-right',
     		today: 'fas fa-check',
      		clear: 'fas fa-trash',
         	close: 'fas fa-times'
       	}
	});
	
}); // end document


$('#numberID') .on('change',function(event) {
	checkNumberOrder();
})


function getNumberOrder() {
	var number_thirdparty_id = $('#number_thirdparty_idID').html();
	var number_year = $('#number_yearID').html();

	$.ajax({
			url: siteUrl+coreRequestAction+'/getNumberOrderAjaxSr',
			async: "false",
			cache: "false",
			type: "POST",
			data: {'number_thirdparty_id':number_thirdparty_id,'number_year':number_year},
	})
	.done(function(data) {
		$('#numberID').val(data);
		checkNumberOrder();	
	})		
	.fail(function() {
		alert("Ajax failed to fetch data article for module");
	})	
}
	
function checkNumberOrder() {
	var number_year = $('#number_yearID').html();
	var number = $('#numberID').val();
	var id = $('#idID').val();		

	$.ajax({
			url: siteUrl+coreRequestAction+'/checkNumberOrderAjaxSr',
			async: "false",
			cache: "false",
			type: "POST",
			data: {'number_year':number_year,'number':number,'id':id},
	})
	.done(function(data) {
		if (data == 0) {
			var message_check_number = '<p class="text-success">' + messages['numero ordine valido'] + '</p>';		
			var result_check_number = 0;
		} else {
			var message_check_number = '<p class="text-danger">' + messages['numero ordine esiste'] + '</p>';
			var result_check_number = 1;
		}		
		$('#message_check_numberID').html(message_check_number);
		$('#result_check_numberID').val(result_check_number);	
	})		
	.fail(function() {
		alert("Ajax failed to fetch data of invoice number check");
	})	
}

$('#thirdparty_idID').on('change',function(event) {
	var id = $('#thirdparty_idID').val();
	if (id > 0) {
		$.ajax({
			url: siteUrl+'ajax/getJsonThirdPartyFromDbId.php',
			async: "false",
			cache: "false",
			type: "POST",
			data: {'id':id},
			dataType: 'json'
		})
		.done(function(data) {

			$('#thirdparty_ragione_socialeID').val(data.ragione_sociale);
			$('#thirdparty_nameID').val(data.name);
			$('#thirdparty_surnameID').val(data.surname);
			$('#thirdparty_streetID').val(data.street);
			$('#thirdparty_comuneID').val(data.city);
			$('#thirdparty_zip_codeID').val(data.zip_code);
			$('#thirdparty_provinciaID').val(data.provincia);
			$('#thirdparty_nationID').val(data.nation);
			$('#thirdparty_emailID').val(data.email);
			$('#thirdparty_telephoneID').val(data.telephone);
			$('#thirdparty_faxID').val(data.fax);
			$('#thirdparty_partita_ivaID').val(data.partita_iva);
			$('#thirdparty_codice_fiscaleID').val(data.codice_fiscale);
			$('#thirdparty_pecID').val(data.pec);
			$('#thirdparty_sidID').val(data.sid);			
			getNumberOrder();			
		})		
		.fail(function() {
			alert("Ajax failed to fetch data article for module");
		})	
	}
}); // end function

	
$('#art_price_unityID').on('keyup',function(event) {
	refreshValuesFromPrice();
	}); // end function
	
$('#art_quantityID').on('keyup',function(event) {
	refreshValuesFromQuantity();
	}); // end function

$('#art_totalID').on('keyup',function(event) {
	refreshValuesTax();
	}); // end function
	
$('#art_taxID').on('keyup',function(event) {
	refreshValuesFromTax();
	}); // end function
	
$('#art_price_unityID').on('change',function(event) {
	$price_unity = $('#art_price_unityID').val();
	$price_unity = parseFloat($price_unity);
	$('#art_price_unityID').val($price_unity.toFixed(2));
	}); // end function
	
function refreshValuesFromPrice() {	
	$price_unity = $('#art_price_unityID').val();
	$quantity = $('#art_quantityID').val();
	$tax = $('#art_taxID').val();
	if ($price_unity == 'NaN') $price_unity = '0.00';
	if ($quantity == 'NaN') $quantity = '1';
	if ($tax == 'NaN') $tax ='0';
	$price_unity = parseFloat($price_unity);
	$tax = parseInt($tax);
	$quantity = parseInt($quantity);	
	$price_total = $price_unity * $quantity;
	$price_tax = ($price_total * $tax) / 100;
	$total = $price_total + $price_tax;
	
	$('#art_price_totalID').val($price_total.toFixed(2));
	$('#art_price_taxID').val($price_tax.toFixed(2));
	$('#art_totalID').val($total.toFixed(2));
	} // end func
	
function refreshValuesFromQuantity() {	
	$price_unity = $('#art_price_unityID').val();
	$quantity = $('#art_quantityID').val();
	$tax = $('#art_taxID').val();
	if ($price_unity == 'NaN') $price_unity = '0.00';
	if ($quantity == 'NaN') $quantity = '1';
	if ($tax == 'NaN') $tax ='0';
	$price_unity = parseFloat($price_unity);
	$tax = parseInt($tax);
	$quantity = parseInt($quantity);
	$price_total = $price_unity * $quantity;
	$price_tax = ($price_total * $tax) / 100;	
	$total = $price_total + $price_tax;
	$('#art_price_totalID').val($price_total.toFixed(2));
	$('#art_price_taxID').val($price_tax.toFixed(2));
	$('#art_totalID').val($total.toFixed(2));
	} // end func
	
function refreshValuesFromTax() {
	$price_total = $('#art_price_totalID').val();
	if ($price_total == 'NaN') $price_total = '0.00';
	$price_total = parseFloat($price_total);
	$tax = $('#art_taxID').val();
	if ($tax == 'NaN') $tax = '0';
	$tax = parseInt($tax);		
	$price_tax = ($price_total * $tax) / 100;
	$total = parseFloat($price_total + $price_tax);	
	$('#art_price_taxID').val($price_tax.toFixed(2));
	$('#art_totalID').val($total.toFixed(2));
	}
	
$('.submittheform').click(function () {
	
	if ($('#result_check_numberID').val() == 1) {
		$('.nav a[href="#datibase"]').tab('show');
		return false;
	}
	
	if ($('#article_editedID').val() == 1) {
		bootbox.alert(messages['ci sono state modifiche negli articoli']);
		return false;
	}
	$('input:invalid').each(function () {
	
		// Find the tab-pane that this element is inside, and get the id
		var $closest = $(this).closest('.tab-pane');
		var id = $closest.attr('id');
		// Find the link that corresponds to the pane and have it show
		$('.nav a[href="#' + id + '"]').tab('show');
		// Only want to do it once
		return false;
	});
});

$('.modifyArtInvSal').on('click',function(event) {
	var $id = $(this).data("id");	
	//alert($id);
	if ($id > 0) {
		$.ajax({
			url: siteUrl+coreRequestAction+'/getArticleOrderAjaxSr',
			async: "true",
			cache: "false",
			type: "POST",
			data: {'id':$id},
			dataType: 'json'
			})
		.done(function(data) {				
			$('#id_articleID').val($id);
			$('#art_contentID').val(data.content);
			$('#art_quantityID').val(data.quantity);
			$('#art_price_unityID').val(data.price_unity);
			$('#art_taxID').val(data.tax);
			$('#art_price_totalID').val(data.price_total);
			$('#art_price_taxID').val(data.price_tax);	
			var $total = parseFloat(data.price_total) + parseFloat(data.price_tax);	
			$('#totalID').val($total.toFixed(2).replace('.',','));										
			
			var $action = 'mod';
			$('#artFormModeID').prop('value',$action);
			$('#articlePanelID').prop('class','panel panel-warning');
			$('#articlePanelTitleID').html(messages['modifica articolo']);
			$('#submitArtFormID').html(messages['modifica articolo']);
			$('#submitArtFormID').prop('class','btn btn-warning btn-sm');
			})
		.fail(function() {
			alert("Ajax failed to fetch data article for module");
			})
 		}		
	}); // end function
		
		
$('#resetArtFormID').on('click',function(event) {
	$('#id_articleID').val(0);
	$('#art_contentID').val('');
	$('#art_quantityID').val('1');
	$('#art_price_unityID').val('0.00');
	$('#art_taxID').val(0);
	$('#art_price_totalID').val('0.00');
	$('#price_taxID').val('0.00');	
	var $action = 'ins';
	$('#artFormModeID').prop('value',$action);
	$('#articlePanelID').prop('class','panel panel-info');
	$('#articlePanelTitleID').html(messages['aggiungi articolo']);
	$('#submitArtFormID').html(messages['aggiungi articolo']);
	$('#submitArtFormID').prop('class','btn btn-info btn-sm');
	$('#article_editedID').val(0);
}); // end function