/* thirdparty/listItem.js v.1.0.0. 05/07/2018  */
var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		ajax: {
		 	type: "GET",
		 	url: siteUrl+coreRequestAction+"/listAjaxItem",
		 	async: "true",
			cache: "false",
			dataSrc: function (json) {
				alertDelete();
				return json.data;
			}       
		},	
		columns: [
			{"data":"id","targets":0,"className":"idcol"},
			{data: "category"},
			{data: "type"},
			{data: "ragione_sociale"},			
			{data: "email"},
			{ "data":"actions","targets":4,"orderable":false,"className":"actions"}
		],
		columnDefs: [
    		{orderable: false, "targets": 5},
    		{className: "text-right",targets: 5},
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