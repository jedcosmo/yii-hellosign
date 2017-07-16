<?php

if($_SERVER['HTTP_HOST'] == '192.168.33.10'){
	$baseURl = "http://192.168.33.10/x30-production/backend/web";
}else{
	$baseURl = 'http://' . $_SERVER['HTTP_HOST'] . '/x30-production/backend/web';
}

return [	
    'adminEmail' => 'support@cloudgmp.com',
	'baseURLEmailTempPath' => $baseURl,
	'baseAbsPath' => $baseURl,
	'dirRootPath' => '/var/www/html/x30-production',
	'timezoneVar' => ' CST',
	'audit_log_action' => [
						'ADD' => 'ADDED', 
						'DELETE' => 'DELETED', 
						'ACCESS' => 'ACCESSED', 
						'APPROVED' => 'APPROVED', 
						'REVIEWED' => 'REVIEWED', 
						'UPDATE' => 'UPDATED', 
						'EXPORT' => 'EXPORTED', 
						'VERIFIED' => 'VERIFIED', 
						'RESTORED' => 'RESTORED',
						'QUARANTINE' => 'QUARANTINED',
						'REJECTED' => 'REJECTED',
			],
	'audit_log_screen_name' => [
								'Person' => 'Person',
								'Company' => 'Company',
								'Units' => 'Units',
								'Product' => 'Product',
								'Equipment' => 'Equipment',
								'MPR' => 'MPR',
								'MPR_Cover_Page' => 'MPR Cover Page',
								'MPR_Bill_of_Materials' => 'MPR Bill of Materials',
								'MPR_Formulation' => 'MPR Formulation',
								'MPR_Equipments' => 'MPR Equipments',
								'MPR_Manufacturing_Instructions' => 'MPR Manufacturing Instructions',
								'MPR_Approvals' => 'MPR Approvals',
								'BPR' => 'BPR',
								'BPR_Cover_Page' => 'BPR Cover Page',
								'BPR_Bill_of_Materials' => 'BPR Bill of Materials',
								'BPR_Formulation' => 'BPR Formulation',
								'BPR_Equipments' => 'BPR Equipments',
								'BPR_Manufacturing_Instructions' => 'BPR Manufacturing Instructions',
								'BPR_Approvals' => 'BPR Approvals',
								'BPR_Status' => 'BPR Status',
								'Login' => 'Login',
								'Logout' => 'Logout',
								'Reset_Password' => 'Reset Password',
								'Forgot_Password' => 'Forgot Password',
								'Manufacturing_Instructions' => 'Manufacturing Instructions',
								'City' => 'City',
								'Country' => 'Country',
								'States' => 'States',
								'PersonCompany' => 'Person Company',
								'RoleManagement' => 'Role Management',
								'CompanyAdmin' => 'Company Admin',
								'MyProfile' => 'My Profile',
							],
];
