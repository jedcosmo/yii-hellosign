</div>


<!-- Delete Modal -->
<div id="deleteClientModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Client</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this client?</p>
      </div>
      <div class="modal-footer">
        <a class="deleteClient btn btn-default btn-danger" >Delete</a>
         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


<!-- Bootstrap Core JavaScript -->
<script src="<?= Yii::$app->homeUrl;?>atlas/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="<?= Yii::$app->homeUrl;?>atlas/js/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="<?= Yii::$app->homeUrl;?>atlas/js/startmin.js"></script>



<!-- JavaScript Validation -->
<script src="<?= Yii::$app->homeUrl;?>atlas/js/jquery.validate.min.js"></script>
<script src="<?= Yii::$app->homeUrl;?>atlas/js/form-validation.js"></script>
<script src="<?= Yii::$app->homeUrl;?>atlas/js/additional-methods.min.js"></script>
<script src="<?= Yii::$app->homeUrl;?>atlas/js/jquery.steps.min.js"></script>
<script src="<?= Yii::$app->homeUrl;?>atlas/js/application.form.steps.js"></script>
<script src="<?= Yii::$app->homeUrl;?>atlas/js/embedded.js"></script>



<script type="text/javascript">
	$(document).ready(function(){

		$("#states").change(function(){

			$(".loading").show();
		

				$.ajax({
					url: "<?= Yii::$app->homeUrl;?>atlas/atlas/city",
					type: "GET",
					data: { id: $(this).val() },
					success: function(res){
						$(".loading").hide();
						var data = JSON.parse(res);
						var opt = "<option> - Select - </option>";

						for (i = 0; i < data.length; i++) { 
						    opt += "<option value='" + data[i].city_id_pk + "'>" + data[i].name + "</option>";
						}
						;
						$("#city").html(opt);
					}
				});


		});

		

		$("#addClientForm").validate({
			rules: {
				firstname: {
					required: true,
				},
				lastname: {
					required: true,
				},
				email: {
					
					remote: {
						url: "<?= Yii::$app->homeUrl;?>atlas/atlas/checkmail",
						type: "post",
						data: {
							email: function(){  return $("#email").val(); },
							_backendCSRF: function() { return $('[name="_backendCSRF"]').val(); }
						}

					},
					required: true,
					email: true
				},
				username: {
					
					remote: {
						url: "<?= Yii::$app->homeUrl;?>atlas/atlas/checkusername",
						type: "post",
						data: {
							username: function(){  return $("#username").val(); },
							_backendCSRF: function() { return $('[name="_backendCSRF"]').val(); }
						}

					},
					required: true
				},
				password: {
					required: true,
				},
			},
			messages: {
				email: {
               	 required: "This field is required",
               	 email: "Please enter a valid email address",
                  remote: 'This email already exist'
           	 	},
           	 	username: {
               	 required: "This field is required",
                  remote: 'This username already exist'
           	 	}
			}
		});


		 
		
	});



	function popitup(url) {
		newwindow=window.open(url,'name','height=800,width=950');
		if (window.focus) {newwindow.focus()}
		return false;
	}


	function deleteClient(id){
		$("#deleteClientModal").modal();
		$(".deleteClient").attr('href', '<?= Yii::$app->homeUrl;?>atlas/atlas/delete?id=' + id);
	}


</script>

</body>
</html>

