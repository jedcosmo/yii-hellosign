jQuery(document).ready(function(){
		var edit_id = $('#f_id_pk').val();
		var mpr_defination_id_fk = $('#mpr_defination_id_fk').val();
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
        	return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
		},'Please enter alphanumeric characters.');
		
		
		jQuery.validator.addMethod("percentVal", function(value, element) {
			if(value!='')
			{
				var peramt = this.optional(element) || /^[0-9 .]+$/.test(value);
				if(peramt==true)
				{
					if(value<=100)
						return true;
					else
						return false;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		},'Please enter valid percentage');
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-formulation-form").validate({
			errorElement:'div',
			rules: {
				material_name : { 
									maxlength:100,
									remote: { 
										url : SITEROOT + "formulation/formulation/formulationunique", 
										data: {'action':'materialName','edit_id': edit_id,'mpr_defination_id_fk':mpr_defination_id_fk},
										type:"post", 
									}
								},
				material_part : {
									maxlength:100,
									alphanumeric:true,
									remote: { 
										url : SITEROOT + "formulation/formulation/formulationunique", 
										data: {'action':'materialPart','edit_id': edit_id,'mpr_defination_id_fk':mpr_defination_id_fk},
										type:"post", 
									}
								},
				formulation_percentage: { percentVal: true, },
				
			},
			messages:{
				material_name : { 
						remote: "Formulation of this ingredient already exists for this MPR Definition"
						},
				material_part : { 
						remote: "Formulation of this Part# already exists for this MPR Definition"
						},
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});

		jQuery("#msg").fadeOut(5000);
		
		
		jQuery('#gmp-formulation-form').on('submit', function(){
			var material_name = $('#material_name').val();
			var material_part = $('#material_part').val();
			var formulation_percentage = $('#formulation_percentage').val();
			if(material_name!='' || material_part!='' || formulation_percentage!='')
			{
				$('#formulation_sub_error').html('');
				$('#formulation_error').hide();
				return true;
			}
			else
			{
				$('#formulation_sub_error').html('Please enter value for alteast anyone field');
				$('#formulation_error').show();
				return false;
			}
		});
		
		
	});
