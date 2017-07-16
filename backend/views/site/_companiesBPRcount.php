<?php
/* @var $this yii\web\View */
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use yii\grid\GridView;
/**********************************************/

?> 
 <table class="table table-striped table-bordered dsbtbl" style="margin-bottom:0px;width:100%;margin-top:10px;">
        <thead>
            <tr>
                <td width="15" class="dashboardTblHead">#</td>
                <td width="55" class="dashboardTblHead">Company Name</td>
                <td width="30" class="dashboardTblHead">BPR(#)</td>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($companiesBPR) && count($companiesBPR)>0){ 
                $bcnt = 0;
                foreach($companiesBPR as $bk=>$bv){ 
                    $bcnt ++;
            ?>
            <tr>
                <td><?=$bcnt;?></td>
                <td><?=$bv['name'];?></td>
                <td><?=$bv['bcnt'];?></td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="3">No records found</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
