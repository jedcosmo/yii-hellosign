<?php
/* @var $this yii\web\View */
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use yii\grid\GridView;

$this->title = 'Welcome '.Yii::$app->user->identity->user_name_person.' ('.getCommonFunctions::getPersonDetails(Yii::$app->user->identity->person_id_pk)['name'].")"; 
$bprFlag = 'No';

?>
<!-- Main content -->
<section class="content">
<div class="row">
<?php if(CommonFunctions::isAccessible('RM_Dashboard')){ ?>
	<div class="col-sm-12">
    	<div class="panel panel-primary">
        	<div class="panel-heading">Approved BPR Material Cost</div>
        	<div class="panel-body">
          		<div id="BPR_Produt_Table" class="dashboardDiv">
            		<table class="table table-striped table-bordered dsbtbl" style="margin-bottom:0px;width:100%;">
            			<thead>
                            <tr>
                                <td width="30" class="dashboardTblHead">Product Code</td>
                                <td width="30" class="dashboardTblHead">BPR</td>
                                <td width="40" class="dashboardTblHead">Material Cost</td>
                            </tr>
                        </thead>
                		<tbody>
							<?php  $approvedBPRS = array();
                            if(isset($BPRS) && count($BPRS)>0){
                                foreach($BPRS as $bk=>$bv){
                                    $curStatus = getCommonFunctions::get_BPR_current_status($bv['bpr_id_pk']);
                                    if(isset($curStatus) && $curStatus=='Approved') { 
                                    $approvedBPRS[$bv['mpr_definition_id_fk']] = $bv['batch'];
                                    $bprFlag = 'Yes';
                                    $params = [':mpr_defination_id_fk'=>$bv['mpr_definition_id_fk'], ':isDeleted' => '0'];
                                    $mCosts = Yii::$app->db->createCommand('SELECT SUM(qty_branch*price_per_unit) as mcost FROM bpr_bill_of_material WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted', $params)->queryOne();
                            ?>		
                            <tr>
                                <td width="30"><?=$bv['product_code'];?></td>
                                <td width="30"><?=$bv['batch'];?></td>
                                <td width="40"><?=number_format($mCosts['mcost'],2,".",",");?></td>
                            </tr>
							<?php } }} ?>
					
							<?php if($bprFlag == 'No'){ ?>
                                <tr>
                                    <td colspan="3">No records found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
        		</div>
       		</div>
        </div>
     </div>
     <div class="col-sm-12">
     	<div class="panel panel-primary">
        	<div class="panel-heading">Batch Cost and Pricing</div>
          	<div class="panel-body">
                <div class="col-sm-4" style="padding-left:0px;">
                    <select class="form-control" id="dsbh_bpr_batch" name="dsbh_bpr_batch" onchange="getBillOfMaterials();">
                        <option value="">---Select Batch---</option>
                        <?php  if(isset($BPRS) && count($BPRS)>0){
                               foreach($BPRS as $bk=>$bv){
                                  $curStatus = getCommonFunctions::get_BPR_current_status($bv['bpr_id_pk']);
                                  if(isset($curStatus) && $curStatus=='Approved') { 
                        ?>
                            <option value="<?=$bv['mpr_definition_id_fk'];?>"><?=$bv['batch'];?></option>
                        <?php  } } } ?>
                    </select>
                </div>
                <div class="col-sm-12" id="bom_list" name="bom_list" style="padding-left:0px;padding-right:0px;"></div>
        	</div>
        </div>
     </div>
    <?php } ?>
    
    <?php if(Yii::$app->user->identity->is_super_admin==1){ ?>
    <div class="col-sm-12">
    	<div class="panel panel-primary">
        	<div class="panel-heading">Count Statistics of All Companies</div>
          	<div class="panel-body">
				<?= $this->render('_dashboard_person_count', [
                    'model' => $model,
                ]) ?>
    		</div>
     	</div>
    </div>
    <?php } ?>
</section>

<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/dashboard.js"></script>
<style type="text/css">
.custPanelBG{ background-color:#63A5CC!important;}
</style>
        
