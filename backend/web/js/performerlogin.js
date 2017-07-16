jQuery(document).ready(function(){ 	
				
	showGlobalLoadingMessage('end');
	
	$('#myModal').on('shown.bs.modal', function () {
		$('#username').focus();
	});
	
	// validate login form on keyup and submit
	jQuery("#login_form").validate({
		errorElement:'div',
		rules: {
			username : {required: true},
			password : { required: true}
		},
		messages:{
			username : {required: "Please enter username/email"},
			password : { required: "Please enter password"}
		},
		// set this class to error-labels to indicate valid fields
		success: function(label){
			// set &nbsp; as text for IE
			label.hide();
		}
	});
	
	jQuery("#msgS").fadeOut(5000);
	jQuery("#msgE").fadeOut(5000);
	
});

function closethisPopup()
{
	$('#myModal').modal('hide');
}
