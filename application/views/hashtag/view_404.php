<?php $this->header->generate( 'Can\'t find hashtag', FALSE ); ?>
<script>
	$(document).ready(function() {

	});


</script>
<?php $this->header->closeHead() ?>
<body>
	<div id="main-wrapper" class="container">
		<img
			src="<?php echo base_url( 'images/logo.png' ) ?>" t
			itle='MakeConfession.com'
			alt='MakeConfession.com logo'>
		<p class="text-center">Make an anonymous confession!</p>
		<div class="row-fluid">
			<div id="input-confession-wrapper">
				<div class="alert alert-error">
					<h2>Damn!</h2>
					<p>Hashtag <strong><?php echo $hashtag ?></strong> doesn't have any confessions :-(</p>
				</div>
			</div>
			<div id="input-hashtag-wrapper">
				<a id='btn-confession' href='<?php echo base_url("") ?>'class="btn btn-large btn-primary">Go to mainpage</a>
			</div>

			<?php $this->load->view("view_footer"); ?>
		</div>
	</div>

</body>
</html>