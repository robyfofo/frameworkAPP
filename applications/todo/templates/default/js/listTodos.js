/* app/todo/listTodos.js v.1.3.0. 24/09/2020 */
var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		paging: true,
		ajax: {
		 	type : "GET",
		 	url : siteUrl+coreRequestAction+"/listAjaxItem",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.
				alertDelete();
				return json.data;
            }       
		},	
		order: [
			[2,"asc"]
		],

		columns: [			
			{ "data":"id","targets":0,"className":"idcol"},
			{ "data":"project","targets":1},
			{ "data":"title","targets":2},
			{ "data":"statuslabel","targets":3},
			{ "data":"actions","targets":4,"orderable":false,"className":"actions"}
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
      drawCallback: function () {
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