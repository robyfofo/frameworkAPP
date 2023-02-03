/* bilanciofamiliare/listEntries.js v.1.0.1. 07/11/2019 */
var requestSent = false;

$(document).ready(function() {
	getTotals();
	
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		//stateSave: true,
		order: [
			[ 0, "desc" ]
			],
		columns: [			
			{"data":"dateinslocal","targets":0},
			{"data":"entry","targets":1,"className":"text-right"},
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
		 	url : siteUrl+coreRequestAction+"/listAjaxItem",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				//Make your callback here.				
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


function getTotals() {
	
	$.getJSON( siteUrl+coreRequestAction+'/getAjaxTotalItem' , function(dataJson) {
	
		$('#totali_entrate_dodici_mesi').html('&euro; '+dataJson.totali_entrate_dodici_mesi);
		$('#totali_uscite_dodici_mesi').html('&euro; '+dataJson.totali_uscite_dodici_mesi);
		$('#totali_bilancio_dodici_mesi').html('&euro; '+dataJson.totali_bilancio_dodici_mesi);
		
		$('#medie_entrate_dodici_mesi').html('&euro; '+dataJson.medie_entrate_dodici_mesi);
		$('#medie_uscite_dodici_mesi').html('&euro; '+dataJson.medie_uscite_dodici_mesi);
		$('#medie_bilancio_dodici_mesi').html('&euro; '+dataJson.medie_bilancio_dodici_mesi);
		
		$('#totali_entrate_mesepre').html('&euro; '+dataJson.totali_entrate_mesepre);
		$('#totali_uscite_mesepre').html('&euro; '+dataJson.totali_uscite_mesepre);
		$('#totali_bilancio_mesepre').html('&euro; '+dataJson.totali_bilancio_mesepre);
		
		$('#totali_entrate_mese').html('&euro; '+dataJson.totali_entrate_mese);
		$('#totali_uscite_mese').html('&euro; '+dataJson.totali_uscite_mese);
		$('#totali_bilancio_mese').html('&euro; '+dataJson.totali_bilancio_mese);
		
	})
	.done(function() {
   	 //console.log( "second success" );
  	})
	.fail(function() {
		//console.log( "error" );
  	})
	.always(function() {
		//console.log( "complete" );
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