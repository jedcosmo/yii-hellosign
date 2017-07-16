<?php
/* @var $this yii\web\View */
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use yii\grid\GridView;
/**********************************************/
function unitConversionToKG($unit,$qty,$pricePerUnit)
{
	$qtyInKG = 0;
	$pricePerKG = 0;
	$params = [':unit'=>$unit];
	$unitDetails = Yii::$app->db->createCommand('SELECT unit,description,kg_conversion FROM bpr_unit_conversion WHERE unit=:unit', $params)->queryOne();
	if(is_array($unitDetails) && isset($unitDetails['unit']) && $unitDetails['unit']!='')
	{
		$qtyInKG = $qty * $unitDetails['kg_conversion'];
		if($unitDetails['kg_conversion']>0)
			$pricePerKG = $pricePerUnit / $unitDetails['kg_conversion'];
		else
			$pricePerKG = $pricePerUnit;
	}
	else
	{
		$qtyInKG = '--';
		$pricePerKG = '--';
	}
	$result = array('qtyInKG'=>$qtyInKG, 'pricePerKG'=>$pricePerKG);
	return $result;
}
/**********************************************/
?>
<div class="table-responsive">
<table class="table table-striped table-bordered dsbtbl" style="margin-bottom:0px;width:100%;margin-top:10px;">
    <thead>
        <tr>
            <td width="5" class="dashboardTblHead">#</td>
            <td width="15" class="dashboardTblHead">Material Name</td>
            <td width="10" class="dashboardTblHead">Qty/Batch</td>
            <td width="10" class="dashboardTblHead">Qty/Batch Unit</td>
            <td width="15" class="dashboardTblHead">$ Price per unit</td>
            <td width="15" class="dashboardTblHead">Qty/Batch (in kg)</td>
            <td width="15" class="dashboardTblHead">$ Price per Kg</td>
            <td width="15" class="dashboardTblHead">Cost per Batch</td>
        </tr>
    </thead>
    <tbody>
    <?php 
    $cnt = 0;
    if(isset($BOMs) && count($BOMs)>0){
		$totalCostPerBatch = 0;
		$costPerBatch = 0;
        foreach($BOMs as $bk=>$bv){
            $cnt++;
			$unitnm = getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$bv['qb_unit_id_fk']);
			$convertedUnit = unitConversionToKG($unitnm,$bv['qty_branch'],$bv['price_per_unit']);
			
			$batchSize = $MPRDetails['batch_size'];
			$mprUnit = getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']);
			$batchSizeInKG = unitConversionToKG($mprUnit,$batchSize,0);
			
			//$costPerBatch = $convertedUnit['pricePerKG'] * $batchSizeInKG['qtyInKG'];
			$costPerBatch = $convertedUnit['qtyInKG'] * $convertedUnit['pricePerKG'];
			
			$totalCostPerBatch = $totalCostPerBatch + $costPerBatch;
    ?>		
        <tr>
            <td><?=$cnt;?></td>
            <td><?=$bv['material_name'];?></td>
            <td><?=$bv['qty_branch'];?></td>
            <td><?=$unitnm;?></td>
 
            <td><?=($bv['price_per_unit']==0)?($bv['price_per_unit']):(number_format($bv['price_per_unit'],8,".",","));?></td>
         
            <td><?=($convertedUnit['qtyInKG']==0 || is_numeric($convertedUnit['qtyInKG'])==false)?($convertedUnit['qtyInKG']):(number_format($convertedUnit['qtyInKG'],8,".",","));?></td>
            
            <td><?=($convertedUnit['pricePerKG']==0)?($convertedUnit['pricePerKG']):(number_format($convertedUnit['pricePerKG'],8,".",","));?></td>
            
            <td><?=($costPerBatch==0)?($costPerBatch):(number_format($costPerBatch,8,".",","));?></td>
        </tr>
    <?php } ?>
    	<tr class="totalBG">
        	<td>&nbsp;</td>
            <td>Total</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><?=($totalCostPerBatch==0)?($totalCostPerBatch):(number_format($totalCostPerBatch,8,".",","));?></td>
        </tr>
    <?php }else{ ?>
        <tr>
            <td colspan="8">No records found</td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>
<style type="text/css">
.totalBG { background-color:#FFFF00!important; font-weight:bold;}
</style>
