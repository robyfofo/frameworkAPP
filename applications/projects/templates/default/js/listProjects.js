/* projects/items-list.js v.1.0.0. 28/02/2017 */
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
				popup();
				modal();
				alertDelete();
				return json.data;
            }       
		 	},	
		columns: [
			{data: "id"},
			{data: "title"},
			{data: "status"},
			{data: "completato"},
			{data: "times"},
			{data: "opts"},
			{data: "actions"}
			],
		columnDefs: [
			{orderable: false, "targets": 4},
			{orderable: false, "targets": 5},
    		{orderable: false, "targets": 6},
    		{className: "actions",targets: 6},
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

function popup() {
	$('.popoverInfo').popover({
		container: 'body'
		});
	};

function modal() {		
	$("#myModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-body").load(link.attr("href"));
		});
	};