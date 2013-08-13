<?php Head::generate( 'Úvodní stránka', FALSE ); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="hero-unit" style="margin-top: 25px;">
			<h1 class="text-center page-header">Právě předěláváme stránky :(</h1>
			<div class="media">
					<img class="pull-left media-object" src="<?php echo base_url('images/maintenance.png') ?>" width="128" height="128">
				<div class="media-body">
					<p>Náš web právě prochází úpravou. Budeme se ho snažit pro veřejnost otevřít co nejdříve.</p>
					<em class="pull-right" style="color: #356635;">děkujeme za pochopení</em>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>