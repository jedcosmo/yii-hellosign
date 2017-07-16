<?php

use yii\web\ExcelGrid;


\moonland\phpexcel\Excel::widget([
	'models' => $models,
	'mode' => 'export',
	'fileName' => Yii::$app->name.' MPR DEFINITIONS.xlsx',
	'columns' => 
		[
			'product_code',
			'MPR_version',
			'product_part',
			'product_name',
		], 
	'headers' => [
			'product_code' => 'Product Code',
			'MPR_version' => 'MPR Version',
			'product_part' => 'Product Part',
			'product_name' => 'Product Name',
		], 
]);


?>
