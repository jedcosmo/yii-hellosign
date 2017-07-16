<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
	'name'=>'CLOUD GMP',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
	'timeZone' => 'US/Central',
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
			'authTimeout' => 3000,
			//'autoRenewCookie' => true,
			//'enableSession' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		 'response' => [
            'formatters' => [
                'pdf' => [
                    'class' => 'robregonm\pdf\PdfResponseFormatter',
                ],
            ]
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',            
        ],
    ],
    'params' => $params,
];
