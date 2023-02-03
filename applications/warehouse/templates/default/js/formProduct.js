/* admin/warehouse/formProduct.js v.4.5.1. 29/07/2020 */
$(document).ready(function(){
	refreshValuesFromPrice();

	$('#price_unityID').on('keyup',function(event) {
		refreshValuesFromPrice();
	});

	$('#taxID').on('keyup',function(event) {
		refreshValuesFromTax();
	});
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
});

function refreshValuesFromPrice() {
	let price_unity = $('#price_unityID').val();
	let tax = $('#taxID').val();
	if (price_unity == '') price_unity = '0.00';
	if (tax == '') tax ='22';
	price_unity = parseFloat(price_unity);
	tax = parseInt(tax);
	price_tax = (price_unity * tax) / 100;
	price_total = price_unity + price_tax;
	$('#price_taxID').html(price_tax.toFixed(2));
	$('#price_totalID').html(price_total.toFixed(2));
}

function refreshValuesFromTax() {
	let price_unity = $('#price_unityID').val();
	let tax = $('#taxID').val();
	if (price_unity == '') price_unity = '0.00';
	if (tax == '') tax ='22';
	price_unity = parseFloat(price_unity);
	tax = parseInt(tax);
	price_tax = (price_unity * tax) / 100;
	price_total = price_unity + price_tax;
	$('#price_taxID').html(price_tax.toFixed(2));
	$('#price_totalID').html(price_total.toFixed(2));
}
