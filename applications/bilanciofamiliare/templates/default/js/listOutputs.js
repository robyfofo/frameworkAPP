/* bilanciofamiliare/listOutputs.js v.1.0.0. 11/10/2019 */
var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		//stateSave: true,
		order: [
			[ 1, "desc" ]
			],
		columns: [			
			{"data":"id","targets":0},
			{"data":"dateinslocal","targets":1},
			{"data":"output","targets":2,"className":"text-right"},
			{"data":"description","targets":3},
			{"data":"actions","targets":4,"orderable":false,"className":"text-right"}
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
		 	url : siteUrl+coreRequestAction+"/listAjaxOutp",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.	
				$('#totali_uscite').html(json.totali_uscite);	
				$('#totali_uscite_ultimo_anno').html(json.totali_uscite_ultimo_anno);	
				$('#totali_uscite_anno_precedente').html(json.totali_uscite_anno_precedente);
				$('#totali_uscite_anno_corrente').html(json.totali_uscite_anno_corrente);
				$('#totali_uscite_tabella').html(json.totali_uscite_tabella);
			
				return json.data;
			}
      },
		initComplete:function( settings, json){
			alertDelete();
		},
		drawCallback: function(settings) {
			alertDelete();
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