<?php 
        
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\city\models\City;
use backend\modules\state\models\State;
use backend\modules\country\models\Country;


?>

<div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header pull-left">Atlas Admin</h1>
                </div>
            </div>

           <!--  <div class="row">
                <div class="col-lg-12 col-xs-12">
                   <?php $form = ActiveForm::begin(['options' => ['id' => 'form','autocomplete' => 'off']]); ?>

                        <div class="form-group">
                            <label for="name" class="name">Name</label>
                            <input type="text" name="lastname" placeholder="Last Name" size="16" required> <input type="text" name="firstname" placeholder="First name" id="firstname" size="16" required> <input type="text" name="middlename" placeholder="Middle name" id="middlename" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="phone">Phone</label>
                            <input type="number" name="phone" placeholder="Enter Phone" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="address" class="address">Address</label> 
                            <input type="text" name="address" placeholder="Enter Address" size="45" required>
                        </div>
                        <div class="form-group">
                            <label for="state" class="state">State</label>
                            <input type="text" name="state" placeholder="Enter State" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="city" class="city">City</label>
                            <input type="text" name="city" placeholder="Enter City" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="zip" class="zip">Zip Code</label>
                            <input type="number" name="zip" placeholder="Enter Zip Code" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="email">Email Address</label>
                            <input type="text" name="email" placeholder="test@batman.com" size="35" type="email" required>
                        </div>
                        <div class="form-group">
                            <label for="username" class="username">Username</label>
                            <input type="text" name="username" placeholder="Username" size="16" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="password">Password</label>
                            <input type="password" name="password" placeholder="Password" size="16" required>
                        </div>
                        <div class="form-group" style="text-align: right;">
                            <button type="submit" form="form" value="Submit" class="button">Submit</button>
                        </div>
                  <?php ActiveForm::end(); ?>                 
                </div>
            </div> -->
        </div>