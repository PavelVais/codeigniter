<?php $this->header->generate( "Administrace - Změna hesla", FALSE ); ?>
<script>
	$(document).ready(function() {

		ci.formValidation.run({
			elements: '#form-edit',
			customValidation: {
				nospace: function(value) {
					return value.trim().indexOf(" ") === -1 ? true : false;
				},
				onlychars: function(value) {
					return value.search(/[^a-zA-Z0-9]+/) === -1 ? true : false;
				}
			}
		});

	});
</script>
<?php $this->header->closeHead() ?>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div id="menu" class="span2">
				<!--Sidebar content-->
				<?php $this->load->view( "administrace/tmpl_menu" ); ?>
			</div>
			<div id="main" class="span10">
				<div class='span12 hero-unit'>
					<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
						<div class="alert alert-success"><?php echo $this->session->flashdata( "admin" ) ?></div>
					<?php endif; ?>
					<?php if ( $this->session->flashdata( "error" ) != false ): ?>
						<div class="alert alert-error"><?php echo $this->session->flashdata( "error" ) ?></div>
					<?php endif; ?>
					<h1>Změna profilu <?php echo User::get_username() ?></h1>

					<div class='span12'>
						<?php echo $form->open(); ?>
						<div class="control-group">
							<span class="add-on">Změna názvu účtu:</span>
							<div class="controls">
								<?php echo $form->generate( "acc_username", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								
							</div>
						</div>
						<div class="control-group">
							<span class="add-on">Změna hesla:</span>
							<div class="controls">
								<?php echo $form->generate( "acc_pass1", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<?php echo $form->generate( "acc_pass2", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<?php echo $form->generate( "user_id", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>
							<button type="submit" class="btn btn-large btn-primary">Změnit heslo</button>
						</div>
						<?php echo $form->close(); ?>
					</div>


				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view("administrace/view_footer"); ?>

</body>
</html>