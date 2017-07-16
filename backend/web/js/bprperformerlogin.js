jQuery(document).ready(function(){ 	
				
	showGlobalLoadingMessage('end');
	
	$('#myModal').on('shown.bs.modal', function () {
		$('#username').focus();
	});
	
	// validate login form on keyup and submit
	jQuery("#login_form").validate({
		errorElement:'div',
		rules: {
			username : {required: true},
			password : { required: true}
		},
		messages:{
			username : {required: "Please enter username/email"},
			password : { required: "Please enter password"}
		},
		// set this class to error-labels to indicate valid fields
		success: function(label){
			// set &nbsp; as text for IE
			label.hide();
		},
		submitHandler: function(form) { 
				//$('button[name="login-button"]', form).attr('disabled', 'disabled');
				
				var module_primary_id = $('#module_primary_id').val();
				var username = $('#username').val();
				var password = $('#password').val();
				var signType = $('#signType').val();
				
				$.post(SITEROOT+'bprapprovals/bprapprovals/checklogincredentials', {username:username, password:password, bpr_appr_id:module_primary_id,signType:signType}, function(data){
					if(data)
					{
						if(data=='success')
						{
							$('#logErrorMsg').html('');
							$('#logErrorMsg').hide();
							$('#loginFormDiv').hide();
							$('#HelloSignBtnDiv').show();
						}
						else
						{
							$('#logErrorMsg').html(data);
							$('#logErrorMsg').show();
						}
					}
				});
			}
	});
	
	jQuery("#msgS").fadeOut(5000);
	jQuery("#msgE").fadeOut(5000);
	
});

function closethisPopup()
{
	$('#myModal').modal('hide');
}


function doHelloSign(id, modulename,signType)
{
	var masterBPR = $('#masterBPR').val();
	var url = SITEROOT+'HelloSign/HelloSign.php?module_primary_id='+id+'&module_name='+modulename+'&signType='+signType+'&masterBPR='+masterBPR;
		
	newwindow = window.open(url,'name','height=800,width=1100');
	if (window.focus) {newwindow.focus()}
	return false;
}

function confirmHelloSign()
{
	var url = $('#login_form').attr('action');
	var formdata = $('#login_form').serialize();
	var module_primary_id = $('#module_primary_id').val();
	var masterBPR = $('#masterBPR').val();
	var username = $('#username').val();
	var password = $('#password').val();
	
	$.post(url, {username:username,password:password,masterBPR:masterBPR,mpr_appr_id:module_primary_id}, function(data){
		if(data.success=="Yes")
		{
			$('#myModal').modal('hide');
			window.location.reload();
		}
	}, 'json');
}
