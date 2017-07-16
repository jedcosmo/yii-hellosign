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

	<div class="row" style="min-height: 700px">
		<div class="col-md-8">
		<?php $form = ActiveForm::begin(['options' => ['id' => 'form','autocomplete' => 'off']]); ?>
			<table class="table table-bordered">
				<tr>
					<td style="width:30%">First Name</td><td> <input type="text" class="form-control" id="firstName" name="firstname" value="<?= $this->params['data']['info']['first_name'] ?>"></td>
				</tr>
				<tr>
					<td>Last Name</td><td><input type="text" class="form-control" id="lastName" name="lastname" value="<?= $this->params['data']['info']['last_name'] ?>"></td>
				</tr>
				<tr>
					<td>Address</td><td><input type="text" class="form-control" id="address" name="address" value="<?= $this->params['data']['info']['address'] ?>"></td>
				</tr>
				<tr>
					<td>State</td>
					<td><input type="text" class="form-control" id="state" name="state" value="<?= $this->params['data']['info']['state'] ?>">
						<!-- <select class="form-control"  id="states" name="state">
					    	<option> - Select - </option>

					    	 <?php //foreach ($this->params['data']['states'] as $key => $value) {  ?>
					    	 	<?php //$currentState = ($this->params['data']['currState'] == $value['state_id_pk']) ? 'selected' : '' ?>
					    	 	<?php //echo "<option " . $currentState . " value='" . $value['state_id_pk'] . "'>" . $value['name'] . "</option>"  ?>
					    	 <?php //} ?>
					    </select>	 -->

					</td>
				</tr>
				<tr>
					<td>City <span class="loading" style="display: none"><small> Loading... </small><img src="<?= Yii::$app->homeUrl;?>atlas/img/loading.gif"></span></td>
					<td>
  						<input type="text" class="form-control" id="city" name="city" value="<?= $this->params['data']['info']['city'] ?>">

						<!-- <select class="form-control"  id="city" name="city">
					    	<option> - Select - </option>

					    	 <?php //foreach ($this->params['data']['city'] as $key => $value) {  ?>
					    	 	<?php //$currentCity = ($this->params['data']['currCity'] == $value['city_id_pk']) ? 'selected' : '' ?>
					    	 	<?php //echo "<option " . $currentCity . "  value='" . $value['city_id_pk'] . "'>" . $value['name'] . "</option>"  ?>
					    	 <?php //} ?>
					    </select>	 -->

					</td>
				</tr>
				<tr>
					<td>Email</td><td><input type="email" class="form-control" id="email" name="email" value="<?= $this->params['data']['info']['email'] ?>"></td>
				</tr>
				<tr>
					<td>Zipcode</td><td><input type="text" class="form-control" id="zipcode" name="zipcode" value="<?= $this->params['data']['info']['zipcode'] ?>"></td>
				</tr>
				<tr>
					<td>Email</td><td><input type="email" class="form-control" id="email" name="email" value="<?= $this->params['data']['info']['email'] ?>"></td>
				</tr>
				<tr>
					<td>Username</td><td><input type="text" class="form-control" id="username" name="username" value="<?= $this->params['data']['info']['username'] ?>"></td>
				</tr>
				<tr>
					<td>Phone Number</td><td><input type="text" class="form-control" id="phonenumber" name="phonenumber" value="<?= $this->params['data']['info']['phone_number'] ?>"></td>
				</tr>
				<tr>
					<td>Password</td><td><input type="password" class="form-control" id="password" name="password" value=""></td>
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

			<input type="hidden" class="form-control" id="client_id" name="client_id" value="<?= $this->params['data']['info']['id'] ?>">


	        	<button type="submit" class="btn btn-default btn-warning btn-sm">Update</button>
	        	<a href="<?= Yii::$app->homeUrl;?>atlas/atlas/clients" class="btn btn-default btn btn-sm">Back</a>
	        	<a onclick="deleteClient(<?= $this->params['data']['info']['id'] ?>)" class="btn btn-default btn btn-danger btn-sm">Delete</a>


	        	<?php ActiveForm::end(); ?>

	        	<br/>
	        	<br/>

		</div>
	</div>
	
      
</div>

</div>


<?= $this->render('footer') ?>