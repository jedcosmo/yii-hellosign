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
                <td width="30" class="dashboardTblHead">MPR(#)</td>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($companiesMPR) && count($companiesMPR)>0){ 
                $mcnt = 0;
                foreach($companiesMPR as $mk=>$mv){ 
                    $mcnt ++;
            ?>
            <tr>
                <td><?=$mcnt;?></td>
                <td><?=$mv['name'];?></td>
                <td><?=$mv['mcnt'];?></td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="3">No records found</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
