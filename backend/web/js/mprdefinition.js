jQuery(document).ready(function(){

		//	set autofous to form first field
		$('#gmp-mprdefinition-form:first *:input[type!=hidden]:first').focus();
								
		var edit_id = $('#mpr_defination_id_pk').val();
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
        	return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
		},'Please enter alphanumeric characters.');
		
		
		jQuery.validator.addMethod("batchsize", function(value, element) {
        	return this.optional(element) || /^[0-9 .]+$/.test(value);
		},'Please enter valid numeric value');
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-mprdefinition-form").validate({
			errorElement:'div',
			rules: {
				product_code : { required: true, },
				author : { required: true, },
				formulation_id : { 
									required: true, 
									alphanumeric: true,
								},
				product_strength : { required: true,
									},
				batch_size : { 
								required: true,
								batchsize: true,
							},
				MPR_unit_id : { required: true, },
				theoritical_yield : { required: true, },
				gmp_company : { required: true, },
				purpose : { required: true, },
				scope : { required: true,},
			},
			messages:{
				product_code : { required: "Please select product code", },
				author : { required: "Please select author", },
				formulation_id : { 
									required: "Please enter formulation ID", 
								},
				product_strength : { required: "Please enter product strength", },
				batch_size : { 
								required: "Please enter batch size",
							},
				MPR_unit_id : { required: "Please select unit", },
				theoritical_yield : { required: "Please enter theoritical yield", },
				gmp_company : { required: "Please select company", },
				purpose : { required: "Please enter purpose", },
				scope : { required: "Please enter scope",},
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});

		jQuery("#msg").fadeOut(5000);
	});
