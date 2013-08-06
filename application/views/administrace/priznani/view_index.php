<?php $this->header->generate( "Administrace - Přiznání", FALSE ); ?>
<script>

	$(document).ready(function() {

		$('.delete').confirm({
			ajax: false,
			message: "Opravdu chcete toto přiznání smazat?",
			title: "Ověřovací dialog"
		});

		$('a.publish').click(function() {
			t = $(this);
			obj = {
				method: 'feed',
				message: t.data('og:title'),
				name: t.data('og:title') + '| makeconfession.com',
				link: t.data('og:url'),
				picture: "<?php echo base_url( "images/fcb_sharer.png" ) ?>",
				caption: t.data('og:title') + (t.data('og:hashtag') !== "" ? " with hashtag #" + t.data('og:hashtag') : ""),
				description: t.data('og:description')
			}

			if (t.data('og:hashtag') !== "")
			{
				a = {
					properties: [{
							text: 'confessions for hashtag #' + t.data('og:hashtag'),
							href: '<?php echo base_url( 'hashtag/' ) ?>/' + t.data("og:hashtag")}
					]

				};
				$.extend(true, obj, a);
			}
			FB.ui(
					  obj,
					  function(response) {
						  if (response !== undefined && response !== null)
							  ci.showNotice("Přiznání bylo publikováno na vaší zdi");
					  }
			);
			return false;
		});
		$('.approve-panel a.approving-btn').click(function() {
			t = $(this);
			box = t.closest(".alert");
			$.ajax({
				url: t.attr("href"),
				type: "post",
				timeout: 8000,
				beforeSend: function() {
					box.mask("posílám požadavek", 100);
				},
				success: function(data)
				{
					try {
						data = jQuery.parseJSON(data);
						ci.showNotice(data.response, data.status);
						if (data.status !== 500) {
							box.animate({
								opacity: 0,
								left: '100%'
							}, 350, function() {
								$(this).remove();
								if (data.approve)
									$('#count-confession').text(parseInt($('#count-confession').text()) + 1);
							});
						}
					}
					catch (e) {
					}
				},
				complete: function() {
					box.unmask();
				}
			});
			return false;
		});



	});
</script>
<?php $this->header->closeHead() ?>
<body>
	<div id="fb-root"></div>
	<script>
		window.fbAsyncInit = function() {
			// init the FB JS SDK
			FB.init({
				appId: '175163265988514', // App ID from the app dashboard
				channelUrl: '<?php echo base_url( "channel.html" ) ?>', // Channel file for x-domain comms
				status: true, // Check Facebook Login status
				xfbml: false // Look for social plugins on the page
			});

			// Additional initialization code such as adding Event Listeners goes here
		};

		// Load the SDK asynchronously
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	<div class="container-fluid">
		<div class="row-fluid">
			<div id="menu" class="span2">
				<!--Sidebar content-->
				<?php $this->load->view( "administrace/tmpl_menu" ); ?>
			</div>
			<div id="main" class="span10">
				<div class='span12 hero-unit'>
					<ul class="nav nav-pills">
						<li class="active"><a href="<?php echo base_url( "administrace/priznani" ) ?>">Seznam všech přiznání</a></li>
						<li><a href="<?php echo base_url( "administrace/priznani/seznam" ) ?>">Odsouhlasená přiznání</a></li>
						<li><a href="<?php echo base_url( "administrace/priznani/zamitnute" ) ?>">Smazaná přiznání</a></li>
					</ul>
					<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
						<div class="alert alert-success"><?php echo $this->session->flashdata( "admin" ) ?></div>
					<?php endif; ?>
					<?php if ( $this->session->flashdata( "error" ) != false ): ?>
						<div class="alert alert-error"><?php echo $this->session->flashdata( "error" ) ?></div>
					<?php endif; ?>
					<h1>Přiznání</h1>
					<div class='span12'>
						<h2>Neodsouhlasená přiznání</h2>
						<?php if ( $confessions_new === FALSE ): ?>
							<div class="alert">
								<strong>Paráda!</strong> přečetli jste všechna nová přiznání!
							</div>
						<?php else: ?>

							<?php foreach ( $confessions_new as $confession ): ?>
								<div class="alert alert-success">
									<p><?php echo $confession->text; ?></p>

									<div class="pull-left approve-panel">
										<a class="btn" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id ) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/1" ) ?>" title="schválit přiznání"><i class="icon-thumbs-up"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/0" ) ?>" title="zamítnout přiznání"><i class="icon-thumbs-down"></i></a>
									</div>
									<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
									<?php if ( $confession->hashtag != null ): ?>
										<strong class="pull-left">tag: <a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . HashtagsModel::hashtag2url( $confession->hashtag ) ) ?>" class="hashtag"><?php echo $confession->hashtag; ?></a></strong>
									<?php endif; ?>
									<div class="clearfix"></div>
								</div>
							<?php endforeach; ?>

						<?php endif; ?>

						<h2>10 posledních přiznání</h2>
						<?php if ( $confessions_old === FALSE ): ?>
							<div class="alert alert-error">
								<strong>Zvláštní!</strong> Nejsou dostupná žádná přiznání.
							</div>
						<?php else: ?>

							<?php foreach ( $confessions_old as $confession ): ?>
								<div class="alert alert-info">
									<p><?php echo $confession->text; ?></p>

									<div class="pull-left approve-panel">
										<a class="btn" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id ) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/0" ) ?>" title="zamítnout přiznání"><i class="icon-thumbs-down"></i></a>
										<a class="btn" target='_blank' href="<?php echo base_url( "confession/$confession->id" ) ?>" 
											title="stránka s přiznáním"><i class="icon-share"></i></a>
										<a 
											data-og:title="Confession #<?php echo $confession->id; ?>"
											data-og:description="<?php echo character_limiter( $confession->text, 90 ) ?>"
											data-og:url="<?php echo base_url( "confession/$confession->id" ) ?>"
											data-og:hashtag="<?php echo HashtagsModel::hashtag2url( $confession->hashtag ); ?>"
											data-og:site_name="Make anonymous Confession!"
											class="btn publish" 
											href="<?php echo base_url( "confession/$confession->id" ) ?>" 
											title="sdílet na facebooku"><i class="icon-globe"></i></a>
									</div>
									<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
									<?php if ( $confession->hashtag != null ): ?>
										<strong class="pull-left">tag: <a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . HashtagsModel::hashtag2url( $confession->hashtag ) ) ?>" class="hashtag"><?php echo $confession->hashtag; ?></a></strong>
									<?php endif; ?>
									<div class="clearfix"></div>
								</div>
							<?php endforeach; ?>

						<?php endif; ?>

						<h2>5 posledních smazaných přiznání</h2>
						<?php if ( $confessions_deleted === FALSE ): ?>
							<div class="alert">
								<strong>Super!</strong> Nemáte žádná smazaná přiznání.
							</div>
						<?php else: ?>

							<?php foreach ( $confessions_deleted as $confession ): ?>
								<div class="alert">
									<p><?php echo $confession->text; ?></p>

									<div class="pull-left approve-panel">
										<a class="btn" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id ) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/1" ) ?>" title="schválit přiznání"><i class="icon-thumbs-up"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/0" ) ?>" title="zamítnout přiznání"><i class="icon-thumbs-down"></i></a>
										<a class="btn delete" href="<?php echo Secure::csrf_url( "administrace/priznani/smazat-natrvalo/" . $confession->id ) ?>" title="smazat na trvalo"><i class="icon-trash"></i></a>
									</div>
									<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
									<?php if ( $confession->hashtag != null ): ?>
										<strong class="pull-left">tag: <a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . HashtagsModel::hashtag2url( $confession->hashtag ) ) ?>" class="hashtag"><?php echo $confession->hashtag; ?></a></strong>
									<?php endif; ?>
									<div class="clearfix"></div>
								</div>
							<?php endforeach; ?>

						<?php endif; ?>

					</div>

				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view("administrace/view_footer"); ?>

</body>
</html>