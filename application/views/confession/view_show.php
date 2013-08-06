<?php $this->header->generate( 'Confession detail', FALSE ); ?>
<meta property="og:title" content="Confession #<?php echo $confession->id; ?> | makeconfession.com" />
<meta property="og:description" content="<?php echo $confession->text; ?>"/>
<meta property="og:url" content="<?php echo current_url() ?>"/>
<meta property="og:site_name" content="Make anonymous Confession!"/>
<meta property="og:image" content="<?php echo base_url("images/fcb_sharer.png") ?>" />
<meta property="og:type" content="website"/>
<meta property="fb:admins" content="1060224904"/>
<script>
	$(document).ready(function() {
		
	});


</script>
<?php $this->header->closeHead() ?>
<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id))
				return;
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/cs_CZ/all.js#xfbml=1&appId=175163265988514";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
	<div id="main-wrapper" class="container">
		<img
			src="<?php echo base_url( 'images/logo.png' ) ?>"
			title='MakeConfession.com'
			alt='MakeConfession.com logo'>
		<p class="text-center">Make an anonymous confession!</p>
		<div class="row-fluid">
			<div id="input-confession-wrapper">
				<div class="alert alert-success hero-unit" style="padding-bottom: 15px;">
					<p><?php echo $confession->text; ?></p>

					<div class='bottom-panel'>
						<?php if ( $confession->hashtag != null ): ?>
							<strong class="pull-left">tag: <span class='hashtag'><?php echo $confession->hashtag; ?></span></strong>
						<?php endif; ?>
						<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
						<div class="clearfix"></div>
					</div>
					<div style="margin-top: 25px;" class="fb-like" data-href="<?php echo current_url() ?>" data-send="false" data-width="450" data-show-faces="true" data-font="segoe ui"></div>
				</div>
			</div>
			<div id="input-hashtag-wrapper">
				<a id='btn-confession' href='<?php echo base_url( "" ) ?>'class="btn btn-large btn-primary">Go to mainpage</a>
			</div>

			<?php $this->load->view("view_footer"); ?>
		</div>
	</div>

</body>
</html>