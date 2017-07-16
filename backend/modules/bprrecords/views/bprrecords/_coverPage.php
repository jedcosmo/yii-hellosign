<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;


$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_definition_id_fk);
$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_pk);


$authorClass = ''; $authorTitle = '';
$unitClass = ''; $unitTitle = '';
$companyClass = ''; $companyTitle = '';

$isAuthorDeleted = checkCommonFunctions::check_If_Record_Deleted($MPRDetails['author'],'bpr_person','person_id_pk');
if($isAuthorDeleted=="Yes")
{
	$authorClass = 'error';
	$authorTitle = 'This person is deleted';
}
	
$isMprUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($MPRDetails['MPR_unit_id'],'bpr_unit','unit_id_pk');
if($isMprUnitDeleted=="Yes")
{
	$unitClass = 'error';
	$unitTitle = 'This unit is deleted';
}
	
$isCompanyDeleted = checkCommonFunctions::check_If_Record_Deleted($MPRDetails['company_id_fk'],'bpr_company','company_id_pk');
if($isCompanyDeleted=="Yes")
{
	$companyClass = 'error';
	$companyTitle = 'This company is deleted';
}
?>
<style type="text/css">
.statusTableBorder{ border: 1px solid #CCCCCC!important;}
.statusTableTD{  border: 1px solid #CCCCCC!important; } 
.statsuTableTH{ background-color:#868686; color:#FFFFFF; border: 1px solid #CCCCCC!important;}
</style>
<label class="col-sm-3">Batch #</label>
<div class="col-sm-8"><?=$model->batch;?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Lot #</label>
<div class="col-sm-8"><?=$model->lot_hash;?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Manufacturing Date</label>
<div class="col-sm-8"><?php if($rflag=='Yes'){ echo date("m/d/Y H:i:s", strtotime($model->manufacturing_date)).Yii::$app->params['timezoneVar']; } ?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Product Code</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($model->product_code),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">MPR Version #</label>
<div class="col-sm-8"><?=$MPRDetails['MPR_version'];?></div>
<div class="col-sm-12"><br/></div>
	
<label class="col-sm-3">Part #</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['product_part']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Author</label>
<div class="col-sm-8"><span class="<?=$authorClass;?>" title="<?=$authorTitle;?>"><?=wordwrap(getCommonFunctions::getPersonName($MPRDetails['author']),50,"<br/>",1);?></span></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Product Name</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['product_name']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Formulation Id</label>
<div class="col-sm-8"><?=wordwrap($MPRDetails['formulation_id'],50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Product Strength</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['product_strength']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Batch Size</label>
<div class="col-sm-8"><?=wordwrap($MPRDetails['batch_size'],50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Unit</label>
<div class="col-sm-8"><span class="<?=$unitClass;?>" title="<?=$unitTitle;?>"><?=wordwrap(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']),50,"<br/>",1);?></span></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Theoritical Yield</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['theoritical_yield']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Company</label>
<div class="col-sm-8"><span class="<?=$companyClass;?>" title="<?=$companyTitle;?>"><?=wordwrap(getCommonFunctions::getFieldNameValue("bpr_company","name","company_id_pk",$MPRDetails['company_id_fk']),50,"<br/>",1);?></span></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Purpose</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['purpose']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<label class="col-sm-3">Scope</label>
<div class="col-sm-8"><?=wordwrap(htmlentities($MPRDetails['scope']),50,"<br/>",1);?></div>
<div class="col-sm-12"><br/></div>

<?php $curStatus = getCommonFunctions::get_BPR_current_status($model->bpr_id_pk);
if($curStatus!='' && strlen($curStatus)>0) { 
	$outputAll = getCommonFunctions::get_BPR_all_status_approver($model->bpr_id_pk);
?>
<label class="col-sm-3">BPR Status</label>
<div class="col-sm-8">
	<table class="table table-striped table-bordered statusTableBorder" width="100%">
    	<thead>
    	<tr class="statusTableTD">
        	<th class="statsuTableTH">Status</th>
            <th class="statsuTableTH">Status Approver</th>
            <th class="statsuTableTH">Time Stamp</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($outputAll) && count($outputAll)>0){ 
			foreach($outputAll as $ok=>$ov){
		?>
        <tr class="statusTableTD">
        	<td class="statusTableTD"><?=$ov['status'];?></td>
            <td class="statusTableTD"><?=$ov['personname'];?></td>
            <td class="statusTableTD"><?=$ov['dttm'].Yii::$app->params['timezoneVar'];?>
            	<?php if($ov['signatureDoc']!=''){ echo "<br/>".$ov['signatureDoc']; } ?>
            </td>
        </tr>
       <?php }} ?>
       </tbody>
    </table>
</div>
<div class="col-sm-12"><br/></div>
<?php } ?>
