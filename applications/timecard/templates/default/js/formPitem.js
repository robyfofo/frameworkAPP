/* timecard/formPitem.js v.1.2.0. 23/12/2019 */
$(document).ready(function() {
		
	$('#starttimeID').datetimepicker({
		locale: user_lang,
		format: 'LT',
 		defaultDate:  moment(defaultTimeIni, 'LT'),
		allowInputToggle: true,
		stepping: '15',
		disabledHours: ['0', '1', '2', '3', '4', '5', '22', '23'],
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
	$('#endtimeID').datetimepicker({
		locale: user_lang,
		format: 'LT',
 		defaultDate:  moment(defaultTimeEnd, 'LT'),
		allowInputToggle: true,
		stepping: '15',
		disabledHours: ['0', '1', '2', '3', '4', '5', '22', '23'],
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
	
	$("#starttimeID").on("dp.change", function (e) {
		var d = new Date(e.date);
		d.setHours(d.getHours()+1);
		moment.locale(user_lang);
		t = moment(d).format("LT");
		$('#endtimeID').val(t);
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