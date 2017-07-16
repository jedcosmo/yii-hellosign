<?php
if($_SERVER['HTTP_HOST'] == '192.168.33.10'){
	$host = 'localhost';
	$username = 'root';
	$password = 'root';
}else{
	$host = 'xerosoftdevdb.cnomngrqfwbn.us-west-2.rds.amazonaws.com';
	$username = 'gmpuser';
	$password = 'gmppassword';
}


$config = [
	//'homeUrl' => '/admin',
	'modules' => [
			'country' => [
				'class' => 'backend\modules\country\country',
			],
			'state' => [
				'class' => 'backend\modules\state\state',
			],
			'city' => [
				'class' => 'backend\modules\city\city',
			],
			'adminUser' => [
            	'class' => 'backend\modules\adminUser\adminUser',
        	],
			'sitesetting' => [
            	'class' => 'backend\modules\sitesetting\sitesetting',
        	],

			'user' => [
            	'class' => 'backend\modules\user\user',
        	],

			'cmspages' => [
            	'class' => 'backend\modules\cmspages\cmspages',
        	],
			
			'faqs' => [
            	'class' => 'backend\modules\faqs\faqs',
        	],
			
			'emailtemplates' => [
            	'class' => 'backend\modules\emailtemplates\emailtemplates',
        	],
			
			'contactus' => [
            	'class' => 'backend\modules\contactus\contactus',
        	],
			
			'contactusreply' => [
            	'class' => 'backend\modules\contactusreply\contactusreply',
        	],
			
			'activitylog' => [
            	'class' => 'backend\modules\activitylog\activitylog',
        	],
			
			'person' => [
            	'class' => 'backend\modules\person\person',
        	],
			
			'unit' => [
            	'class' => 'backend\modules\unit\unit',
        	],
			
			'company' => [
            	'class' => 'backend\modules\company\company',
        	],
			
			'product' => [
            	'class' => 'backend\modules\product\product',
        	],
			
			'documents' => [
            	'class' => 'backend\modules\documents\documents',
        	],
			
			'equipment' => [
            	'class' => 'backend\modules\equipment\equipment',
        	],
			
			'mprdefinition' => [
            	'class' => 'backend\modules\mprdefinition\mprdefinition',
        	],
			
			'billofmaterial' => [
            	'class' => 'backend\modules\billofmaterial\billofmaterial',
        	],
			
			'equipmentmap' => [
            	'class' => 'backend\modules\equipmentmap\equipmentmap',
        	],
			
			'minstructions' => [
            	'class' => 'backend\modules\minstructions\minstructions',
        	],
			
			'mprapprovals' => [
            	'class' => 'backend\modules\mprapprovals\mprapprovals',
        	],
			
			'bprrecords' => [
            	'class' => 'backend\modules\bprrecords\bprrecords',
        	],
			
			'bprapprovals' => [
            	'class' => 'backend\modules\bprapprovals\bprapprovals',
        	],
			'rolemanagement' => [
            	'class' => 'backend\modules\rolemanagement\rolemanagement',
        	],
			'personcompany' => [
            	'class' => 'backend\modules\personcompany\personcompany',
        	],
			'companyadmins' => [
            	'class' => 'backend\modules\companyadmins\companyadmins',
        	],
			'myprofile' => [
            	'class' => 'backend\modules\myprofile\myprofile',
        	],
			'formulation' => [
            	'class' => 'backend\modules\formulation\formulation',
        	],
            'atlas' => [
                'class' => 'backend\modules\atlas\atlas',
            ]

		],


    'components' => [
        /*'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'dkw5b6mpuwMUBpkW9CADbYyVd2fAwab7',
        ],
		'session' => [ 
			//To seperate frontend and backend folder
			'name' => 'PHPBACKSESSID',
			'savePath' => __DIR__,
		],*/
		 'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $host . ';dbname=gmpdb',
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
        ],
		
		'user' => [
			'identityClass' => 'common\models\Admin',
			'enableAutoLogin' => true,
			'identityCookie' => [
				'name' => '_backendUser', // unique for backend
			]
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => '/var/www/html/',
			//'savePath' => sys_get_temp_dir(),
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'dkw5b6mpuwMUBpkW9CADbYyVd2fAwab7',
            'csrfParam' => '_backendCSRF',
			//'baseUrl' => '/admin',
        ],	
		'assetManager' => [
        	'bundles' => [
            	'dmstr\web\AdminLteAsset' => [
                	'skin' => 'skin-black',
            	],
        	],
    	],
			
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
   /* $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;

