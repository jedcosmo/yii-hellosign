<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' STATES.xlsx',
	'columns' => 
		[
			'name',
			[                      
				'attribute'=>'country_id_fk',
				'format'=>'raw',
            	'value' => function($models){
					return $models->showCountryName($models->country_id_fk);
					},
        	],
		], 
	'headers' => [
			'state_id_pk' => 'State ID',
			'name' => 'State Name', 
			'country_id_fk' => 'Country Name',
		], 
]);


?>
