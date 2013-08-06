<?php $this->header->generate( "Administrace - Hashtagy", FALSE ); ?>
<script>

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
						<li><a href="<?php echo base_url( "administrace/hashtagy" ) ?>">&larr; Seznam všech tagů</a></li>
					</ul>
					<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
						<div class="alert alert-success"><?php echo $this->session->flashdata( "admin" ) ?></div>
					<?php endif; ?>
					<?php if ( $this->session->flashdata( "error" ) != false ): ?>
						<div class="alert alert-error"><?php echo $this->session->flashdata( "error" ) ?></div>
					<?php endif; ?>
					<h1>Detail tagu <?php echo $hashtag_name ?></h1>
					<div class='span12'>
						<?php if ( $confessions === FALSE ): ?>
							<div class="alert alert-error">
								<strong>Je mi líto!</strong> ale tento hashtag neni zaevidován.
							</div>
						<?php else: ?>
							<p>
								K tagu <?php echo $hashtag_name ?> je přidruženo <?php echo count( $confessions ) ?> přiznání
							</p>
							<a target='_blank' href='<?php echo base_url('hashtag/'.$this->uri->segment(4));  ?>' class='btn btn-primary btn-large' style='margin-bottom: 15px;'>Stránka pro veřejnost</a>
							<?php foreach ( $confessions as $confession ): ?>
								<div class="alert alert-success">
									<p><?php echo $confession->text; ?></p>

									<div class="pull-left approve-panel">
										<a class="btn" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id ) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
										<a class="btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/0" ) ?>" title="zamítnout přiznání"><i class="icon-thumbs-down"></i></a>
									</div>
									<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
									<?php if ( $confession->hashtag != null ): ?>
										<strong class="pull-left">tag: <span class="hashtag"><?php echo $confession->hashtag; ?></span></strong>
									<?php endif; ?>
									<div class="clearfix"></div>
								</div>
							<?php endforeach; ?>
						
					</div>
					<div class='span12'>
						<h2>Přejmenovat tento hash</h2>
						<?php echo $form->open(); ?>
						<div class="control-group">
							<p>Změna se projeví u všech přidružených přiznání.</p>
							<span class="add-on">Změnit název hashtagu <?php echo $hashtag_name ?>.</span>
							<div class="controls">
								<?php echo $form->generate( "rename_hashtag", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<?php echo $form->generate( "hashtag_id", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<?php echo $form->generate( "old_hashtag", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>	
							<button type="submit" class="btn btn-large btn-primary">Přejmenovat hashtag</button>
						</div>
						</form>
					</div>
					<?php endif; ?>


				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view("administrace/view_footer"); ?>

</body>
</html>