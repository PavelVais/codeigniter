<?php $this->header->generate( "Přihlašovací okno do administrace", FALSE ); ?>
<script>

	$(document).ready(function() {

		ci.formValidation.run({
			elements: 'form.form-horizontal',
		});

	});
</script>
<?php $this->header->closeHead() ?>
<body>


	<div class="container">
		<div id='login-container' class="span6 offset2">
			<img src="<?php echo base_url( 'images/logo.png' ) ?>" title='MakeConfession.com' alt='MakeConfession.com logo'>
			<h1 class='text-center'>Administrace</h1>
			<?php if($this->session->flashdata("admin") != false): ?>
			<div class="alert alert-error">
				<strong>:-( </strong>
				<?php echo $this->session->flashdata("admin"); ?>
			</div>
			<?php endif; ?>

			<?php echo $form->open(); ?>
			<form class="form-horizontal">
				<div class="control-group">
					<label class="control-label" for="inputEmail">Login</label>
					<div class="controls">
						<?php echo $form->generate("login", Form::WITHOUT_LABEL+Form::WITHOUT_WRAPPER) ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPassword">Heslo</label>
					<div class="controls">
						<?php echo $form->generate("password", Form::WITHOUT_LABEL+Form::WITHOUT_WRAPPER) ?>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-large btn-primary">Přihlásit se</button>
					</div>
				</div>
			</form>
		</div>
	</div>

</body>
</html>