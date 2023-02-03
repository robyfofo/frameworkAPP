/* invoices/listInvSal.js v.1.2.0. 01/07/2020 */
var requestSent = false;

$(document).ready(function() {

	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		order: [
			[ 1, "desc" ]
			],
		columns: [			
			{"data":"id","targets":0},
			{"data":"order_number","targets":1},
			{"data":"dateinslocal","targets":2},
			{"data":"note","targets":3},
			{"data":"total","targets":4,"className":"text-right"},
			{"data":"totaltaxes","targets":5,"className":"text-right"},
			{"data":"totalorder","targets":6,"className":"text-right"},
			{"data":"info","targets":7,"orderable":false,"className":"info"},
			{"data":"pdf","targets":8,"orderable":false,"className":"info"},
			{"data":"actions","targets":9,"orderable":false,"className":"actions"}
  			],
		language: {
			sSearch: lang['search'],
         	lengthMenu: lang['lengthMenu'],
         	zeroRecords: lang['zeroRecords'],
         	info: lang['datatableInfo'],
         	infoEmpty: lang['infoEmpty'],
         	infoFiltered: lang['infoFiltered'],
         	loadingRecords: lang['loadingRecords'],
    		processing:     lang['processing'],
         	paginate: {
       			first:      lang['paginate']['first'],
        		last:       lang['paginate']['last'],
        		next:       lang['paginate']['next'],
        		previous:   lang['paginate']['previous']
    		}
      	},
      ajax: {
		 	type : "GET",
		 	url : siteUrl+coreRequestAction+"/listAjaxOrders",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.
				modal();
				modal1();
				alertDelete();
				return json.data;
            }       
		 },
  		drawCallback: function(settings) {
			$('#listDataID_paginate ul.pagination').addClass("pagination-sm");
		}		 
	});	

});

function modal() {		
	$("#myModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-body").load(link.attr("href"));
	});
};

function modal1() {		
	$("#myModal1").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-body").load(link.attr("href"));
	});
};
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
	
function alertSegnapagata() {
	$("#listDataID").on('click','.segnapagata',function(e) {
		e.preventDefault(e);
		var location = $(this).attr('href');
		bootbox.confirm(messages['Sei sicuro?'],function(confirmed) {
			if(confirmed) {
				window.location.replace(location);
			}
		});
	});     
};
