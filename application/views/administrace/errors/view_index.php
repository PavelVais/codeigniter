<?php Head\Head2::generate( "Seznam chyb", FALSE ); ?>
<script>

</script>
<?php Head\Head2::close(); ?>
<body>
	<div id="wrapper">

		<?php $this->load->view( 'administrace/view_headnav' ); ?>

		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Seznam chyb</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">

						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> Výpis chyb na serveru
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<?php foreach ( $errors as $er ): ?>
								<div class="alert <?php echo $er->viewed ? 'alert-warning' : 'alert-danger' ?>">
									<span class="pull-right close" data-id="<?php echo $er->id ?>">x</span>
									<h4><small><?php echo $er->date ?></small> <?php echo anchor( $er->url, $er->url, array('rel' => 'nofollow') ) ?></h4>
									<?php if ( $er->viewed ): ?> 
										<p class="text-success"><i class="fa fa-check-circle-o"></i> Chyba byla shlédnuta / opravena.</p>
									<?php endif; ?>
									<p class="text-muted">
										<i class="fa fa-file"></i> <?php echo $er->file ?> [<?php echo $er->line ?>]
									</p>
									<div class="clearfix"></div>
									<p><?php echo $er->message ?> </p>
									<a href="#" class="open">detaily »</a>
									<div style="display:none;">
										<p>
											<i class="fa fa-user"></i> Uživatel: <?php echo $er->user_name == null ? 'nepřihlášen' : $er->user_name . ' (id: -' . $er->user_id . ')' ?><br>
											<i class="fa fa-globe"></i> IP: <?php echo $er->ip ?><br>
											<i class="fa fa-desktop"></i> User agent: <?php echo $er->user_agent ?>
										</p>
										<h4>Trace chyby:</h4>
										<?php echo $er->detail ?>
									</div>
								</div>
							<?php endforeach; ?>

						</div>
						<!-- /.panel-body -->
					</div>


				</div>
			</div>
			<!-- /.row -->
		</div>
		<!-- /#page-wrapper -->

	</div>
	<?php $this->load->view( "administrace/view_footer" ); ?>
	<?php Head\Head2::addJS( 'effects.js' ) ?>
	<?php echo Head\Head2::generateDeferred(); ?>
	<script>
		$(document).ready(function() {
			$('.open').click(function() {
				$(this).next().fadeToggle(200);
				return false;
			})
			
			$('.close').click(function() {
			var t = $(this);
			$.ajax({
				url: '<?php echo \URI\Link::URL('!errors/deleteErrorMessage') ?>/'+t.data('id'),
				type: "post",
				timeout: 8000,
				success: function(data)
				{
					try {
						data = jQuery.parseJSON(data);
						css3_engine.scaleOut(t.closest('.alert')).done(function(){
							t.closest('.alert').remove();
						});
					}
					catch (e) {
					}
				}
			});
			return false;
		});
		});
		
	</script>

</body>
</html>