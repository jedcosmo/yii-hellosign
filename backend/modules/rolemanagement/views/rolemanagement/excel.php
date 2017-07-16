<?php

use yii\web\ExcelGrid;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' ROLES.xlsx',
	'columns' => 
		[
			 'name',
            [
				'attribute' => 'modules',
				'format' => 'raw',
				'value' => function($data){
					$retStr = CommonFunctions::display_all_role_modules($data->modules);
					return $retStr;
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
			'role_id_pk' => 'Role ID',
			'name' => 'Role Name', 
			'modules' => 'Modules',
			'created_datetime' => 'Added Datetime',
		], 
]);


?>
