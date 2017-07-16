<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">GMP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

               <li class="dropdown notifications-menu user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if(Yii::$app->user->identity->user_name_person) { ?>
                        <img src="<?= Yii::$app->homeUrl; ?>/images/user.png" class="user-image" alt=""/>
                        <span class="hidden-xs">Welcome <?=Yii::$app->user->identity->user_name_person.' ('.getCommonFunctions::getPersonDetails(Yii::$app->user->identity->person_id_pk)['name'].")";?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu" style="width:180px;">
                        <li>
                            <ul class="menu">
                                <li>
                                    <a href="<?= Yii::$app->homeUrl; ?>myprofile/myprofile/view?id=<?=Yii::$app->user->id;?>">
                                        <i class="fa fa-user"></i> My Profile
                                    </a>
                                </li>
                                 <li>
                                 	<?= Html::a(
                                    '<i class="fa fa-key"></i>Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => '']
                                	) ?>
                                   
                                </li>
                            </ul>
                         </li>
                      </ul>
                 </li>
            </ul>
        </div>
    </nav>
</header>
