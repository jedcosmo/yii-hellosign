<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' PERSONS.xlsx',
	'columns' => 
		[
			'person_id_pk',
			'first_name',
			'last_name',
			'emailid',
			'user_name_person',
			'phone',
			'fax',
			'address',
			[                      
				'attribute'=>'country_id_fk',
				'format'=>'raw',
            	'value' => function($models){
					return $models->showCountryName($models->country_id_fk);
					},
        	],
			[                      
				'attribute'=>'state_id_fk',
				'format'=>'raw',
            	'value' => function($models){
					return $models->showStateName($models->state_id_fk);
					},
        	],
			[                      
				'attribute'=>'city_id_fk',
				'format'=>'raw',
            	'value' => function($models){
					return $models->showCityName($models->city_id_fk);
					},
        	],
			'pobox',
			'zip_pincode',
			[                      
				'attribute' => 'role_id_fk',
				'format' => 'raw',
            	'value' => function($models){
					return $models->showRoleName($models->role_id_fk);
				},
        	],
			[
				'attribute' => 'created_datetime',
				'format' => 'raw',
            	'value' => function($models){
					return date("m/d/Y H:i:s",strtotime($models->created_datetime));
				},
			],
		], 
	'headers' => [
			'person_id_pk' => 'Person ID',
			'first_name' => 'First Name', 
			'last_name' => 'Last Name',
			'emailid' => 'Email ID',
			'user_name_person' => 'username',
			'phone' => 'Phone',
			'fax' => 'Fax',
			'address' => 'Address',
			'country_id_fk' => 'Country',
			'state_id_fk' => 'State',
			'city_id_fk' => 'City',
			'pobox' => 'PO Box',
			'zip_pincode' => 'Zip/Pincode',
			'role_id_fk' => 'Role',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
