<?php
echo phpinfo(); die;
		$from = "Admin"; 
		$fromEmail = "pm@mindts.com";
		
		$subject = "x 30 - Account details updated - test 1";
		$emailContent = "Hello test test,<br/><br/> Your account credentials are changed.<br/> Please click on below link to activate your changes.<br/><br/><br/><br/>Regards,<br/>  Services";	

		$email_msg = file_get_contents("backend/web/emailtemplate/verify-email.html");
		$email_msg = str_replace('{email_content}',$emailContent,$email_msg);
		$email_msg = str_replace('{subject}',$subject,$email_msg);
		$email_msg = str_replace('{base_url}','',$email_msg);
		$email_msg = str_replace('{sitetitle}','CloudGMP',$email_msg);
		$email_msg = str_replace('{date}',date("Y"),$email_msg);

		$name = '=?UTF-8?B?'.base64_encode($from).'?=';
		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
		
		$headers = "From: $from <{$fromEmail}>\r\n";
		$headers.= "Reply-To: {$fromEmail}\r\n";
		$headers.= "MIME-Version: 1.0\r\n";
		//$headers.= "Content-type: text/html; charset=UTF-8";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
		//echo $email_msg; exit;
		echo mail('mukeshrane.mcm@gmail.com',$subject,$email_msg,$headers);
		
?>