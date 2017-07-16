<?php
use yii\helpers\Html;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

?>

<!-- Navigation -->
    <nav class="navbar navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?= Yii::$app->homeUrl;?>atlas/atlas"><img src="<?= Yii::$app->homeUrl;?>atlas/img/atlasbailbond.png"></a>
        </div>

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <!-- Top Navigation: Right Menu -->
        <ul class="nav navbar-right navbar-top-links">
            <!-- <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell fa-fw"></i> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="#">
                            <div>
                                <i class="fa fa-comment fa-fw"></i> Welcome user.admin
                                <span class="pull-right text-muted small">8 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a class="text-center" href="#">
                            <strong>See All Alerts</strong>
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </li> -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <!-- <i class="fa fa-user fa-fw"></i>   -->

                       <?php if(Yii::$app->user->identity->user_name_person) { ?>
                        <img src="<?= Yii::$app->homeUrl; ?>/images/user.png" class="user-image" alt=""/>
                        <span class="hidden-xs">Welcome <?= \Yii::$app->user->identity->user_name_person.' ('.getCommonFunctions::getPersonDetails(Yii::$app->user->identity->person_id_pk)['name'].")";?></span>
                        <?php } ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                    </li>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="<?= Yii::$app->homeUrl;?>site/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Sidebar -->
        <div class="sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">

                <ul class="nav" id="side-menu">
                    <li>
                        <a href="<?= Yii::$app->homeUrl;?>atlas/atlas" class="active"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    </li>

                    <?php if($_SESSION['atlas_role'] == 'admin'){ ?>
                     <li>
                        <a href="<?= Yii::$app->homeUrl;?>atlas/atlas/clients"><i class="fa fa-user fa-fw"></i> Client</a>
                    </li>

                    <?php }else{ ?>
                    
                    <li>
                        <a href="#"><i class="fa fa-paperclip fa-fw"></i> Forms<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="<?php echo isset($settings['application']) ? 'done-green' : ''; ?>" href="<?= Yii::$app->homeUrl;?>atlas/atlas/application">Application</a>
                            </li>
                            <li>
                                <a class="<?php echo isset($settings['contract']) ? 'done-green' : ''; ?>" href="<?= Yii::$app->homeUrl;?>atlas/atlas/contract">Contract to Indemnify</a>
                            </li>
                            <li>
                                <a class="<?php echo isset($settings['promissory']) ? 'done-green' : ''; ?>" href="<?= Yii::$app->homeUrl;?>atlas/atlas/promissory">Promissory Note</a>
                            </li>
                            <li>
                                <a class="<?php echo isset($settings['ccauthorization']) ? 'done-green' : ''; ?>" href="<?= Yii::$app->homeUrl;?>atlas/atlas/ccauthorization">CC Authorization</a>
                            </li>
                             <li>
                                <a href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review" role="menuitem">Review</a>
                            </li>
                        </ul>
                    </li>

                    <?php } ?>
                </ul>

            </div>
        </div>
    </nav>