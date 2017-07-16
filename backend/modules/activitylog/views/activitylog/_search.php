<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\person\models\Person;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\activitylog\models\ActivityLogSearch */
/* @var $form yii\widgets\ActiveForm */

$search_by = isset($_GET['search_by'])?$_GET['search_by']:'';
$search_for = isset($_GET['search_for'])?$_GET['search_for']:'';
$from_date = isset($_GET['from_date'])?$_GET['from_date']:'';
$to_date = isset($_GET['to_date'])?$_GET['to_date']:'';

?>
<style type="text/css">
.input-group .input-group-addon { background-color:#eeeeee!important;}
/*.ui-datepicker-header{ color:#666666!important; } */ 
.mytxt{ background-color:#FFFFFF!important;}
.ui-datepicker-header{  color:#666666!important;  }  
#ui-datepicker-div{
	opacity: 1 !important;
	position: fixed !important;
	top: 181px !important;
	z-index: 1;
}
.ui_tpicker_minute{
	padding-left:7px;
	padding-right:7px;
}
.ui_tpicker_hour{
	padding-left:7px;
	padding-right:7px;
}
.ui_tpicker_second{
	padding-left:7px;
	padding-right:7px;
}
</style>
<div class="activity-log-search col-sm-12 col-md-8">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
		'id' => 'auditsearch_frm',
		'options' => ['autocomplete' => 'off'],
    ]); ?>
    
    <div class="row">
    <label class="col-sm-3 text-right">Search By</label>
    <div class="col-sm-9">
        <select name="search_by" id="search_by" class="form-control" onchange="javascript:showPersonId(this.value);">
        	<option value="">---Select---</option>
            <option value="Person_ID" <?php if($search_by=='Person_ID'){ ?> selected="selected" <?php } ?>>Person ID</option>
            <option value="Action" <?php if($search_by=='Action'){ ?> selected="selected" <?php } ?>>Action</option>
            <option value="Date_Time" <?php if($search_by=='Date_Time'){ ?> selected="selected" <?php } ?>>Date & Time</option>
            <option value="Screen_Name" <?php if($search_by=='Screen_Name'){ ?> selected="selected" <?php } ?>>Screen Name</option>
        </select>
    </div>
    </div>
    
    <div class="col-sm-12">&nbsp;</div>
    
    <div class="row" id="person_id_div" <?php if($search_by!='Person_ID'){ ?> style="display:none;" <?php  } ?>>
    <label class="col-sm-3 text-right">Search For</label>
    <div class="col-sm-9">
    <?php 
		$person_id = isset($_GET['search_for_person'])?$_GET['search_for_person']:'';
		$pesonsArr = Person::find()->where(['super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('person_id_pk ASC')->all();
		if(is_array($pesonsArr) && count($pesonsArr)>0) { 
	?>
    	 <select name="search_for_person" id="search_for_person" class="form-control">
        	<option value="">---Select---</option>
	<?php	for($i=0;$i<count($pesonsArr);$i++){ 
			if(in_array($pesonsArr[$i]['person_id_pk'],$availablePersons)) {
	?>
            <option value="<?=$pesonsArr[$i]['person_id_pk'];?>" <?php if($person_id==$pesonsArr[$i]['person_id_pk']){ ?> selected="selected" <?php } ?>><?= $pesonsArr[$i]['person_id_pk'].'   [ '.$pesonsArr[$i]['first_name'].' '.$pesonsArr[$i]['last_name'].' ] '; ?></option>
	<?php } }?>
		 </select>  
	<?php  } ?>
    </div>
    </div>
    
    <div id="action_div" class="row" <?php if($search_by!='Action'){ ?> style="display:none;" <?php  } ?>>
    <label class="col-sm-3 text-right">Search For</label>
    <div class="col-sm-9">
    <?php $search_for_action = isset($_GET['search_for_action'])?$_GET['search_for_action']:''; 
		$screenActions = Yii::$app->params['audit_log_action'];
		asort($screenActions);
	?>
     <select name="search_for_action" id="search_for_action" class="form-control">
        <option value="">---Select---</option>
        <?php foreach($screenActions as $k=>$v) {
			if(in_array($v,$availableActions)){
		 ?>
        <option value="<?=$v;?>" <?php if($search_for_action==$v){ ?> selected="selected" <?php } ?>><?=$v;?></option>
        <?php } } ?>
     </select>  
    </div>
    </div>
    
    
    <div id="screen_div" class="row" <?php if($search_by!='Screen_Name'){ ?> style="display:none;" <?php  } ?>>
    <label class="col-sm-3 text-right">Search For</label>
    <div class="col-sm-9">
    <?php $search_for_Screen_Name = isset($_GET['search_for_Screen_Name'])?$_GET['search_for_Screen_Name']:''; 
				
		$screenArray = Yii::$app->params['audit_log_screen_name'];
		asort($screenArray);
	?>
     <select name="search_for_Screen_Name" id="search_for_Screen_Name" class="form-control">
        <option value="">---Select---</option>
        <?php foreach($screenArray as $k=>$v) { 
			if(in_array($v,$availableScreens)) {
		?>
        <option value="<?=$v;?>" <?php if($search_for_Screen_Name==$v){ ?> selected="selected" <?php } ?>><?=$v;?></option>
        <?php } } ?>
    </select>  
    </div>
    </div>
    
    <div id="search_for_div" class="row" <?php if($search_by!='Date_Time' && $search_by!=''){ ?> style="display:none;" <?php  } ?>>
    <label class="col-sm-3 text-right">Search For</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="search_for" id="search_for" value="<?=$search_for;?>">
    </div>
    </div>
    
    <div class="col-sm-12">&nbsp;</div>
    
    <div class="row">
    <label class="col-sm-3 text-right">From</label>
    <div class="col-sm-3">
    	<div class="input-group">
            <input type="text" class="form-control mytxt" name="from_date" id="from_date" value="<?=$from_date;?>" placeholder="" readonly="readonly">
            <a class="input-group-addon" href="javascript:void(0);" onclick="$('#from_date').focus();">
                <i class="fa fa-calendar"></i>
            </a>
        </div>
        <div class="error" for="from_date" generated="true"></div>
    </div>

    <label class="col-sm-1 text-right">To</label>
    <div class="col-sm-3">
    	<div class="input-group">
            <input type="text" class="form-control mytxt" name="to_date" id="to_date" value="<?=$to_date;?>" placeholder="" readonly="readonly">
            <a class="input-group-addon" href="javascript:void(0);" onclick="$('#to_date').focus();">
                <i class="fa fa-calendar"></i>
            </a>
        </div>
        <div class="error" for="to_date" generated="true"></div>
    </div>
    </div>
	<div class="col-sm-12">&nbsp;</div>
    
    <div class="row">
    <label class="col-sm-3 text-right">&nbsp;</label>
    <div class="col-sm-6">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary', 'onclick' => 'javascript:return checkCriteria();']) ?> 
        &nbsp;&nbsp;
        <?= Html::Button('Reset', ['class' => 'btn btn-default', 'onclick' => 'javascript:resetSearch();']) ?>
    </div>
    <div class="col-sm-4">&nbsp;</div>
    </div>
	<div class="col-sm-12">&nbsp;</div>
    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.css");
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery-ui-1.11.0.custom/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery-ui-timepicker-addon.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/auditlogsearch.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
