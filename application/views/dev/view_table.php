<?php Head::generate( 'dev: table', FALSE ); ?>
<?php Head::generate(); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="row">
			<div class="col-sm-5">
				<?php echo $echo; ?>
			</div>
		</div>

	</div>
</body>
</html>