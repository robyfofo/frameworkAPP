/* admin/core/password.js v.7.00. 04/02/2022 */
$(document).ready(function() {
	
	/* controllo password */	
	$('#passwordCKID').change(function(){
		var pass = $('#passwordID').val();
		var passCK = $('#passwordCKID').val();
		if(pass !== passCK) {
			bootbox.alert(messages['Le due password non corrispondono!']);
		}
	});
	
});

setvalidatefields();

$('.submittheform').click(function (e) { 
    var elements = document.getElementsByClassName('input-no-validate');
    while(elements.length > 0){
        elements[0].classList.remove('input-no-validate');
    }
    validation = true;
    controlloTabHTML5();
    validateFieldByID(validatefields);
    if (validation == false) {
        return false;
    }
});

function setvalidatefields() {
	let x = 0;
	validatefields[x] = ['datibase','maxchar','passwordID','100'];
}