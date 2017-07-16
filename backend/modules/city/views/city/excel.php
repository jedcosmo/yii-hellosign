<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' CITIES.xlsx',
	'columns' => 
		[
			'name',
			[
	            'attribute'=>'state_id_fk',
				'format' => 'raw',
	            'value' => function($models){
						return $models->showStateName($models->state_id_fk);
					}
	        ],
			[
	            'attribute'=>'country_id_fk',
				'format' => 'raw',
				'value' => function($models){
						return $models->showCountryName($models->country_id_fk);
					}
	        ],
		], 
	'headers' => [
			'city_id_pk' => 'City ID',
			'name' => 'City Name', 
			'state_id_fk' => 'State',
			'country_id_fk' => 'Country',
		], 
]);


?>
