<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprdefinition\models\Mprdefination */

$this->title = 'MPR DEFINITION DETAILS:'.$model->MPR_version;
$this->params['breadcrumbs'][] = ['label' => 'MPR Definitions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'MPR Definition Details';


$masterMPR = $model->mpr_defination_id_pk;
$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_pk,'bpr_mpr_defination','mpr_defination_id_pk');

$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($model->mpr_defination_id_pk);
$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($model->mpr_defination_id_pk);
?>

<div class="mprdefination-view row">
	<div class="col-sm-12 col-md-12 col-xs-12">
    <?php if($rflag=='Yes'){ ?>
    	<div class="alert alert-warning alertCust">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp; This record is approved. So it is sealed & in readonly mode.
        </div>
    <?php }elseif($isMDeleted=='1'){?> 
    	<div class="alert alert-warning alertCust">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp; This record is deleted. So it is sealed & in readonly mode.
        </div>	
	<?php } ?>
     <?php if($pflag=="Yes"){?>
    	<div class="alert alert-danger alertCust">
        	<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp; Product related to this MPR is deleted.
        </div>
    <?php }if($eflag=="Yes"){?>
    	<div class="alert alert-danger alertCust">
        	<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp; Equipment related to this MPR is either expired or deleted.
        </div>
	<?php } ?>
    <?php \yii\widgets\Pjax::begin(); ?>
	<ul id="custMyTab" class="nav nav-tabs">
      <li <?php if($activetab=='' || $activetab=='coverpage' ){ ?> class="active" <?php } ?>><a href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=coverpage&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>">Cover Page</a></li>
      <li <?php if($activetab=='billofmaterials'){ ?> class="active" <?php } ?>><a href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=billofmaterials&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>" >Bill Of Materials</a></li>
       <li <?php if($activetab=='formulation'){ ?> class="active" <?php } ?>><a href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=formulation&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>">Formulations</a></li>
      <li <?php if($activetab=='equipments'){ ?> class="active" <?php } ?> ><a  href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=equipments&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>">Equipment</a></li>
      <li <?php if($activetab=='manufacturingInst'){ ?> class="active" <?php } ?> ><a href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=manufacturingInst&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>">Manufacturing Instructions</a></li>
      <li <?php if($activetab=='mprApprovals'){ ?> class="active" <?php } ?> ><a href="<?=Yii::$app->homeUrl;?>mprdefinition/mprdefinition/view?id=<?=$_GET['id'];?>&tab=mprApprovals&al=<?=isset($_GET['al'])?$_GET['al']:'0';?>">MPR Approvals</a></li>
    </ul>
	
	<div class="tab-content cust-my-tab">
    	<div id="coverPage" class="tab-pane fade <?php if($activetab=='' || $activetab=='coverpage' ){ ?>in active<?php } ?>">
      		<div class="myTab col-sm-12 col-md-12 col-xs-12" style="padding-left:0">
            	<?= $this->render('_coverPage', [
                	'model' => $model,
            	]) ?>
            </div>
         </div>
         <div id="billofMaterials" class="tab-pane fade <?php if($activetab=='billofmaterials'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?php if($activetab=='billofmaterials'){ ?>
                <?=$this->render('_billOfMaterials', [
                	'model' => $model,
					'dataProvider' => $dataProvider,
					'searchModel' => $searchModel,
            	]) ?>
                <?php } else{ ?> 
				<img src="<?=Yii::$app->homeUrl;?>images/indicator.gif" /> Processing...
				<?php } ?>
            </div>
         </div>
         
         <div id="formulation" class="tab-pane fade <?php if($activetab=='formulation'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?php if($activetab=='formulation'){ ?>
                <?=$this->render('_formulations', [
                	'model' => $model,
					'formuladataProvider' => $formuladataProvider,
					'formulasearchModel' => $formulasearchModel,
            	]); ?>
                <?php }else{ ?>
                <img src="<?=Yii::$app->homeUrl;?>images/indicator.gif" /> Processing...
                <?php } ?>
            </div>
         </div>
         
          <div id="equipment" class="tab-pane fade <?php if($activetab=='equipments'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?php if($activetab=='equipments'){ ?>
            	<?=$this->render('_equipments', [
                	'model' => $model,
					'eqpdataProvider' => $eqpdataProvider,
					'eqpsearchModel' => $eqpsearchModel,
            	]);?>
                <?php }else{ ?>
                <img src="<?=Yii::$app->homeUrl;?>images/indicator.gif" /> Processing...
                <?php } ?>
            </div>
         </div>
          <div id="manufacturingInst" class="tab-pane fade <?php if($activetab=='manufacturingInst' ){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?php if($activetab=='manufacturingInst' ){ ?>
            	<?= $this->render('_manufacturingInst', [
                	'model' => $model,
					'minstdataProvider' => $minstdataProvider,
					'minstsearchModel' => $minstsearchModel,
            	]);?>
                <?php }else{ ?>
                <img src="<?=Yii::$app->homeUrl;?>images/indicator.gif" /> Processing...
                <?php } ?>
            </div>
         </div>
          <div id="mprApprovals" class="tab-pane fade <?php if($activetab=='mprApprovals' ){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?php if($activetab=='mprApprovals' ){ ?>
            	<?=$this->render('_mprApprovals', [
                	'model' => $model,
					'mapprdataProvider' => $mapprdataProvider,
					'mapprsearchModel' => $mapprsearchModel,
            	]);?>
                <?php }else{ ?>
                <img src="<?=Yii::$app->homeUrl;?>images/indicator.gif" /> Processing...
                <?php } ?>
            </div>
         </div>
         <?php yii\widgets\Pjax::end() ?>
      </div>
    </div>
</div>

<!-- Modal Starts here-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal Ends here-->

<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile(Yii::$app->homeUrl.'js/yii.gridView.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/mprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('$(document).on("pjax:timeout", function(event) {
  // Prevent default timeout redirection behavior
  event.preventDefault()
});');
?>