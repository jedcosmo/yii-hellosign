<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */

if(is_array($mprVersion) && count($mprVersion)>0){

	foreach($mprVersion as $k=>$v){
		$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($k);
?>
	<tr>
		<td><a href="javascript:void(0);" onclick="javascript:showBPRDetails('<?=$MPRDetails['mpr_defination_id_pk'];?>','<?=$MPRDetails['MPR_version'];?>');"><?=$MPRDetails['product_code'];?></a></td>
		<td><?=$MPRDetails['MPR_version'];?></td>
		<td><?=$MPRDetails['product_part'];?></td>
		<td><?=getCommonFunctions::getPersonName($MPRDetails['author']);?></td>
		<td><?=$MPRDetails['product_name'];?></td>
	</tr>
<?php
	}
}else{ ?>
<tr>
	<td colspan="5">No records found</td>
</tr>
<?php } ?>
                    
