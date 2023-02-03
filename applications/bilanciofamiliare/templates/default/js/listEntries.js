/* bilanciofamiliare/listEntries.js v.1.0.0. 26/05/2019 */
var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		//stateSave: true,
		order: [
			[ 0, "desc" ]
			],
		columns: [
			{"data":"dateinslocal","targets":1},
			{"data":"entry","targets":2,"className":"text-right"},
			{"data":"description","targets":3},
			{"data":"actions","targets":4,"orderable":false,"className":"text-right"}
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
		 	url : siteUrl+coreRequestAction+"/listAjaxEntr",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.		
				
				$('#totali_entrate').html(json.totali_entrate);	
				$('#totali_entrate_ultimo_anno').html(json.totali_entrate_ultimo_anno);	
				$('#totali_entrate_anno_precedente').html(json.totali_entrate_anno_precedente);
				$('#totali_entrate_anno_corrente').html(json.totali_entrate_anno_corrente);
				$('#totali_entrate_tabella').html(json.totali_entrate_tabella);
				return json.data;
			}
      },
		initComplete:function( settings, json){
			alertDelete();
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