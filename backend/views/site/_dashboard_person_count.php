<?php
/* @var $this yii\web\View */
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use yii\grid\GridView;
/**********************************************/
$params = [':isDeleted' => '0'];
$personCompanies = Yii::$app->db->createCommand('SELECT company_id_pk,name,count(bpr_person.person_id_pk) as pcnt FROM bpr_person_company LEFT JOIN bpr_person ON bpr_person.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk', $params)->queryAll();
/**********************************************/
?>
<div>
<table class="table table-striped table-bordered dsbtbl" style="margin-bottom:0px;width:100%;margin-top:10px;">
    <thead>
        <tr>
            <td width="15" class="dashboardTblHead">#</td>
            <td width="55" class="dashboardTblHead">Company Name</td>
            <td width="30" class="dashboardTblHead">Person Count</td>
        </tr>
    </thead>
    <tbody>
    	<?php if(is_array($personCompanies) && count($personCompanies)>0){ 
			$pcnt = 0;
			foreach($personCompanies as $pk=>$pv){ 
				$pcnt ++;
		?>
        <?php if($pv['pcnt']>5){ ?>
        <tr class="outOfQuota">
            <td class="outOfQuota">
            	<span class="text-danger" title="Account quota exceeded" data-toggle="tooltip" data-placement="top"><i class="fa fa-exclamation"></i></span>&nbsp;
				<?=$pcnt;?>
            </td>
            <td class="outOfQuota"><?=$pv['name'];?></td>
            <td class="outOfQuota"><?=$pv['pcnt'];?></td>
        </tr>
        <?php }else{ ?>
        <tr>
            <td><?=$pcnt;?></td>
            <td><?=$pv['name'];?></td>
            <td><?=$pv['pcnt'];?></td>
        </tr>
        <?php } } } else { ?>
        <tr>
        	<td colspan="3">No records found</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>
<?php 
$params = [':isDeleted' => '0'];
$companiesBPR = Yii::$app->db->createCommand('SELECT company_id_pk,name,count(bpr_batch_processing_records.bpr_id_pk) as bcnt FROM bpr_person_company LEFT JOIN bpr_batch_processing_records ON bpr_batch_processing_records.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk', $params)->queryAll();
?>
<div>
<h4 class="text-primary">Companies BPR(#)</h4>
<input type="text" name="bpr_date_range" id="bpr_date_range" value="" placeholder="Date Range" />
<div id="dsh_bpr_count_div">
	<?=	$this->render('_companiesBPRcount', [
        	'companiesBPR' => $companiesBPR,
        ]);
	?>
</div>
</div>


<?php 
$params = [':isDeleted' => '0'];
$companiesMPR = Yii::$app->db->createCommand('SELECT company_id_pk,name,count(bpr_mpr_defination.mpr_defination_id_pk) as mcnt FROM bpr_person_company LEFT JOIN bpr_mpr_defination ON bpr_mpr_defination.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk', $params)->queryAll();
?>
<div>
<h4 class="text-primary">Companies MPR(#)</h4>
<input type="text" name="mpr_date_range" id="mpr_date_range" value="" placeholder="Date Range" />
<div id="dsh_mpr_count_div">
	<?=	$this->render('_companiesMPRcount', [
        	'companiesMPR' => $companiesMPR,
        ]);
	?>
</div>
</div>

<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$objName = "bpr_date_range";
$this->registerJs("$(function() {
	$('[data-toggle=\"tooltip\"]').tooltip(); 
	var myDtObj = document.getElementsByName('".$objName."');
	$(myDtObj).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			var selectedDate = $('#bpr_date_range').val();
			$.get(SITEROOT+'site/getcompaniesbprcount',{selectedDate:selectedDate},function(data){
				if(data)
				{
					$('#dsh_bpr_count_div').html(data);
				}
			});
		},
	});
	
	var myDtObj2 = document.getElementsByName('mpr_date_range');
	$(myDtObj2).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			var selectedDate = $('#mpr_date_range').val();
			$.get(SITEROOT+'site/getcompaniesmprcount',{selectedDate:selectedDate},function(data){
				if(data)
				{
					$('#dsh_mpr_count_div').html(data);
				}
			});
		},
	});
	
});");
?>
<style>
.ui-daterangepicker-arrows input.ui-rangepicker-input{ height:1.8em!important; }
.ui-daterangepicker-arrows .ui-daterangepicker-prev{ top:8px; }
.ui-daterangepicker-arrows .ui-daterangepicker-next{ top:8px; }
.outOfQuota{ background-color:#FFE1E1!important;}
</style>
