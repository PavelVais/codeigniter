<?php Head::generate( 'Maintenance', FALSE ); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="hero-unit" style="margin-top: 25px;">
			<h1 class="text-center page-header">We are working on it :(</h1>
			<div class="media">
					<img class="pull-left media-object" src="<?php echo base_url('images/maintenance.png') ?>" width="128" height="128">
				<div class="media-body">
					<p>This web is currently under development. We will bring you new site as soon as possible.</p>
					<em class="pull-right" style="color: #356635;">Thank you for your understanding</em>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>