<?php

if($_SERVER['HTTP_HOST'] == '192.168.33.10'){
	return [
		'components' => [
			'db' => [
				'class' => 'yii\db\Connection',
				 'dsn' => 'mysql:host=localhost;dbname=gmpdb',
				'username' => 'root',
				'password' => 'root',
				'charset' => 'utf8',
			],
			'mailer' => [
				'class' => 'yii\swiftmailer\Mailer',
				'viewPath' => '@common/mail',
				// send all mails to a file by default. You have to set
				// 'useFileTransport' to false and configure a transport
				// for the mailer to send real emails.
				'useFileTransport' => true,
			],
			'urlManager' => [
				'class' => 'yii\web\UrlManager',
				'enablePrettyUrl' => true,
				'showScriptName' => false,
			],
			
		],
	];
}else{
	
	return [
		'components' => [
			'db' => [
				'class' => 'yii\db\Connection',
				 'dsn' => 'mysql:host=xerosoftdevdb.cnomngrqfwbn.us-west-2.rds.amazonaws.com;dbname=gmpdb',
				'username' => 'gmpuser',
				'password' => 'gmppassword',
				'charset' => 'utf8',
			],
			'mailer' => [
				'class' => 'yii\swiftmailer\Mailer',
				'viewPath' => '@common/mail',
				// send all mails to a file by default. You have to set
				// 'useFileTransport' to false and configure a transport
				// for the mailer to send real emails.
				'useFileTransport' => true,
			],
			'urlManager' => [
				'class' => 'yii\web\UrlManager',
				'enablePrettyUrl' => true,
				'showScriptName' => false,
			],
			
		],
	];
}