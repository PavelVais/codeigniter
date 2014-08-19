<?php \Head\Head2::generate( 'Úvodní stránka', FALSE ); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php \Head\Head2::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="row">
			<div class="col-sm-5">
			</div>
		</div>

	</div>
	<?php echo HTML\JS::insert() ?>
	<?php \Head\Head2::generateDeferred(); ?>
</body>
</html>