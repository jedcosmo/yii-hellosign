jQuery(document).ready(function(){
		//	set autofous to form first field
		$('#gmp-unit-form:first *:input[type!=hidden]:first').focus();
		$('#gmp-unitupdate-form:first *:input[type!=hidden]:first').focus();
		
		$('#gmp_unit_description').change(function(e){
			var gmp_unit_description = $("#gmp_unit_description").val();
			if(gmp_unit_description.length > 0){
				$("#gmp_unit_description").removeData("previousValue"); //clear cache when changing group
				
				if(document.getElementById("gmp-unit-form"))
					$("#gmp-unit-form").data('validator').element('#gmp_unit_description'); //retrigger remote call
				if(document.getElementById("gmp-unitupdate-form"))
					$("#gmp-unitupdate-form").data('validator').element('#gmp_unit_description'); //retrigger remote call
			}
		});
		
		
		$('#gmp_unit_name').change(function(e){
			var gmp_unit_name = $("#gmp_unit_name").val();
			if(gmp_unit_name.length > 0){
				$("#gmp_unit_name").removeData("previousValue"); //clear cache when changing group
				
				if(document.getElementById("gmp-unit-form"))
					$("#gmp-unit-form").data('validator').element('#gmp_unit_name'); //retrigger remote call
				if(document.getElementById("gmp-unitupdate-form"))
					$("#gmp-unitupdate-form").data('validator').element('#gmp_unit_name'); //retrigger remote call
			}
		});
		
		
		var edit_id = $('#gmp_unit_id_pk').val();
		var unit_name = $('#gmp_unit_name').val();
 		var unit_description = $('#gmp_unit_description').val();
		// validate add person form on keyup and submit
		jQuery("#gmp-unit-form").validate({
			errorElement:'div',
			rules: {
				gmp_unit_name : { 
									required: true,
									maxlength: 20,
									remote: { 
										url : SITEROOT + "unit/unit/unitunique", 
										data: {'action':'check_duplicate_unit','edit_id': edit_id, 'unit_description': function(){return $('#gmp_unit_description').val();}},
										 type:"post",
										//async:false
									}
								 },
				gmp_unit_description : { 
									required: true,
									maxlength: 255,
									remote: { 
										url : SITEROOT + "unit/unit/unitunique", 
										data: {'action':'check_duplicate_desc','edit_id': edit_id, 'unit_name': function(){return $('#gmp_unit_name').val();}},
										 type:"post",
										//async:false 
									}
								},
			},
			messages:{
				gmp_unit_name:{required: "Please enter unit name",
								remote: "Unit name with same description already exists",
						},
				gmp_unit_description:{	required: "Please enter unit description",
										remote: "Unit name with same description already exists",
										maxlength: "Please enter less than 255 characters.",
							}		
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		
		// validate update person form on keyup and submit
		jQuery("#gmp-unitupdate-form").validate({
			errorElement:'div',
			rules: {
				gmp_unit_name : { 
									required: true,
									maxlength: 20,
									remote: { 
										url : SITEROOT + "unit/unit/unitunique", 
										data: {'action':'check_duplicate_unit','edit_id': edit_id, 'unit_description': function(){return $('#gmp_unit_description').val();}},
										 type:"post",
										//async:false 
									}
								 },
				gmp_unit_description : { 
									required: true,
									maxlength: 255,
									remote: { 
										url : SITEROOT + "unit/unit/unitunique", 
										data: {'action':'check_duplicate_desc','edit_id': edit_id, 'unit_name': function(){return $('#gmp_unit_name').val();}},
										 type:"post",
										//async:false 
									}
								},
			},
			messages:{
				gmp_unit_name:{required: "Please enter unit name",
								remote: "Unit name with same description already exists",
							},
				gmp_unit_description:{	required: "Please enter unit description",
										remote: "Unit name with same description already exists",
										maxlength: "Please enter less than 255 characters.",
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

function blurDescription(){
	$('#gmp_unit_description').trigger('change');
}

function blurName()
{
	$('#gmp_unit_name').trigger('change');
}
