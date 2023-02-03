/* settings/listIvaa.js v.1.0.0. 11/09/2018 */
var requestSent = false;

$(document).ready(function() {
	$('#listDataID').DataTable( {
		responsive: true,
		processing: true,
		serverSide: true,
		stateSave: true,
		order: [
			[1,"desc"]
		],
		columns: [		
			{ "data":"id","targets":0},
			{ "data":"note","targets":1},
			{ "data":"amount","targets":2,"className":"text-right"},
			{ "data":"actions","targets":3,"orderable":false,"className":"text-right"}
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
		 	url : siteUrl+coreRequestAction+"/listAjaxIvaa",
		 	async: "true",
			cache: "false",
			dataSrc: function ( json ) {
				alertDelete();
				return json.data;
            }       
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