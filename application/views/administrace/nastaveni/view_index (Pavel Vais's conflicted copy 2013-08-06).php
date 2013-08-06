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
					<h1>Nastavení stránek</h1>
					<div class='span12'>
						<h2>Obnovit seznam odkazů</h2>
						<p>Seznam odkazů (sitemap) slouží k informování vyhledadávačů, které stránky jsou přístupné.<br>
							Pokud se přidá nebo upraví větší množství přiznání, je dobré seznam aktualizovat, aby si google mohl nové přiznání zaindexovat a použít ve svém vyhledavači.
						</p>
						<a href="<?php echo base_url( "sitemap/index/administrace" ) ?>" class="btn btn-primary">Obnovit sitemaps</a>
					</div>
					<div class="span12" style="border-top: 1px solid #CCC; margin-top: 35px;">
						<h2>Facebook nastavení sdílení</h2>
						<div class="alert alert-error">
							Tento doplněk zatím není funkční.
						</div>
						<p>Toto nastavení slouží pro sdílení přiznání přímo z administrace.</p>
						<p>
							<small>Pro správnou funkcionalitu je potřeba znát access token, 
							díky kterému může sdílecí aplikace přistupovat ke stránkám a chovat se tam, jako by tam byl jejich správce.<br>
							ID stránek je poznávací číslo stránky, na kterou se bude snažit sdílecí aplikace přistupovat.
							</small>
						</p>
						
						<?php echo $form->open(); ?>
						<div class="control-group">

							<div class="controls">
								<span class="add-on">Access Token aplikace:</span>
								<?php echo $form->generate( "fcb_access_token", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>
							<div class="controls">
								<span class="add-on">ID stránek, na kterou se budou vkládat příspěvky:</span>
								<?php echo $form->generate( "fcb_target_app_id", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
							</div>
							<div class="controls">
								<span class="add-on">Zahrnout odkaz do sdílení:</span>
								<?php echo $form->generate( "fcb_include_url", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
								<p>
									<small>
										Příspěvky na facebook může administrace vkládat čisté, tzn pouze text a nic jiného,
										v druhém případě do toho zahrne i detailní odkaz na veřejnou strankou s přiznáním.
									</small>
								</p>
							</div>
							<button type="submit" class="btn btn-primary">Uložit nastavení</button>
						</div>

						<?php echo $form->close(); ?>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view( "administrace/view_footer" ); ?>

</body>
</html>