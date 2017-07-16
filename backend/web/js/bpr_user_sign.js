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
	var mpr_def_id_hid = $('#mpr_def_id_hid').val();
	var mi_id_hid = $('#mi_id_hid').val();
	var signType = $('#signType').val();
	
	var formdata = $('#login_form').serialize();
	$.post(SITEROOT + 'bprrecords/bprrecords/sign_approve?bpr_id='+bpr_id_hid+'&mpr_def_id='+mpr_def_id_hid+'&mi_id='+mi_id_hid+'&signType='+signType, formdata, function(dataNew){
		if(dataNew.success == 1)
		{
			var htmlStr = '<span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;'+dataNew.personDetails['first_name'] + ' ' + dataNew.personDetails['last_name'] + '&nbsp;( '+dataNew.personDetails['user_name_person']+' )&nbsp;</span>';
			$('#performer_id_fk').val(dataNew.personDetails['person_id_pk']);
			$('#confirm_btn').removeAttr('disabled');
			$('#signed_user_div').html(htmlStr);
			$('#signed_user_div').show();
			$('#sign_btn_div').hide();
			$('#myModal').modal('hide');
		}
		else
		{
			var htmlStr = 'Invalid login credentials';
			$('#login_error_div').html(htmlStr);
			$('#login_error_div').show();
		}
	}, 'json');
}
