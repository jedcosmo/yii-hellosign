<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\bprrecords\models\Bprrecords */

$this->title = 'BATCH PRODUCTION RECORD: '.$model->batch;
$this->params['breadcrumbs'][] = ['label' => 'BPR Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Batch Production Record';

$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_definition_id_fk);

$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_pk);

$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($model->mpr_definition_id_fk);
$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($model->mpr_definition_id_fk);
?>

<div class="bprrecords-view row">
	<div class="col-sm-12">
     <?php if($rflag=='Yes'){ ?>
    	<div class="alert alert-warning alertCust">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp; This record is approved. So it is sealed & in readonly mode.
        </div>
    <?php }if($pflag=="Yes"){?>
    	<div class="alert alert-danger alertCust">
        	<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp; Product related to this BPR is deleted.
        </div>
    <?php }if($eflag=="Yes"){?>
    	<div class="alert alert-danger alertCust">
        	<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp; Equipment related to this BPR is either expired or deleted.
        </div>
	<?php } ?>
	<ul id="custMyTab" class="nav nav-tabs">
      <li <?php if($activetab=='' || $activetab=='coverpage' ){ ?> class="active" <?php } ?>><a data-toggle="tab" href="#coverPage" onclick="javascript:changeTabs('coverpage');">Cover Page</a></li>
      <li <?php if($activetab=='billofmaterials'){ ?> class="active" <?php } ?>><a data-toggle="tab" href="#billofMaterials" onclick="javascript:changeTabs('billofmaterials');">Bill Of Materials</a></li>
       <li <?php if($activetab=='formulation'){ ?> class="active" <?php } ?>><a data-toggle="tab" href="#formulation" onclick="javascript:changeTabs('formulation');">Formulation</a></li>
      <li <?php if($activetab=='equipments'){ ?> class="active" <?php } ?> ><a data-toggle="tab" href="#equipment" onclick="javascript:changeTabs('equipments');">Equipment</a></li>
      <li <?php if($activetab=='manufacturingInst'){ ?> class="active" <?php } ?> ><a data-toggle="tab" href="#manufacturingInst" onclick="javascript:changeTabs('manufacturingInst');">Manufacturing Instructions</a></li>
      <li <?php if($activetab=='bprApprovals'){ ?> class="active" <?php } ?> ><a data-toggle="tab" href="#bprApprovals" onclick="javascript:changeTabs('bprApprovals');">Executed BPR Approvals</a></li>
    </ul>
    
    <div class="tab-content cust-my-tab">
    	<div id="coverPage" class="tab-pane fade <?php if($activetab=='' || $activetab=='coverpage' ){ ?>in active<?php } ?>">
      		<div class="myTab col-sm-12" style="padding-left:0">
            	<?= $this->render('_coverPage', [
                	'model' => $model,
            	]) ?>
            </div>
         </div>
 
    	<div id="billofMaterials" class="tab-pane fade <?php if($activetab=='billofmaterials'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?= $this->render('_billOfMaterials', [
                	'model' => $model,
					'dataProvider' => $dataProvider,
					'searchModel' => $searchModel,
            	]) ?>
            </div>
         </div>
         
          <div id="formulation" class="tab-pane fade <?php if($activetab=='formulation'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
                <?= $this->render('_formulations', [
                	'model' => $model,
					'formuladataProvider' => $formuladataProvider,
					'formulasearchModel' => $formulasearchModel,
            	]) ?>
            </div>
         </div>
         
        <div id="equipment" class="tab-pane fade <?php if($activetab=='equipments'){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
            	<?= $this->render('_equipments', [
                	'model' => $model,
					'eqpdataProvider' => $eqpdataProvider,
					'eqpsearchModel' => $eqpsearchModel,
            	]) ?>
            </div>
         </div>
         
         <div id="manufacturingInst" class="tab-pane fade <?php if($activetab=='manufacturingInst' ){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
            	<?= $this->render('_manufacturingInst', [
                	'model' => $model,
					'minstdataProvider' => $minstdataProvider,
					'minstsearchModel' => $minstsearchModel,
					'mInstructionSteps' => $mInstructionSteps,
            	]) ?>
            </div>
         </div>
         
          <div id="bprApprovals" class="tab-pane fade <?php if($activetab=='bprApprovals' ){ ?>in active<?php } ?>">
        	<div class="myTab col-sm-12">
            	<?= $this->render('_bprApprovals', [
                	'model' => $model,
					'bapprdataProvider' => $bapprdataProvider,
					'bapprsearchModel' => $bapprsearchModel,
            	]) ?>
            </div>
         </div>
       
         
    	</div>
    </div>
</div>

<!-- Modal Starts here-->
<div class="modal modal-fullscreen fade" id="myModalBig" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal Ends here-->

<!-- Modal Starts here-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal Ends here-->

<script type="text/javascript">
function changeTabs(tabname)
{
	var id = '<?=$_GET['id'];?>';
	var al = '<?=@$_GET['al'];?>';
	var str1 = '';
	if(al==1)
		str1 = "&al=1";
	window.location.href = '<?=Yii::$app->homeUrl;?>bprrecords/bprrecords/view?id='+id+'&tab='+tabname+str1;
}
</script>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/bprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
