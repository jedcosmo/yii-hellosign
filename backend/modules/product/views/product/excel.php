<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' PRODUCTS.xlsx',
	'columns' => 
		[
			//'product_id_pk',
			'part',
			'name',
			'code',
			[                      
				'attribute'=>'company_id_fk',
				'format'=>'raw',
            	'value' => function($models){
					return $models->showCompanyName($models->company_id_fk);
				},
        	],
			[                      
				'attribute'=>'unit_id_fk',
				'format'=>'raw',
            	'value' =>function($models){
					return $models->showUnitName($models->unit_id_fk);
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
			'product_id_pk' => 'Product ID',
			'part' => 'Product Part', 
			'name' => 'Product Name',
			'code' => 'Product Code',
			'company_id_fk' => 'Company',
			'unit_id_fk' => 'Unit',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
