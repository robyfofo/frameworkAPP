/* app/invoices/formInvSal.js v.1.3.0. 29/09/2020 */
var requestSent = false;
$(document).ready(function() {	

	// imposta il flag quando si modifica un campo articolo
	$('.form-article') .on('change',function(event) {
		$('#submitArtFormID').prop('class','btn btn-danger btn-sm');
		$('#submitArtFormID').prop('class','btn btn-danger btn-sm');
		$('#article_editedID').val(1);
	})

	checkNumberInvoice();
	
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

	$('#datescaDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defDatesca,
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
	checkNumberInvoice();
})


function getNumberInvoice() {
	var number_customers_id = $('#number_customers_idID').html();
	var number_year = $('#number_yearID').html();

	$.ajax({
			url: siteUrl+coreRequestAction+'/getNumberInvoiceAjaxSr',
			async: "false",
			cache: "false",
			type: "POST",
			data: {'number_customers_id':number_customers_id,'number_year':number_year},
	})
	.done(function(data) {
		$('#numberID').val(data);
		checkNumberInvoice();	
	})		
	.fail(function() {
		alert("Ajax failed to fetch data article for module");
	})	
}
	
function checkNumberInvoice() {
	var number_customers_id = $('#number_customers_idID').html();
	var number_year = $('#number_yearID').html();
	var number = $('#numberID').val();
	var id = $('#idID').val();		

	$.ajax({
			url: siteUrl+coreRequestAction+'/checkNumberInvoiceAjaxSr',
			async: "false",
			cache: "false",
			type: "POST",
			data: {'number_customers_id':number_customers_id,'number_year':number_year,'number':number,'id':id},
	})
	.done(function(data) {
		if (data == 0) {
			var message_check_number = '<p class="text-success">' + messages['numero fattura valido'] + '</p>';		
			var result_check_number = 0;
		} else {
			var message_check_number = '<p class="text-danger">' + messages['numero fattura esiste'] + '</p>';
			var result_check_number = 1;
		}		
		$('#message_check_numberID').html(message_check_number);
		$('#result_check_numberID').val(result_check_number);	
	})		
	.fail(function() {
		alert("Ajax failed to fetch data of invoice number check");
	})	
}

$('#id_customerID').on('change',function(event) {
	var id = $('#id_customerID').val();
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

			$('#customer_ragione_socialeID').val(data.ragione_sociale);
			$('#customer_nameID').val(data.name);
			$('#customer_surnameID').val(data.surname);
			$('#customer_streetID').val(data.street);
			$('#customer_cityID').val(data.city);
			$('#customer_zip_codeID').val(data.zip_code);
			$('#customer_provinceID').val(data.provincia);
			$('#customer_stateID').val(data.nation);
			$('#customer_emailID').val(data.email);
			$('#customer_faxID').val(data.fax);
			$('#customer_partita_ivaID').val(data.partita_iva);
			$('#customer_codice_fiscaleID').val(data.codice_fiscale);
			$('#customer_pecID').val(data.pec);
			$('#customer_sidID').val(data.sid);
			
			if (data.stampa_quantita == '1') {
				$('#stampa_quantitaID').attr( "checked",true );
			} else {
				$('#stampa_quantitaID').attr( "checked",false );
			}
			
			if (data.stampa_unita == '1') {
				$('#stampa_unitaID').attr( "checked",true );
			} else {
				$('#stampa_unitaID').attr( "checked",false );
			}
			
			$('#number_customers_idID').html(data.id);
			
			getNumberInvoice();
			
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
			url: siteUrl+coreRequestAction+'/getArticleAjaxInvSal',
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