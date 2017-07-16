jQuery(document).ready(function(){
								
		//	set autofous to form first field
		$('#gmp-person-form:first *:input[type!=hidden]:first').focus();
		$('#gmp-personupdate-form:first *:input[type!=hidden]:first').focus();
		
		var edit_id = $('#gmp_person_id_pk').val();
		jQuery.validator.addMethod("gmpalphabets", function (param, element) {
				if(param.length > 0)
				  	return param.match(/^[a-zA-Z\s]+$/);
				else
					return true;
  		}, "Only alphabets & space is allowed");
		 
		jQuery.validator.addMethod("gmpalphanumericwithspace", function (param, element) {
				if(param.length > 0)
				  return param.match(/^[a-zA-Z0-9\s]+$/);
				else
					return true;
  		}, "Only alphabets,digits & space are allowed");
		 
		jQuery.validator.addMethod("gmpemail", function (param, element) {
				if(param.length > 0)
				  	return param.match(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i);
				else
					return true;
  		}, "Please enter valid email address");
		 
		jQuery.validator.addMethod("gmpusername", function (param, element) {
				if(param.length > 0)
				  	return param.match(/^([a-z]|[\w._-]{5,16})$/i);
				else
					return true;
  		}, "Only alphabets,digits,period,hypen & underscore is allowed"); 
		
		 
		jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
			  return this.optional(element) || phone_number.length > 5 &&
			  phone_number.match(/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/);
		}, "You can use numbers and +,-,() characters"); 
		 
		 
		jQuery.validator.addMethod("personCompany", function(value, element) {
			if($('#super_company_dropdown').is(':visible')==true)
			{
				if(value)
					return true;
				else
					return false;
			}
			else
				return true;
		},'Please select person company');
		 
		// validate add person form on keyup and submit
		jQuery("#gmp-person-form").validate({
			errorElement:'div',
			rules: {
				gmp_super_company_id_fk : {
									personCompany: true,
								},
				gmp_first_name : { 
									required: true,
									maxlength: 50,
									gmpalphabets: true,
								 },
				gmp_last_name : { 
									required: true,
									maxlength: 50,
									gmpalphabets: true,
								},
				gmp_phone : { 
								required: true,
								minlength: 6,
								maxlength: 20,
								phoneUS: true,
							},
				gmp_fax : { 
								minlength: 6,
								maxlength: 20,
								phoneUS: true,
							},
				gmp_address : { 
								required: true, 
								maxlength: 120,
							  },
				gmp_country_id_fk : { required: true },
				gmp_state_id_fk : { required: true },
				gmp_city_id_fk : { required: true,},
				gmp_pobox : {
								minlength: 2,
								maxlength: 20,
								gmpalphanumericwithspace:true,
							},
				gmp_zip_pincode : {
								maxlength: 20,
								gmpalphanumericwithspace:true,
							},
				gmp_emailid : { 
								required: true, 
								email: true,
								gmpemail: true,
								maxlength: 60,
								remote: { 
										url : SITEROOT + "companyadmins/companyadmins/emailunique", 
										data: {'action':'check_duplicate_email','edit_id': edit_id, 'gmp_super_company_id_fk': function(){return $('#gmp_super_company_id_fk').val();}},
										 type:"post",
										//async:false
									}
							},
				gmp_user_name_person : { 
										required: true,
										minlength: 5,
										maxlength: 16,
										gmpusername:true,
										remote: { 
										url : SITEROOT + "companyadmins/companyadmins/emailunique", 
										data: {'action':'check_duplicate_username','edit_id': edit_id, 'gmp_super_company_id_fk': function(){return $('#gmp_super_company_id_fk').val();}},
										 type:"post",
										//async:false
									}
									},
				gmp_password_person : { 
										required: true,
										minlength: 6,
										maxlength: 16,
									},
				gmp_role_id_fk : { required: true },
			},
			messages:{
				gmp_first_name:{required: "Please enter first name"},
				gmp_last_name:{	required: "Please enter last name"},
				gmp_phone : { required: "Please enter phone number" },
				gmp_address : { required: "Please enter address",
								maxlength: "Please enter less than 120 characters.",
							},
				gmp_country_id_fk : { required: "Please select country" },
				gmp_state_id_fk : { required: "Please select state" },
				gmp_city_id_fk : { required: "Please select city",
									remote: "Selected city doesn't match with the zipcode."
								},
				gmp_zip_pincode : { remote: "Zipcode doesn't belong to the selected city."},
				gmp_emailid : { required: "Please enter email",
								remote: "Emailid is already in use by another user."
							},
				gmp_user_name_person : { required: "Please enter username",
										 remote: "Username is already in use by another user."
										},
				gmp_password_person : { required: "Please enter password" },
				gmp_role_id_fk : { required: "Please select role" },
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		
		// validate update person form on keyup and submit
		jQuery("#gmp-personupdate-form").validate({
			errorElement:'div',
			rules: {
				gmp_super_company_id_fk : {
									personCompany: true,
								},
				gmp_first_name : { 
									required: true,
									maxlength: 50,
									gmpalphabets: true,
								 },
				gmp_last_name : { 
									required: true,
									maxlength: 50,
									gmpalphabets: true,
								},
				gmp_phone : { 
								required: true,
								minlength: 6,
								maxlength: 20,
								phoneUS: true,
							},
				gmp_fax : { 
								minlength: 6,
								maxlength: 20,
								phoneUS: true,
							},
				gmp_address : { 
								required: true, 
								maxlength: 120,
							  },
				gmp_country_id_fk : { required: true },
				gmp_state_id_fk : { required: true },
				gmp_city_id_fk : { required: true	},
				gmp_pobox : {
								minlength: 2,
								maxlength: 20,
								gmpalphanumericwithspace:true,
							},
				gmp_zip_pincode : {
								maxlength: 20,
								gmpalphanumericwithspace:true,
							},
				gmp_emailid : { 
								required: true, 
								email: true,
								gmpemail: true,
								maxlength: 60,
								remote: { 
										url : SITEROOT + "companyadmins/companyadmins/emailunique", 
										data: {'action':'check_duplicate_email','edit_id': edit_id, 'gmp_super_company_id_fk': function(){return $('#gmp_super_company_id_fk').val();}},
										 type:"post",
										//async:false
									}
							},
				gmp_user_name_person : { 
										required: true,
										minlength: 5,
										maxlength: 16,
										gmpusername:true,
										remote: { 
										url : SITEROOT + "companyadmins/companyadmins/emailunique", 
										data: {'action':'check_duplicate_username','edit_id': edit_id, 'gmp_super_company_id_fk': function(){return $('#gmp_super_company_id_fk').val();}},
										 type:"post",
										//async:false
									}
									},
				gmp_password_person : { 
										minlength: 6,
										maxlength: 16,
									},
				gmp_role_id_fk : { required: true },
			},
			messages:{
				gmp_first_name:{required: "Please enter first name"},
				gmp_last_name:{	required: "Please enter last name"},
				gmp_phone : { required: "Please enter phone number" },
				gmp_address : { required: "Please enter address",
								maxlength: "Please enter less than 120 characters.",
								},
				gmp_country_id_fk : { required: "Please select country" },
				gmp_state_id_fk : { required: "Please select state" },
				gmp_city_id_fk : { required: "Please select city",
									remote: "Selected city doesn't match with the zipcode."
								},
				gmp_zip_pincode : { remote: "Zipcode doesn't belong to the selected city."},
				gmp_emailid : { required: "Please enter email",
								remote: "Emailid is already in use by another user."				
							},
				gmp_user_name_person : { required: "Please enter username",
										 remote: "Username is already in use by another user."
										},
				gmp_role_id_fk : { required: "Please select role" },
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			},
			/*submitHandler: function(form) { 
				$('#cmpadminsubbtn').attr('disabled', 'disabled');
				$("#gmp-personupdate-form").submit();
			},*/
		});
		
		
		jQuery("#msg").fadeOut(5000);
	});
