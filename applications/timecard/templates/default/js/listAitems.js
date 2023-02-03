/* timecard/listAitems.js v.1.3.0. 28/08/2020 */
var requestSent = false;

$(document).ready(function() {
	generateTable();

	manageDivsDate();

	$('.selectpicker').change(function(e) {
		e.preventDefault(e);
		manageDivsDate();
		$("#listDataID").DataTable().ajax.reload();
	});

	$("#datainiID").on("dp.change", function (e) {
		e.preventDefault(e);
		cambiodate();
	});

	$("#dataendID").on("dp.change", function (e) {
		e.preventDefault(e);
		cambiodate();
	});

	$('#datainiID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultDataini,
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

	$('#dataendID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultDataend,
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

function manageDivsDate() {
	if ($('#intervaldataID').val() == 'id') {
		 $('.date').show();
	} else {
		$('.date').hide();
	}
}

function cambiodate() {
	if ($('#intervaldataID').val() == 'id') {
		let dataini = $('#datainiID').val()
		let mdataini = moment(dataini, 'DD/MM/YYYY');
		let datainiIso = mdataini.format('YYYY-MM-DD'); // 2016-07-15

		let dataend = $('#dataendID').val();
		let mdataend = moment(dataend, 'DD/MM/YYYY');
		let dataendIso = mdataend.format('YYYY-MM-DD'); // 2016-07-15

		if (moment(dataendIso).isSameOrAfter(datainiIso) == true) {

			$("#listDataID").DataTable().ajax.reload();

		} else {
			bootbox.alert(messages['Intervallo tra le due date errato!']);
			return false;
		}
	}
}


function alertDelete() {
	$("#listDataID").on('click','.confirmdelete',function(e) {
		e.preventDefault(e);
		var location = $(this).attr('href');
		bootbox.confirm(messages['Sei sicuro?'],function(confirmed) {
			if(confirmed) {
				window.location.replace(location);
				}
			});
		});
	};

function exportFormat() {
	$("#listDataID").on('click','.export',function(e) {
		e.preventDefault(e);
		var table = $('#listDataID').DataTable();
		var filter = table.search();
		var location = siteUrl+coreRequestAction+'/exportXlsAite';
		if (filter != '') location = location +'/'+filter;
		window.location.replace(location);
	});
}


function generateTable() {
	let urlAjax = siteUrl+coreRequestAction+"/listAjaxAite";
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		"bDestroy": true,
		ajax: {
		 	type: "GET",
		 	url: urlAjax,
		 	async: "false",
			cache: "false",

			"data": function ( d ) {
             	d.id_project = $('#id_projectID').val();
				d.intervaldata = $('#intervaldataID').val();
				d.dataini = function() {
					let dataini = $('#datainiID').val()
					let momentObj = moment(dataini, 'DD/MM/YYYY');
					let datainiIso = momentObj.format('YYYY-MM-DD'); // 2016-07-15
					return datainiIso;
				};
				d.dataend  = function() {
					let dataend = $('#dataendID').val();
					let momentObj = moment(dataend, 'DD/MM/YYYY');
					let dataendIso = momentObj.format('YYYY-MM-DD'); // 2016-07-15
					return dataendIso;
				};
            },
			dataSrc: function (json) {
				alertDelete();
				exportFormat();
				return json.data;
            },
		},
		"drawCallback": function(settings) {
			 alertDelete();
			 exportFormat();
      	},
		order:
		[
			[ 4, "desc" ]
		],
		columns: [
			{data: "id"},
			{data: "id_user"},
			{data: "project"},
			{data: "content"},
			{data: "datains"},
			{data: "starttime"},
			{data: "endtime"},
			{data: "worktime"},
			{data: "actions"}
			],
		columnDefs: [
    		{orderable: false, "targets": 8},
    		{className: "text-right",targets: 8},
  			],
		language: {
			sSearch: LocalStrings['search'],
         	lengthMenu: LocalStrings['lengthMenu'],
         	zeroRecords: LocalStrings['zeroRecords'],
         	info: LocalStrings['datatableInfo'],
         	infoEmpty: LocalStrings['infoEmpty'],
         	infoFiltered: LocalStrings['infoFiltered'],
         	loadingRecords: LocalStrings['loadingRecords'],
    		processing:     LocalStrings['processing'],
         	paginate: {
        		first:      LocalStrings['paginate']['first'],
        		last:       LocalStrings['paginate']['last'],
        		next:       LocalStrings['paginate']['next'],
        		previous:   LocalStrings['paginate']['previous']
    		}
      	},
      	drawCallback: function () {
			$('#listDataID_paginate ul.pagination').addClass("pagination-sm");
      	}
	});

}
