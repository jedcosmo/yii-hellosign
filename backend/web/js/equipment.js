jQuery(document).ready(function(){
								
		//	set autofous to form first field
		$('#gmp-equipment-form:first *:input[type!=hidden]:first').focus();
		$('#gmp-equipmentupdate-form:first *:input[type!=hidden]:first').focus();
		
		$( "#caliberation_due_date" ).datepicker({ dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, minDate: '+0d'});
		$( "#preventive_m_due_date" ).datepicker({ dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, minDate: '+0d'});
		
		var edit_id = $('#gmp_equipment_id_pk').val();
	
		jQuery.validator.addMethod("gmpequipmentname", function (param, element) {
				if(param.length > 0)
				  return param.match(/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '()-@.&,\s~?!]{2,}$/i);
				else
					return true;
  		}, "You can use alphabets,digits,space and special characters -.,/()'&");
	
		jQuery.validator.addMethod("gmpequipmentcode", function (param, element) {
				if(param.length > 0)
				  return param.match(/^[a-zA-Z0-9 -.]+$/);
				else
					return true;
  		}, "You can use alphabets,digits & special characters .-");
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-equipment-form").validate({
			errorElement:'div',
			rules: {
				gmp_equipment_name : { 
									required: true,
									maxlength: 200,
									//gmpequipmentname:true,
									remote: { 
										url : SITEROOT + "equipment/equipment/equipmentunique", 
										data : {"action" : "check_duplicate_equipment",'edit_id': edit_id},
										type : "post"
										//async:false
									}
								 },
				gmp_equipment_model : { required: true,
										maxlength: 50,
										gmpequipmentcode:true,
								},
				gmp_equipment_serial:{
										required: true,
										maxlength: 50,
										gmpequipmentcode:true,
								},
				caliberation_due_date : { required: true },
				preventive_m_due_date : { required: true },
				/*gmp_equipment_document : { 
									required: true,
								},*/
			},
			messages:{
				gmp_equipment_name : { required: "Please enter equipment name.",
									   remote: "Equipment name already exists.",
									},
				gmp_equipment_model : { required: "Please enter model number."	},
				gmp_equipment_serial:{	required: "Please enter serial number." },
				caliberation_due_date : { required: "Please select calibration due date." },
				preventive_m_due_date : { required: "Please select preventive maintenance due date." },
				/*gmp_equipment_document : { required: "Please upload document." },*/
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		 
		
		// validate add person form on keyup and submit
		jQuery("#gmp-equipmentupdate-form").validate({
			errorElement:'div',
			rules: {
				gmp_equipment_name : { 
									required: true,
									maxlength: 200,
									//gmpequipmentname:true,
									remote: { 
										url : SITEROOT + "equipment/equipment/equipmentunique", 
										data : {"action" : "check_duplicate_equipment",'edit_id': edit_id},
										type : "post"
										//async:false
									}
								 },
				gmp_equipment_model : { required: true,
										maxlength: 50,
										gmpequipmentcode:true,
								},
				gmp_equipment_serial:{
										required: true,
										maxlength: 50,
										gmpequipmentcode:true,
								},
				caliberation_due_date : { required: true },
				preventive_m_due_date : { required: true }
			},
			messages:{
				gmp_equipment_name : { required: "Please enter equipment name.",
										remote: "Equipment name already exists.",
									},
				gmp_equipment_model : { required: "Please enter model number."	},
				gmp_equipment_serial:{	required: "Please enter serial number." },
				caliberation_due_date : { required: "Please select caliberation due date." },
				preventive_m_due_date : { required: "Please select preventive maintenance due date." },
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		
		jQuery("#msg").fadeOut(5000);
	});
