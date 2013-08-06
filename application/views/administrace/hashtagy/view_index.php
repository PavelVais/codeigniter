<?php $this->header->generate( "Administrace - Hashtagy", FALSE ); ?>
<script>

	$(document).ready(function() {

		ci.formValidation.run({
			elements: 'form.form-horizontal',
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
					<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
						<div class="alert alert-success"><?php echo $this->session->flashdata( "admin" ) ?></div>
					<?php endif; ?>
					<?php if ( $this->session->flashdata( "error" ) != false ): ?>
						<div class="alert alert-error"><?php echo $this->session->flashdata( "error" ) ?></div>
					<?php endif; ?>
					<h1>#Hashtagy</h1>
					<div class='span12'>
						<h2>Použité hashtagy</h2>
						<p>Seznam všech tagů, které jsou přidružené k <strong>odsouhlaseným</strong> přiznáním.</p>
						<?php if ( $hashtags === FALSE ): ?>
							<div class="alert">
								<strong>Ouvej!</strong> není k dispozici žádný tag.
							</div>
						<?php else: ?>

							<ul class='unstyled inline'>
								<?php foreach ( $hashtags as $hashtag ): ?>
								<li><a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . $hashtag->value_url ) ?>" class="hashtag">
										<span class="count"><?php echo $hashtag->count ?>x</span>
										#<?php echo $hashtag->value_url; ?>
									</a>
								</li>
								<?php endforeach; ?>

							</ul>

						<?php endif; ?>


					</div>

				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view("administrace/view_footer"); ?>

</body>
</html>