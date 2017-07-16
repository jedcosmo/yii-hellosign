jQuery(document).ready(function(){

		var maxdate = new Date();
		
		var edit_id = $('#equipment_map_id_pk').val();
		var mpr_defination_id_fk = $('#mpr_defination_id_fk').val();
		var product_id_fk = $('#product_id_fk').val();
		var product_code = $('#product_code').val();
		
		$("#start_date_time").datetimepicker({ dateFormat: 'mm/dd/yy', timeFormat :'HH:mm:ss', changeMonth: true, changeYear: true});
		$("#end_date_time").datetimepicker({ dateFormat: 'mm/dd/yy', timeFormat :'HH:mm:ss', changeMonth: true, changeYear: true});
		
		jQuery("#equipment_id_fk").on('change',function(){
			$.get( SITEROOT+'equipmentmap/equipmentmap/equipmentdetails?id='+$('#equipment_id_fk').val(), function( data ) { 
					$("#equipment_name").val(data.equipment_id_pk);
					$("#equipment_model").val(data.model);					
					
					var calibratioDate = new Date(data.caliberation_due_date);
					var cDate = (calibratioDate.getMonth() + 1) + '/' + calibratioDate.getDate() + '/' +  calibratioDate.getFullYear();
					
					var preventiveDate = new Date(data.preventive_m_due_date);
					var pDate = (preventiveDate.getMonth() + 1) + '/' + preventiveDate.getDate() + '/' +  preventiveDate.getFullYear();
					
					$("#calibration_due_date").val(cDate);
					$("#preventive_m_due_date").val(pDate);
					
				},"json");
		});
		
		$('#start_date_time').change(function(e){
			var start_date_time = $("#start_date_time").val();
			if(start_date_time.length > 0){
				$("#start_date_time").removeData("previousValue"); //clear cache when changing group
				
				if(document.getElementById("gmp-equipment-form"))
				{
					$("#gmp-equipment-form").data('validator').element('#start_date_time'); //retrigger remote call
					$("#gmp-equipment-form").data('validator').element('#end_date_time'); //retrigger remote call
				}
			}
		});

		$('#end_date_time').change(function(e){
			var end_date_time = $("#end_date_time").val();
			if(end_date_time.length > 0){
				$("#end_date_time").removeData("previousValue"); //clear cache when changing group
				
				if(document.getElementById("gmp-equipment-form"))
				{
					$("#gmp-equipment-form").data('validator').element('#start_date_time'); //retrigger remote call
					$("#gmp-equipment-form").data('validator').element('#end_date_time'); //retrigger remote call
				}
			}
		});
		
		jQuery.validator.addMethod("comparedate", function(value, element) {
			
			var start_time = jQuery("#start_date_time").val() ;
			var end_time = value;
			
			if(start_time!='' && value!='')
			{				
				var starttm = Date.parse(start_time);
				var endtm = Date.parse(end_time);
			
				if(endtm > starttm)
					return true;
				else
					return false;
			}
			else
				return true;
		}, "End time must be greater than start time.");
		
		jQuery.validator.addMethod("comparefromdate", function(value, element) {
			
			var start_time = value;
			var end_time = jQuery("#end_date_time").val();
			
			if(end_time!='' && value!='')
			{	
				var starttm = Date.parse(start_time);
				var endtm = Date.parse(end_time);
			
				if(endtm > starttm)
					return true;
				else
					return false;
			}
			else
				return true;
		}, "Start time must be less than end time.");
		
		// validate add person form on keyup and submit
		jQuery("#gmp-equipment-form").validate({
			errorElement:'div',
			rules: {
				equipment_id_fk : { required: true, 
									remote: { 
										url : SITEROOT + "equipmentmap/equipmentmap/equipmentunique", 
										data: {'edit_id': edit_id,'mpr_defination_id_fk':mpr_defination_id_fk,'product_id_fk':product_id_fk,'product_code':product_code},
										type:"post",
										//async:false 
									}
								},
				start_date_time : { comparefromdate: true },
				end_date_time : { comparedate: true }
			},
			messages:{
				equipment_id_fk : { required: "Please select equipment",
									remote: "Equipment already exists for this MPR Definition",
								}
			},

			success: function(label){
				label.hide();
			}
		});

		jQuery("#msg").fadeOut(5000);
});
