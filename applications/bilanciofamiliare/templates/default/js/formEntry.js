/* bilanciofamiliare/formOutput.js v.1.2.0. 03/12/2019 */

$(document).ready(function() {		
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