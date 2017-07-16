<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#">CLOUD GMP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in</p>
		<?php $session = Yii::$app->session;
			$verifySuccess = '';
			$verifyError = '';
			if(isset($_GET['verify']) && $_GET['verify']=='success')
			{
				$verifySuccess = 'Your account verified successfully.';
			}
			elseif(isset($_GET['verify']) && $_GET['verify']=='error')
			{
				$verifyError = 'Your account is deleted. Please contact CloudGMP Admin at support@cloudgmp.com';
			}
			if(isset($verifySuccess) && $verifySuccess!=''){ ?>
            <div id="msgS" class="alert alert-success">
              <strong>Success!</strong>&nbsp; <?=$verifySuccess;?>
            </div>
            <?php } if(isset($verifyError) && $verifyError!=''){ ?>
             <div id="msgS" class="alert alert-danger">
              <strong>Error!</strong>&nbsp; <?=$verifyError;?>
            </div>
        <?php } ?>
        <?php $form = ActiveForm::begin(['options' => ['id' => 'login-form', 'enableClientValidation' => false, 'autocomplete'=> 'off']]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => 'Username/Email', 'autofocus'=>'autofocus']) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <a href="<?php echo Yii::$app->homeUrl;?>site/forgot">I forgot my password</a>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
