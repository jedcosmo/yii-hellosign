jQuery(document).ready(function(){ 	
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
				var module_primary_id = $('#module_primary_id').val();
				var username = $('#username').val();
				var password = $('#password').val();
				var signType = $('#signType').val();
				
				$.post(SITEROOT+'bprrecords/bprrecords/checklogincredentials', {username:username, password:password, mpr_appr_id:module_primary_id,signType:signType}, function(data){
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
	window.location.href = SITEROOT + 'bprrecords/bprrecords';
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
	var bpr_id_hid  = $('#bpr_id_hid').val();
	var status = $('#status_hid').val();
	var formdata = $('#login_form').serialize();
	$.post(SITEROOT + 'bprrecords/bprrecords/change_bpr_status?bprid='+bpr_id_hid+'&status='+status,formdata, function(data){
		if(data.success == 1)
		{
			$('#myModal').modal('hide');
			window.location.href = SITEROOT + 'bprrecords/bprrecords';
		}
		else
		{
			var htmlStr = 'Invalid login credentials';
			$('#login_error_div').html(htmlStr);
			$('#login_error_div').show();
		}
	},'json');
}
