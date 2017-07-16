<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' PERSON COMPANIES.xlsx',
	'columns' => 
		[
			'company_id_pk',
			'name',
			'address1',
			'address2',
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
			'zip_postalcode',
			[
				'attribute' => 'created_datetime',
				'format' => 'raw',
            	'value' => function($models){
					return date("m/d/Y H:i:s",strtotime($models->created_datetime));
				},
			],
		], 
	'headers' => [
			'company_id_pk' => 'Company ID',
			'name' => 'Company Name', 
			'address1' => 'Address 1',
			'address2' => 'Address 2',
			'country_id_fk' => 'Country',
			'state_id_fk' => 'State',
			'city_id_fk' => 'City',
			'zip_postalcode' => 'Zip/Postal Code',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
