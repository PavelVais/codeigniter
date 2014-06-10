<?php Head::generate( 'dev: form3 validate', FALSE ); ?>
<?php Head::generate(); ?>
<script>
	$(document).ready(function() {

		$('#validatorform').validate({
			ajax: true
		});
		$('#form2test').validate({
			ajax: true,
			onFail: function(){
				console.log("fail");
			},
			customValidation: {
				custom: function(val, ref, arg)
				{
					return val === arg[0];
				}
			}
		});
	});
	
</script>
<?php ?>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="row">
			<div class="col-sm-5">
				<?php echo $generator->generate(); ?>
			</div>
			<div class="col-sm-5">
				<?php echo $generator2->generate(); ?>
			</div>
		</div>

	</div>
</body>
</html>