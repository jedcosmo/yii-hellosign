<?php
//namespace vendor\hellosign;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../../common/config/bootstrap.php');
require(__DIR__ . '/../../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../common/config/main.php'),
    require(__DIR__ . '/../../../common/config/main-local.php'),
    require(__DIR__ . '/../../config/main.php'),
    require(__DIR__ . '/../../config/main-local.php')
);

$application = new yii\web\Application($config);

include('config.php');

$documentID = isset($_REQUEST['documentID'])?$_REQUEST['documentID']:0; 
$docTitle = time().'.pdf';
$url = 'https://@api.hellosign.com/v3/signature_request/files/'.$documentID;

/************************************************/
if($documentID)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERPWD, API_KEY . ":");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($ch);
	//print_r($response);
	
	header('Cache-Control: public'); 
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="'.$docTitle.'"');
	header('Content-Length: '.strlen($response));
	echo $response;
	
	curl_close($ch);
}
?>
