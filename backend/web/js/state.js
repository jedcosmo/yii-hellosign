jQuery(document).ready(function(){

	//	set autofous to form first field
	$('#gmp-state-form:first *:input[type!=hidden]:first').focus();

	var edit_id = $('#gmp_state_id_pk').val();
	
	// validate add person form on keyup and submit
	jQuery("#gmp-state-form").validate({
		errorElement:'div',
		rules: {
			gmp_country_id_fk : { required: true },
			gmp_state_name : { 
								required: true,
								maxlength: 50,
								remote: { 
										url : SITEROOT + "state/state/stateunique", 
										data: {'action':'check_duplicate_state','edit_id': edit_id, 'gmp_country_id_fk': function(){return $('#gmp_country_id_fk').val();}},
										 type:"post",
										//async:false
									}
							 },
			
		},
		messages:{
			gmp_country_id_fk : { required: "Please select country" },
			gmp_state_name : { required: "Please enter state name.",
								remote: "State already exists",
							},
		
		},
		// set this class to error-labels to indicate valid fields
		success: function(label){
			// set &nbsp; as text for IE
			label.hide();
		}
	});
	jQuery("#msg").fadeOut(5000);
});
