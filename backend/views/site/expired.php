<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\AdminResetPasswordForm;
use backend\modules\adminUser\models\Admin;
/* @var $this \yii\web\View */
/* @var $content string */
dmstr\web\AdminLteAsset::register($this);

$this->title = 'Error';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page">

<?php $this->beginBody() ?>

<div class="login-box">
	 <div class="login-logo">
        <a href="#">CLOUD GMP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body row">
    	<div class="col-sm-2">
        	<h2 class="text-info"><i class="fa fa-warning text-yellow"></i></h2>
        </div>
        <div class="col-sm-10">
            <div class="error-content">
                <h3>ERROR</h3>
                <p>The link is expired. Please check your link once again. </p>
            </div>
        </div>
   </div>
</div><!-- /.login-box -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();exit; ?>
