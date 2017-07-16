<?php 
        
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\city\models\City;
use backend\modules\state\models\State;
use backend\modules\country\models\Country;


?>
<?= $this->render('header') ?>
<?= $this->render('sidebar') ?>


<!-- Page Content -->
<div id="page-wrapper" style="margin-top:30px">

<div class="row">
	
		
	<div class="row">
		<div class="col-md-12">
			<div class="pull-left"><h3>Client Information</h3> </div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-8">
			<table class="table table-bordered">
				<tr>
					<td style="width:30%">First Name</td><td><?= $this->params['data']['info']['first_name'] ?></td>
				</tr>
				<tr>
					<td>Last Name</td><td><?= $this->params['data']['info']['last_name'] ?></td>
				</tr>
				<tr>
					<td>Address</td><td><?= $this->params['data']['info']['address'] ?></td>
				</tr>
				<tr>
					<td>State</td><td><?= $this->params['data']['info']['state'] ?></td>
				</tr>
				<tr>
					<td>City</td><td><?= $this->params['data']['info']['city'] ?></td>
				</tr>
				<tr>
					<td>Zipcode</td><td><?= $this->params['data']['info']['zipcode'] ?></td>
				</tr>
				<tr>
					<td>Email</td><td><?= $this->params['data']['info']['email'] ?></td>
				</tr>
				<tr>
					<td>Username</td><td><?= $this->params['data']['info']['username'] ?></td>
				</tr>
				<tr>
					<td>Phone Number</td><td><?= $this->params['data']['info']['phone_number'] ?></td>
				</tr>
				<tr>
					<td>Created</td><td><?= $this->params['data']['info']['created'] ?></td>
				</tr>
				<tr>
					<td>Updated</td><td><?= $this->params['data']['info']['updated'] ?></td>
				</tr>
				<tr>
					<td>Last Login</td><td><?= $this->params['data']['info']['last_login'] ?></td>
				</tr>
			</table>



	        	<a href="<?= Yii::$app->homeUrl;?>atlas/atlas/edit?id=<?= $this->params['data']['info']['id'] ?>" class="btn btn-default btn-warning btn-sm">Update</a>
	        	<a href="<?= Yii::$app->homeUrl;?>atlas/atlas/clients" class="btn btn-default btn btn-sm">Back</a>
	        	<a onclick="deleteClient(<?= $this->params['data']['info']['id'] ?>)" class="btn btn-default btn btn-danger btn-sm">Delete</a>

		</div>
	</div>
	
      
</div>

</div>


<?= $this->render('footer') ?>