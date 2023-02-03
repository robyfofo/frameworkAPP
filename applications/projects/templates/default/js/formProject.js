/* projects/formItem.js v.1.0.1. 05/07/2017 */

$(document).ready(function() {	
	listPprojecttimecards(siteUrl+coreRequestAction+"/listTimecardsAjax/"+idproject);	
});
		
$('.submittheform').click(function () {
	$('input:invalid').each(function () {
		// Find the tab-pane that this element is inside, and get the id
		var $closest = $(this).closest('.tab-pane');
		var id = $closest.attr('id');
		// Find the link that corresponds to the pane and have it show
		$('.nav a[href="#' + id + '"]').tab('show');
		// Only want to do it once
		return false;
	});
});

function listPprojecttimecards(url) {
	$("#listProjectTimecardsID").load(url);
}

$('#listProjectTimecardsAllID').click(function () {
	listPprojecttimecards(siteUrl+coreRequestAction+"/listTimecardsAjax/"+idproject+"/All");	
});

$('#listProjectTimecardsCmID').click(function () {
	listPprojecttimecards(siteUrl+coreRequestAction+"/listTimecardsAjax/"+idproject+"/cm");	
});

$('#listProjectTimecardsPmID').click(function () {
	listPprojecttimecards(siteUrl+coreRequestAction+"/listTimecardsAjax/"+idproject+"/pm");	
});