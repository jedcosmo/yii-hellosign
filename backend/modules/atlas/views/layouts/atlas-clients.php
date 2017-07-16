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
<div id="page-wrapper" style="margin-top:50px">

<div class="row wrap-atlas">
	
		
	<div>
		<div class="pull-left"><h2>Client List</h2> </div>
		<div class="pull-right">
		<button type="button" class="btn btn-success"  data-toggle="modal" data-target="#addNewClientModal">Add New Client</button>
		</div>
	</div>
      <table class="table table-bordered">
	    <thead>
	      <tr>
	        <th>First Name</th>
	        <th>Last Name</th>
	        <th>Email</th>
	         <th>Action</th>
	      </tr>
	    </thead>
	    <tbody>

	    <?php foreach ($this->params['data']['clients'] as $key => $value) { ?>

	      <tr>
	        <td><?= $value['first_name'] ?></td>
	        <td><?= $value['last_name'] ?></td>
	        <td><?= $value['email'] ?></td>
	        <td style="width:20%">
	        	<a href="<?= Yii::$app->homeUrl;?>atlas/atlas/view?id=<?= $value['id'] ?>" class="btn btn-default btn-sm">View</a>
	        	<a href="<?= Yii::$app->homeUrl;?>atlas/atlas/edit?id=<?= $value['id'] ?>" class="btn btn-default btn-warning btn-sm">Update</a>
	        	<a onclick="deleteClient(<?= $value['id'] ?>)" class="btn btn-default btn btn-danger btn-sm">Delete</a>

	        	
	        </td>
	      </tr>

	    <?php } ?>
	     

	    </tbody>
	  </table>
</div>

</div>

<!-- Add Modal -->
<div id="addNewClientModal" class="modal fade" role="dialog" novalidate="novalidate">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add New Client</h4>
      </div>
     <?php $form = ActiveForm::begin(['options' => ['id' => 'addClientForm','autocomplete' => 'off']]); ?>
      <div class="modal-body">
       		

       		<div class="row">
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="firstName">First Name</label>
				    <input type="text" class="form-control" id="firstName" name="firstname">
				  </div>
       			</div>
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="lastName">Last Name</label>
				    <input type="text" class="form-control" id="lastName" name="lastname">
				  </div>
       			</div>
       		</div>

       		<div class="row">
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="address">Address</label>
				    <input type="text" class="form-control" id="address" name="address">
				  </div>
       			</div>
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="phoneNumber">Phone Number</label>
				    <input type="text" class="form-control" id="phoneNumber" name="phonenumber">
				  </div>
       			</div>
       		</div>

       		<div class="row">
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="states">State</label>
				    <input type="text" class="form-control" id="state" name="state">
				    <!-- <select class="form-control"  id="states" name="state">
				    	<option> - Select - </option>

				    	 <?php //foreach ($this->params['data']['states'] as $key => $value) {  ?>
				    	 	<?php //echo "<option value='" . $value['state_id_pk'] . "'>" . $value['name'] . "</option>"  ?>
				    	 <?php // } ?>
				    </select> -->

				  </div>
       			</div>
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="state">City <span class="loading" style="display: none"><small> Loading... </small><img src="<?= Yii::$app->homeUrl;?>atlas/img/loading.gif"></span></label>
				    <input type="text" class="form-control" id="city" name="city">
				   <!-- <select class="form-control"  id="city" name="city">
				    	<option> - Select - </option>
				    	
				    </select> -->
				  </div>
       			</div>
       		</div>

       		<div class="row">
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="zipcode">Zipcode</label>
				    <input type="text" class="form-control" id="zipcode" name="zipcode">
				  </div>
       			</div>
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="email">Email Address</label>
				    <input type="email" class="form-control" id="email" name="email">
				  </div>
       			</div>
       		</div>
       		
      		
			  <div class="row">
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="username">Username</label>
				    <input type="text" class="form-control" id="username" name="username">
				  </div>
       			</div>
       			<div class="col-md-6">
       				<div class="form-group">
				    <label for="password">Password</label>
				    <input type="password" class="form-control" id="password" name="password">
				  </div>
       			</div>
       		</div>

   		<div class="validation-status"></div>
			 
			

      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-default">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
       <?php ActiveForm::end(); ?>      
    </div>

  </div>
</div>






<?= $this->render('footer') ?>