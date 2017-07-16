<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' COUNTRIES.xlsx',
	'columns' => 
		[
			'name',
		], 
	'headers' => [
			'country_id_pk' => 'Country ID',
			'name' => 'Country Name', 
		], 
]);


?>
