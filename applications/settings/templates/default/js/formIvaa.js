/* estimates/formItem.js v.1.0.0. 11/07/2018 */
var requestSent = false;
$(document).ready(function() {	
	
	$('#dateinsDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defDateins,
		format: 'L'
		});

	$('#datescaDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defDatesca,
		format: 'L'
		});
	
	}); // end document
	
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

$('.modifyArtItem').on('click',function(event) {
		var $id = $(this).data("id");	
		//alert($id);
		if ($id > 0) {
			$.ajax({
				url: siteUrl+coreRequestAction+'/getArticleAjaxItem',
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
				$('#submitArtFormID').html(messages['modifica']);
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
	$('#articlePanelID').prop('class','panel panel-primary');
	$('#articlePanelTitleID').html(messages['inserisci articolo']);
	$('#submitArtFormID').html(messages['aggiungi']);
	$('#submitArtFormID').prop('class','btn btn-primary btn-sm');
	}); // end function