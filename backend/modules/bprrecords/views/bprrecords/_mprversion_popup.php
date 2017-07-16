<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */

$this->title = 'Select Master Production Record';

?>
<style type="text/css">
.wrapMyTD{ word-wrap:break-word; }
</style>
<div class="modal-header text-primary">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h4 class="modal-title">SELECT MASTER PRODUCTION RECORD</h4>
</div>
<div class="modal-body">
    <div class="row custm-tbl">
    	<div class="col-sm-12">
        	<div class="col-sm-8">
                <label class="col-sm-3" style="padding-left:0">Product Code</label>
                <div class="col-sm-5" style="padding-left:0">
                    <?=$product_code;?>
                    <input type="hidden" name="prdcode_hid" id="prdcode_hid" value="<?=$product_code;?>">
                </div>
            </div>
            <div class="col-sm-12"><br/></div>
            

            <div class="col-sm-8">
            	<label class="col-sm-3" style="padding-left:0">Part #</label>
                <div class="col-sm-4" style="padding-left:0;padding-right:0;">
                    <input type="text" class="form-control" name="part_search_txt" id="part_search_txt">
                </div>
                <div class="col-sm-5 text-left" style="padding-right:0;">
                <button type="button" class="btn btn-primary btn-flat" onclick="javascript:searchOnPart('<?=$product_code;?>');">SEARCH</button>
                <button type="button" class="btn btn-default btn-flat" onclick="javascript:searchClear('<?=$product_code;?>');">CLEAR</button>
                </div>
            </div>
            <div class="col-sm-4" style="padding-left:0;">
            	<label class="col-sm-4">Sort By:</label>
                <div class="col-sm-8" style="padding-right:0;">
                	<select class="form-control" id="sort_mpr" name="sort_mpr" style="padding:2px;" onchange="javascript:sortResultMPR()">
                    	<option value="product_code">Product Code</option>
                        <option value="MPR_version">MPR Version</option>
                        <option value="product_part">Part #</option>
                        <option value="author">Person Name</option>
                        <option value="product_name">Product Name</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12"><br/></div>
            
            <div class="col-sm-12 tbl">
            	<table width="100%" class="table table-bordered table-striped table-responsive" border="0" cellpadding="5" cellspacing="5" style="border:1px solid #FFFFFF!important;">
                	<tr>
                    	<th width="20%">Product Code</th>
                        <th width="15%">MPR Version</th>
                        <th width="10%">Part #</th>
                        <th width="15%">Person Name</th>
                        <th width="20%">Product Name</th>
                    </tr>
                    <tbody id="myPartTable">
                    <?php
						
                     if(is_array($mprVersion) && count($mprVersion)>0){
			
						foreach($mprVersion as $k=>$v){
							$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($k);
							$MFlag = checkCommonFunctions::check_if_BPR_already_exists($MPRDetails['mpr_defination_id_pk'], $MPRDetails['product_code'], $MPRDetails['MPR_version']);
					?>
                    	<tr>
                            <td class="wrapMyTD" width="20%"><a href="javascript:void(0);" onclick="javascript:showBPRDetails('<?=$MPRDetails['mpr_defination_id_pk'];?>','<?=$MPRDetails['MPR_version'];?>');"><?=wordwrap($MPRDetails['product_code'],25,'<br/>',1);?></a></td>
                            <td class="wrapMyTD" width="15%"><?=wordwrap($MPRDetails['MPR_version'],25,'<br/>',1);?></td>
                            <td class="wrapMyTD" width="10%"><?=wordwrap($MPRDetails['product_part'],15,'<br/>',1);?></td>
                            <td class="wrapMyTD" width="20%"><?=wordwrap(getCommonFunctions::getPersonName($MPRDetails['author']),25,'<br/>',1);?></td>
                            <td class="wrapMyTD" width="25%"><?=wordwrap($MPRDetails['product_name'],25,'<br/>',1);?></td>
                        </tr>
                    <?php
						}
					}
					?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	$( "#part_search_txt" ).keypress(function( event ) {
	  if ( event.which == 13 ) {
		 var product_code =  $('#prdcode_hid').val();
		 searchOnPart(product_code);
	  }
	});
});
</script>
