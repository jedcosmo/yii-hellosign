<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' UNITS.xlsx',
	'columns' => 
		[
			'unit_id_pk',
			'name',
			'description',
			[
				'attribute' => 'created_datetime',
				'format' => 'raw',
            	'value' => function($models){
					return date("m/d/Y H:i:s",strtotime($models->created_datetime));
				},
			],
		], 
	'headers' => [
			'unit_id_pk' => 'Unit ID',
			'name' => 'Unit Name', 
			'description' => 'Description',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
