/* timecard/items.js v.1.2.0. 12/08/2020 */

$(document).ready(function() {

	$('#esporta_timecardID').click(function(e) {
		e.preventDefault(e);
		let id_project = $('#id_projectID').val();
		let appdata = $('#appdataID').val()
		let momentObj = moment(appdata, 'DD/MM/YYYY');
		let appdataIso = momentObj.format('YYYY-MM-DD'); // 2016-07-15
		let location = siteUrl + coreRequestAction + '/timecardSpdf/' + appdataIso +'/' + id_project;
		window.location.replace(location);
	});

	$('.timedelconfirm').click(function(e) {
		e.preventDefault(e);
		var location = $(this).attr('href');
		bootbox.confirm("Sei sicuro?",function(confirmed) {
			if (confirmed) {
				window.location.replace(location);
			}
		});
	});
	
	$('#dataDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultdata,
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
		
	$('#data1DPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultdata1,
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
		
	$('#appdataDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultappdata,
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
	
	$('#startTimeID').datetimepicker({
		format: 'LT',
		locale: user_lang,
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

	
	
	
	$('#endTimeID').datetimepicker({
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

	$("#startTimeID").on("dp.change", function (e) {
		var d = new Date(e.date);
		d.setHours(d.getHours()+1);
		moment.locale(user_lang);
		t = moment(d).format("LT");
		$('#endTimeID').val(t);
	});	

	$('#starttime1ID').datetimepicker({
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

 	$('.tooltip-proj').tooltip({
		selector: "[data-toggle=tooltip]",
		container: "body"
	});

	$('#applicationForm2').on('submit', function (e) {
	
		let result = false;
		var id_project = [];
    	id_project = $('#progettoID').val();
     	    	
    	if (id_project.length > 0) {  		
    		result = false;
    	} else {
    		bootbox.alert(messages['Devi selezionare un progetto']);
    	}
    	
    	
    	let data = $('#dataID').val();
    	let startTime = $('#startTimeID').val();
    	let endTime = $('#endTimeID').val();
    	
    	let id_timecard = 0;
    	if ($('#id_timecardID').length > 0) {  		
    		id_timecard = $('#id_timecardID').val();
    	}
    	
    	//console.log('id_project: ' + id_project);
    	//console.log('data: ' + data);
    	//console.log('startTime: ' + startTime);
    	//console.log('endTime: ' + endTime);
    	
    	$.ajax({
			url: siteUrl + coreRequestAction + '/ajaxCheckTimeInterval',
			async: false,
			cache: false,
			timeout: 20000,
			type: "POST",
			data: {'id_timecard':id_timecard, 'id_project':id_project, 'data':data, 'startTime': startTime, 'endTime': endTime},
			dataType: 'html'
		})
		.done(function(data) {
			console.log(data);
			if (data == 0) {
				result = true;
				console.log('form consentito');
			} else {
				bootbox.alert(messages['intervallo di tempo si sovrappone ad un altro inserito nella stessa data']);
			}
			
		})		
		.fail(function() {
			alert("Ajax failed to fetch data article for module");
		})	
    	
    	/*
    	if (result == true) {
    		console.log('form consentito');
    	} else {
    		console.log('form NON consentito');
    	}
    	*/
    	return result;
	});
		
});

