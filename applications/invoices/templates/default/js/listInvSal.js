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
			{"data":"dateinslocal","targets":1},
			{"data":"datescalocal","targets":2},
			{"data":"pagata","targets":3,"orderable":false},
			{"data":"customer","targets":4},
			{"data":"invoice_number","targets":5},
			{"data":"note","targets":6},
			{"data":"total","targets":7,"className":"text-right"},
			{"data":"totaltaxes","targets":8,"className":"text-right"},
			{"data":"totalinvoice","targets":9,"className":"text-right"},
			{"data":"pdf","targets":10,"orderable":false,"className":"actions"},
			{"data":"actions","targets":11,"orderable":false,"className":"actions"}
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
      ajax: {
		 	type : "GET",
		 	url : siteUrl+coreRequestAction+"/listAjaxInvSal",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.
				alertDelete();
				alertSegnapagata();
				return json.data;
            }       
		 },
  		drawCallback: function(settings) {
			$('#listDataID_paginate ul.pagination').addClass("pagination-sm");
		}		 
	});	
});


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