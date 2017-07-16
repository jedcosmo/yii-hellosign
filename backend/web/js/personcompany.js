jQuery(document).ready(function(){
								
		//	set autofous to form first field
		$('#gmp-company-form:first *:input[type!=hidden]:first').focus();
		
		var edit_id = $('#gmp_company_id_pk').val();
		jQuery.validator.addMethod("gmpcompanyname", function (param, element) {
				if(param.length > 0)
				  	return param.match(/^([a-z]|[\w._-\s]{6,16})$/i);
				else
					return true;
  		}, "Only alphabets,digits,period,hypen & underscore is allowed");
		
		
		jQuery.validator.addMethod("gmpzipcode", function (param, element) {
				if(param.length > 0)
				  return param.match(/^[a-zA-Z0-9\s]+$/);
				else
					return true;
  		}, "Only alphabets,digits & space are allowed");
		
		jQuery.validator.addMethod("gmpalphanumericwithspace", function (param, element) {
				if(param.length > 0)
				  return param.match(/^(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '-@.&,\s~?!]{2,}$/i);//param.match(/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '-@.&,\s~?!]{2,}$/i);
				else
					return true;
  		}, "You can use alphabets,digits,space and special characters -.,/&");
		 
		// validate add person form on keyup and submit
		jQuery("#gmp-company-form").validate({
			errorElement:'div',
			rules: {
				gmp_company_name : { 
									required: true,
									maxlength: 100,
									gmpalphanumericwithspace: true,
									remote: { 
										url : SITEROOT + "personcompany/personcompany/companyunique", 
										data: {'action':'check_duplicate_company','edit_id': edit_id},
										 type:"post"
										//async:false
									}
								 },
				gmp_address1 : { 
								required: true, 
								maxlength: 120,
							  },
				gmp_address2 : { 
								maxlength: 120,
							  },
				gmp_country_id_fk : { required: true },
				gmp_state_id_fk : { required: true },
				gmp_city_id_fk : { required: true},
				gmp_pobox : {
								minlength: 2,
								maxlength: 20,
								gmpzipcode:true,
							},
				gmp_zip_pincode : {
								maxlength: 20,
								gmpzipcode:true,
							},
			},
			messages:{
				gmp_company_name:{required: "Please enter company name",
									remote: "Company name already exists."
				},
				gmp_address1 : { required: "Please enter address1",
								maxlength: "Please enter less than 120 characters.",
							},
				gmp_address2 : { maxlength: "Please enter less than 120 characters." },
				gmp_country_id_fk : { required: "Please select country" },
				gmp_state_id_fk : { required: "Please select state" },
				gmp_city_id_fk : { required: "Please select city"},			
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		

		jQuery("#msg").fadeOut(5000);
	});
