jQuery(document).ready(function(){
								
	//	set autofous to form first field
	$('#gmp-mprapprovals-form:first *:input[type!=hidden]:first').focus();
				
	var edit_id = $('#bpr_approval_id_pk').val();
	var bpr_id_fk = $('#bpr_id_fk').val();
	
	
	jQuery.validator.addMethod("approverRequired", function(value, element) {
		if($('#approverDiv').is(':visible')==true)
		{
			if(value)
       			return true;
			else
				return false;
		}
		else
			return true;
	},'Please select approver');
	
	jQuery.validator.addMethod("approverJobFunction", function(value, element) {
		if($('#approverDiv').is(':visible')==true)
		{
			if(value)
       			return true;
			else
				return false;
		}
		else
			return true;
	},"Please enter apporver's job function");
	
	jQuery.validator.addMethod("verifierRequired", function(value, element) {
		if($('#verifierDiv').is(':visible')==true)
       	{
			if(value)
       			return true;
			else
				return false;
		}
		else
			return true;
	},'Please select verifier');
	
	
	jQuery.validator.addMethod("verifierJobFunction", function(value, element) {
		if($('#verifierDiv').is(':visible')==true)
       	{
			if(value)
       			return true;
			else
				return false;
		}
		else
			return true;
	},"Please enter verifier's job function");
	
	// validate add person form on keyup and submit
	jQuery("#gmp-mprapprovals-form").validate({
		errorElement:'div',
		rules: {
			approval_person_id_fk : { approverRequired: true,
									  remote: { 
										url : SITEROOT + "bprapprovals/bprapprovals/bprapprovalunique", 
										data: {'action':'Approver','edit_id': edit_id,'bpr_id_fk':bpr_id_fk,'approval_job_function': function(){return $('#approval_job_function').val();}},
										type:"post",
										//async:false 
									}
								},
			approval_job_function : { 
							approverJobFunction: true,
							maxlength: 255,
							remote: { 
										url : SITEROOT + "bprapprovals/bprapprovals/bprapprovalunique", 
										data: {'action':'Approver','edit_id': edit_id,'bpr_id_fk':bpr_id_fk,'approval_person_id_fk': function(){return $('#approval_person_id_fk').val();}},
										type:"post",
										//async:false 
									}
						},
			verifier_person_id_fk : { verifierRequired: true,
									  remote: { 
										url : SITEROOT + "bprapprovals/bprapprovals/bprapprovalunique", 
										data: {'action':'Verifier','edit_id': edit_id,'bpr_id_fk':bpr_id_fk,'verifier_job_function': function(){return $('#verifier_job_function').val();}},
										type:"post",
										//async:false 
									}
								},
			verifier_job_function : { 
							verifierJobFunction: true,
						 	maxlength:255,
							remote: { 
										url : SITEROOT + "bprapprovals/bprapprovals/bprapprovalunique", 
										data: {'action':'Verifier','edit_id': edit_id,'bpr_id_fk':bpr_id_fk,'verifier_person_id_fk': function(){return $('#verifier_person_id_fk').val();}},
										type:"post",
										//async:false 
									}
						},
		},
		messages:{
			approval_person_id_fk : { required: "Please select approver",
									  remote: "Approver with same job function already exists for this BPR",
							},
			approval_job_function : { required: "Please enter apporver's job function",
									  remote: "Approver with same job function already exists for this BPR",
									},
			verifier_person_id_fk : { required: "Please select verifier",
									  remote: "Verifier with same job function already exists for this BPR",
								},
			verifier_job_function : { required: "Please enter verifier's job function",
									  remote: "Verifier with same job function already exists for this BPR",
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

function showHideFields(acttxt)
{
	switch(acttxt){
		case 'Performer':
							if($('#performerChk').is(':checked')==true)
							{
								$('#approverDiv').show();
							}
							else
							{
								$('#approverDiv').hide();
								$('#approval_person_id_fk').val('');
								$('#approval_job_function').val('');
							}
							break;
		case 'Verifier':
							if($('#verifierChk').is(':checked')==true)
							{
								$('#verifierDiv').show();
							}
							else
							{
								$('#verifierDiv').hide();
								$('#verifier_person_id_fk').val('');
								$('#verifier_job_function').val('');
							}
							break;
	}
	
	if($('#performerChk').is(':checked')==true ||  $('#verifierChk').is(':checked')==true)
		$('#submitbtnDiv').show();
	else
		$('#submitbtnDiv').hide();
}
