jQuery(document).ready(function(){	
	
	//	set autofous to form first field
	$('#gmp-role-form:first *:input[type!=hidden]:first').focus();
	
	var edit_id = jQuery('#gmp_role_id_pk').val();
	jQuery("#msg").fadeOut(5000);
	
	jQuery('input[type="checkbox"]').on('click',function(){
		checkifAllchecked();											  
	});	
	
	jQuery("#gmp-role-form").on('submit',function(){
		var checkedCount = $("input[custAttr='roleModuleChk']:checked").length;
		if(checkedCount<=0)
		{
			$('#chkModulesErr').html("Please select role modules");
			$('#chkModulesErr').show();
			return false;
		}
		else
		{
			$('#chkModulesErr').html("");
			$('#chkModulesErr').hide();
		}
	});
	
	
	// validate add person form on keyup and submit
		jQuery("#gmp-role-form").validate({
			errorElement:'div',
			rules: {
				gmp_role : { 
								required: true,
								maxlength: 50,
								remote: { 
									url : SITEROOT + "rolemanagement/rolemanagement/roleunique", 
									data : {'edit_id': edit_id},
									type : "post"
									//async:false
								}
							 },	
			},
			messages:{
				gmp_role : { required: "Please enter role name.",
							 remote: "Role already exists",
						},
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
	
	
	
});


function AddRoleModule(obj)
{
	var isChecked = jQuery(obj).children('input[type="checkbox"]').is(':checked');

	if(isChecked==true)
		jQuery(obj).children('input[type="checkbox"]').prop('checked',false);
	else
		jQuery(obj).children('input[type="checkbox"]').prop('checked',true);
}

function checkAllModules()
{
	var isChecked = jQuery('#all_chk').is(':checked');
	if(isChecked==true)
	{
		jQuery('input[type="checkbox"]').each(function(){
			jQuery(this).prop('checked',true);											  
		});
	}
	else
	{
		jQuery('input[type="checkbox"]').each(function(){
			jQuery(this).prop('checked',false);											  
		});
	}
}

function checkifAllchecked()
{
	var AllChk = $("input[custAttr='roleModuleChk']").length;
	var checkedCount = $("input[custAttr='roleModuleChk']:checked").length;
	if(AllChk == checkedCount)
		jQuery('#all_chk').prop('checked',true);
	else
		jQuery('#all_chk').prop('checked',false);
}
