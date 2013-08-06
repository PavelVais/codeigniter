<?php $this->header->generate( 'Cofessions for ' . $hashtag, FALSE ); ?>
<meta property="og:title" content="Confessions for tag <?php echo $hashtag; ?> | makeconfession.com"/>
<meta property="og:description" content="Check confessions for hashtag <?php echo $hashtag; ?>!"/>
<meta property="og:url" content="<?php echo current_url() ?>"/>
<meta property="og:site_name" content="Make anonymous Confession!"/>
<meta property="og:type" content="website"/>
<meta property="og:image" content="<?php echo base_url("images/fcb_sharer.png") ?>" />
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
				<h1>Confessions for <?php echo $hashtag ?></h1>
				<?php foreach ( $confessions as $confession ): ?>
					<div class="alert alert-success hero-unit">
						<p><?php echo $confession->text; ?></p>

						<div class='bottom-panel'>
							<?php if ( $confession->hashtag != null ): ?>
								<strong class="pull-left">tag: <span class='hashtag'><?php echo $confession->hashtag; ?></span></strong>
							<?php endif; ?>
							<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
							<div class="clearfix"></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div id="input-hashtag-wrapper">
				<div class="fb-like" data-href="<?php echo current_url() ?>" data-send="false" data-width="300" data-show-faces="true" data-font="segoe ui"></div>
				<a id='btn-confession' href='<?php echo base_url( "" ) ?>'class="btn btn-large btn-primary">Go to mainpage</a>
			</div>
			<?php $this->load->view( "view_footer" ); ?>
		</div>
	</div>

</body>
</html>