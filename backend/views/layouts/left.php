<?php
use yii\bootstrap\Nav;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;


$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$mprid = (isset($_REQUEST['mprid']))?$_REQUEST['mprid']:0;
?>
<aside class="main-sidebar">

    <section class="sidebar">
        <?=
        Nav::widget(
            [
                'encodeLabels' => false,
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
					['label' => '<i class="fa fa-tachometer"></i><span>DASHBOARD</span>', 'url' => ['/'],'active'=> \Yii::$app->controller->id == 'site'],
                  					
                    [
                        'label' => '<i class="glyphicon glyphicon-lock"></i><span>Sign in</span>', //for basic
                        'url' => ['/site/login'],
                        'visible' =>Yii::$app->user->isGuest
                    ],
                ],
				'activateItems'=>'true',
            ]
        );
        ?>
        
         <?php  if((CommonFunctions::isAccessible('RM_Country') || CommonFunctions::isAccessible('RM_State') || CommonFunctions::isAccessible('RM_City')) && $fromAuditLog!='Yes'){ ?>
        <ul class="sidebar-menu">
            <li class="treeview <?php if((Yii::$app->controller->id == 'country' || Yii::$app->controller->id == 'state' || Yii::$app->controller->id == 'city' ) && $fromAuditLog!='Yes'){ ?> active<?php }?>">
                <a href="#">
                    <i class="fa fa-map"></i><span>GEOGRAPHICS</span>
                    <i class="fa fa-caret-down pull-right"></i>
                </a>
                <ul class="treeview-menu">
                	<?
                    	if(CommonFunctions::isAccessible('RM_Country') && $fromAuditLog!='Yes'){
					?>
                    <li><a href="<?= \yii\helpers\Url::to(['/country/country']) ?>" class="<?php if(Yii::$app->controller->id == 'country'){ ?> active<?php }?>"><span class="fa fa-map-marker"></span>COUNTRY</a></li>
                    <? } ?>
                    
                    <?
                    	if(CommonFunctions::isAccessible('RM_State') && $fromAuditLog!='Yes'){
					?>
                    <li><a href="<?= \yii\helpers\Url::to(['/state/state']) ?>" class="<?php if(Yii::$app->controller->id == 'state'){ ?> active<?php }?>"><span class="fa fa-location-arrow"></span>STATE</a></li>
                    <? } ?>
                    
                     <?
                    	if(CommonFunctions::isAccessible('RM_City') && $fromAuditLog!='Yes'){
					?>
                    <li><a href="<?= \yii\helpers\Url::to(['/city/city']) ?>" class="<?php if(Yii::$app->controller->id == 'city'){ ?> active<?php }?>"><span class="fa fa-map-signs"></span>CITY</a></li>
                    <? } ?>
                    
                </ul>
            </li>
        </ul>
         <?php } ?>
        
        <?php if(Yii::$app->user->identity->is_super_admin==1 && $fromAuditLog!='Yes'){ ?>
         <ul class="sidebar-menu">
       		<li class="treeview <?php if(\Yii::$app->controller->id == 'personcompany') { ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/personcompany/personcompany']) ?>" class="<?php if(Yii::$app->controller->id == 'personcompany'){ ?> active<?php }?>"><i class="fa fa-university"></i><span>PERSON COMPANY</span></a>           
            </li>   
       </ul>
       
        <ul class="sidebar-menu">
       		<li class="treeview <?php if(\Yii::$app->controller->id == 'companyadmins') { ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/companyadmins/companyadmins']) ?>" class="<?php if(Yii::$app->controller->id == 'companyadmins'){ ?> active<?php }?>"><i class="fa fa-user"></i><span>COMPANY ADMINS</span></a>           
            </li>   
       </ul>
       <?php } ?>
       
       <?
			if(CommonFunctions::isAccessible('RM_Role_Management') && $fromAuditLog!='Yes'){
		?>
       
       <ul class="sidebar-menu">
       		<li class="treeview <?php if(\Yii::$app->controller->id == 'rolemanagement') { ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/rolemanagement/rolemanagement']) ?>" class="<?php if(Yii::$app->controller->id == 'rolemanagement'){ ?> active<?php }?>"><i class="fa fa-sitemap"></i><span>ROLE MANAGEMENT</span></a>           
            </li>   
       </ul>
		<?
        	}
		?>       
       <?
       	if((CommonFunctions::isAccessible('RM_Person') || CommonFunctions::isAccessible('RM_Company') || CommonFunctions::isAccessible('RM_Unit') || CommonFunctions::isAccessible('RM_Product') || CommonFunctions::isAccessible('RM_Equipment')) && $fromAuditLog!='Yes'){
	   ?>
       
        <ul class="sidebar-menu">
            <li class="treeview <?php if((\Yii::$app->controller->id == 'person' || \Yii::$app->controller->id == 'unit' || \Yii::$app->controller->id == 'company' || \Yii::$app->controller->id == 'product' || \Yii::$app->controller->id == 'equipment') && $fromAuditLog!='Yes'){ ?> active<?php }?>">
                <a href="#">
                    <i class="fa fa-cogs"></i><span>MASTERS</span>
                    <i class="fa fa-caret-down pull-right"></i>
                </a>
                <ul class="treeview-menu">
                	<?
                    	if(CommonFunctions::isAccessible('RM_Person') && $fromAuditLog!='Yes'){
					?>
                	<li class="treeview-sub <?php if(\Yii::$app->controller->id == 'person'){ ?> active<?php }?>">
                        <a href="<?= \yii\helpers\Url::to(['/person/person']) ?>" class="<?php if(Yii::$app->controller->id == 'person'){ ?> active<?php }?>"><i class="fa fa-user"></i><span>PERSON</span></a>           
                    </li>
                    <?
                    	}
					?>
                    <?
                    	if(CommonFunctions::isAccessible('RM_Company') && $fromAuditLog!='Yes'){
					?>
                    <li class="treeview-sub <?php if(\Yii::$app->controller->id == 'company'){ ?> active<?php }?>">
                        <a href="<?= \yii\helpers\Url::to(['/company/company']) ?>" class="<?php if(Yii::$app->controller->id == 'company'){ ?> active<?php }?>"><i class="fa fa-building"></i><span>COMPANY</span></a>           
                    </li>
                    <?
                    	}
					?>
                    <?
                    	if(CommonFunctions::isAccessible('RM_Unit') && $fromAuditLog!='Yes'){
					?>
                    <li class="treeview-sub <?php if(\Yii::$app->controller->id == 'unit'){ ?> active<?php }?>">
                        <a href="<?= \yii\helpers\Url::to(['/unit/unit']) ?>" class="<?php if(Yii::$app->controller->id == 'unit'){ ?> active<?php }?>"><i class="fa fa-balance-scale"></i><span>UNIT</span></a>           
                    </li>
                    <?
                    	}
					?>
                    <?
                    	if(CommonFunctions::isAccessible('RM_Product') && $fromAuditLog!='Yes'){
					?>
                    <li class="treeview-sub <?php if(\Yii::$app->controller->id == 'product'){ ?> active<?php }?>">
                        <a href="<?= \yii\helpers\Url::to(['/product/product']) ?>" class="<?php if(Yii::$app->controller->id == 'product'){ ?> active<?php }?>"><i class="fa fa-tags"></i><span>PRODUCT</span></a>           
                    </li>
                    <?
                    	}
					?>
                    <?
                    	if(CommonFunctions::isAccessible('RM_Equipment') && $fromAuditLog!='Yes'){
					?>
                    <li class="treeview-sub <?php if(\Yii::$app->controller->id == 'equipment'){ ?> active<?php }?>">
                        <a href="<?= \yii\helpers\Url::to(['/equipment/equipment']) ?>" class="<?php if(Yii::$app->controller->id == 'equipment'){ ?> active<?php }?>"><i class="fa fa-wrench"></i><span>EQUIPMENT</span></a>           
                    </li>       
                    <?
                    	}
					?>
                </ul>
            </li>
        </ul>
        
        <?
        	}
		?>
       
		<?
       	 if(CommonFunctions::isAccessible('RM_MPR') && $fromAuditLog!='Yes'){
        ?>
       <ul class="sidebar-menu">
       		<li class="treeview <?php if((\Yii::$app->controller->id == 'mprdefinition' || \Yii::$app->controller->id == 'billofmaterial' || \Yii::$app->controller->id == 'equipmentmap' || \Yii::$app->controller->id == 'minstructions' || \Yii::$app->controller->id == 'mprapprovals' ) && $mode!='bpr'){ ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/mprdefinition/mprdefinition']) ?>" class="<?php if((Yii::$app->controller->id == 'mprdefinition' || \Yii::$app->controller->id == 'billofmaterial' || \Yii::$app->controller->id == 'equipmentmap' || \Yii::$app->controller->id == 'minstructions' || \Yii::$app->controller->id == 'mprapprovals')  && $mode!='bpr'){ ?> active<?php }?>"><i class="fa fa-book"></i><span>MPR DEFINITION</span></a>           
            </li>   
       </ul>
		<?
        }
		?>       
        <?
       	 if(CommonFunctions::isAccessible('RM_BPR') && $fromAuditLog!='Yes'){
        ?>
       <ul class="sidebar-menu">
           	<li class="treeview <?php if(\Yii::$app->controller->id == 'bprrecords' || \Yii::$app->controller->id == 'bprapprovals'  || ((\Yii::$app->controller->id == 'billofmaterial' || \Yii::$app->controller->id == 'equipmentmap') && $mode=='bpr')) { ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/bprrecords/bprrecords']) ?>" class="<?php if(\Yii::$app->controller->id == 'bprrecords' || \Yii::$app->controller->id == 'billofmaterial' || \Yii::$app->controller->id == 'equipmentmap' || \Yii::$app->controller->id == 'bprapprovals' || $mode=='bpr'){ ?> active<?php }?>"><i class="fa fa-list"></i></i><span>BATCH PRODUCTION RECORDS</span></a>           
            </li>
        </ul>
        <?
        	}
		?>
        <?
       	 if(CommonFunctions::isAccessible('RM_Audit_Log')){
        ?>
        <ul class="sidebar-menu">
           	<li class="treeview <?php if(\Yii::$app->controller->id == 'activitylog') { ?> active<?php }?>">
              <a href="<?= \yii\helpers\Url::to(['/activitylog/activitylog']) ?>" class="<?php if(Yii::$app->controller->id == 'activitylog'){ ?> active<?php }?>"><i class="fa fa-newspaper-o"></i></i><span>AUDIT LOG</span></a>           
            </li>
        </ul>
       <?
       	}
	   ?>
       
      
    </section>

</aside>
