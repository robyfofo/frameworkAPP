/* invoices/listInvPur.js v.1.2.0. 02/12/2019 */

var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable({
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		paging: true,
		ajax: {
		 	type : "GET",
		 	url : siteUrl+coreRequestAction+"/listAjaxInvPur",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.
				alertDelete();
				return json.data;
            }       
		 },	
		order: [
			[ 1, "desc" ]
			],
		columns: [			
			{ "data":"id","targets":0},
			{ "data":"dateinslocal","targets":1},
			{ "data":"datescalocal","targets":2},
			{ "data":"customer","targets":3},
			{ "data":"number","targets":4},
			{ "data":"total","targets":5,"className":"text-right"},
			{ "data":"actions","targets":6,"orderable":false,"className":"actions"}
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