jQuery(document).ready(function(){
		var edit_id = $('#bom_id_pk').val();
		var mpr_defination_id_fk = $('#mpr_defination_id_fk').val();
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
        	return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
		},'Please enter alphanumeric characters.');
		
		
		jQuery.validator.addMethod("batchsize", function(value, element) {
        	return this.optional(element) || /^[0-9 .]+$/.test(value);
		},'Please enter valid numeric value');
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-billofmaterials-form").validate({
			errorElement:'div',
			rules: {
				material_name : { 
									required: true,
									maxlength:100,
									remote: { 
										url : SITEROOT + "billofmaterial/billofmaterial/bomunique", 
										data: {'action':'materialName','edit_id': edit_id,'mpr_defination_id_fk':mpr_defination_id_fk},
										type:"post",
										//async:false 
									}
								},
				material_id : {
								maxlength:100,
								remote: { 
										url : SITEROOT + "billofmaterial/billofmaterial/bomunique", 
										data: {'action':'materialId','edit_id': edit_id,'mpr_defination_id_fk':mpr_defination_id_fk},
										type:"post",
										//async:false 
									}
								
								},
				qty_branch : { 
								required: true,
								batchsize: true,
							},
				qb_unit_id_fk : { required: true, },
				composition : { required: true, },
				com_unit_id_fk : { required: true, },
				price_per_unit: { batchsize: true, },
				maximum_qty: { batchsize:true,  },
				CAS_Number: { maxlength:20 },
				Control_Number: { maxlength:20 },
			},
			messages:{
				material_name : { required: "Please enter material name",
									remote: "Material name already exists for this MPR Definition",
								},
				material_id : {	remote: "Material ID already exists for this MPR Definition"	},
				qty_branch : { 
								required: "Please enter unit size",
							},
				qb_unit_id_fk : { required: "Please select unit", },
				composition : { required: "Please enter composition", },
				com_unit_id_fk : { required: "Please select unit", },
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});

		jQuery("#msg").fadeOut(5000);
	});
