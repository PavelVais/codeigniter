<?php $this->header->generate( "Administrace - Přiznání", FALSE ); ?>
<script>
	if (!String.prototype.trim) {
		String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, '');
		};
	}

	$(document).ready(function() {

		ci.formValidation.run({
			elements: '#form-edit',
			customValidation: {
				nospace: function(value) {
					return value.trim().indexOf(" ") === -1 ? true : false;
				},
					   onlychars: function(value) {
					return value.search(/[^a-zA-Z]+/) === -1 ? true : false;
				}
			}
		});
		
	
		$('#delete').confirm({
			ajax : false,
			message: "Opravdu chcete toto přiznání smazat?",
			title: "Ověřovací dialog"
		});
		

	});
</script>
<?php $this->header->closeHead() ?>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div id="menu" class="span2">
				<!--Sidebar content-->
				<?php $this->load->view("administrace/tmpl_menu"); ?>
			</div>
			<div id="main" class="span10">
				<div class='span12 hero-unit'>
					<ul class="nav nav-pills">
						<li><a href="<?php echo base_url( "administrace/priznani" ) ?>">Seznam všech přiznání</a></li>
						<li><a href="<?php echo base_url( "administrace/priznani/seznam" ) ?>">Odsouhlasená přiznání</a></li>
						<li><a href="<?php echo base_url( "administrace/priznani/smazana" ) ?>">Smazaná přiznání</a></li>
					</ul>

					<h1>Editace přiznání #<?php echo $confession->id ?></h1>
					<div class='span12'>
						<?php echo $form->open(); ?>
						<div class="control-group">
							<span class="add-on">Text přiznání</span>
							<div class="controls">
								<?php echo $form->generate( "conf_text", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>	 

						</div>
						<div class="control-group">
							<span class="add-on">Hashtag #</span>
							<div class="controls">
								<?php echo $form->generate( "conf_hashtag", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<?php echo $form->generate( "conf_id", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>	
							<button type="submit" class="btn btn-large btn-primary">Uložit</button>
							<a id="delete" href="<?php echo Secure::csrf_url( "administrace/priznani/smazat/" . $confession->id ) ?>" class="btn btn-large btn-danger">Smazat</a>
						</div>
						</form>
					</div>
				
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("administrace/view_footer"); ?>
</body>
</html>