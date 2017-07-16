jQuery(document).ready(function(){
		var edit_id = $('#mi_id_pk').val();
		var mpr_defination_id_fk = $('#mpr_defination_id_fk').val();
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
        	return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
		},'Please enter alphanumeric characters.');
		
		
		jQuery.validator.addMethod("batchsize", function(value, element) {
        	return this.optional(element) || /^[0-9 .]+$/.test(value);
		},'Please enter valid numeric value');
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-minstructions-form").validate({
			errorElement:'div',
			rules: {
				mi_step : { required: true,
							maxlength:100,
							remote: { 
										url : SITEROOT + "minstructions/minstructions/stepunique", 
										data: {'action':'check_duplicate_step','edit_id': edit_id, 'mpr_defination_id_fk':mpr_defination_id_fk},
										type:"post",
									}
						  },
				mi_action : { maxlength:500 },
				mi_range : { maxlength:100 },
				target : { maxlength:100 },
			},
			messages:{
				mi_step : { required: "Please enter step",
							remote: "Step already exists",
						 }
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});

		jQuery("#msg").fadeOut(5000);
	});
