<?php Head\Head2::generate( "Přihlašovací okno do administrace", FALSE ); ?>
<script>

	$(document).ready(function() {

		ci.formValidation.run({
			elements: 'form.form-horizontal',
		});

	});
</script>
<?php Head\Head2::close() ?>
<body>

	<div class="container">
		<div class="row">

			<div id='login-container' class="col-sm-6 col-sm-offset-3">
				<div class="row">
					<div class="col-xs-12">
						<img src="http://placehold.it/500x150" class="img-responsive" title='<?php echo $title ?>' alt='<?php echo $title ?>'>

					</div>

				</div>
				<h1 class='text-center'>Administrace</h1>
				<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
					<div class="alert alert-dismissable alert-danger">
						<strong>:-( </strong>
						<?php echo $this->session->flashdata( "admin" ); ?>
					</div>
				<?php endif; ?>
				<?php echo $fgenerator->generate(); ?>

			</div>
		</div>
	</div>
</body>
</html>