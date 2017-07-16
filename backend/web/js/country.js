jQuery(document).ready(function(){
								
	//	set autofous to form first field
	$('#gmp-country-form:first *:input[type!=hidden]:first').focus();
								
	var edit_id = $('#gmp_country_id_pk').val();
	
	// validate add person form on keyup and submit
	jQuery("#gmp-country-form").validate({
		errorElement:'div',
		rules: {
			gmp_country_name : { 
								required: true,
								maxlength: 50,
								remote: { 
									url : SITEROOT + "country/country/countryunique", 
									data : {"action" : "check_duplicate_country",'edit_id': edit_id},
									type : "post"
									//async:false
								}
							 },
			
		},
		messages:{
			gmp_country_name : { required: "Please enter country name.",
								remote: "Country name already exists",
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
