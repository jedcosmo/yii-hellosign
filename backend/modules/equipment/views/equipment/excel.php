<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' EQUIPMENTS.xlsx',
	'columns' => 
		[
			'equipment_id_pk',
			'name',
			'model',
			'serial',
			[                      
				'attribute'=>'caliberation_due_date',
				'format'=>'raw',
            	'value' => function($models){
					return ($models->caliberation_due_date && $models->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($models->caliberation_due_date)):'';
					},
			],
           	[                      
				'attribute'=>'preventive_m_due_date',
				'format'=>'raw',
            	'value' => function($models){
					return ($models->preventive_m_due_date && $models->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($models->preventive_m_due_date)):'';
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
			'equipment_id_pk' => 'Equipment ID',
			'name' => 'Equipment Name', 
			'model' => 'Model',
			'serial' => 'Serial',
			'caliberation_due_date' => 'Calibration Due Date',
			'preventive_m_due_date' => 'Preventive Maintenance Due Date',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
