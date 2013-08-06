<?php $this->header->generate( "Administrace - Přiznání", FALSE ); ?>
<script>

	$(document).ready(function() {

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
									$('#count-confession').text(parseInt($('#count-confession').text()) + 1)
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
		})
		
		$('.delete').confirm({
			ajax : false,
			message: "Opravdu chcete toto přiznání smazat?",
			title: "Ověřovací dialog"
		});
	});
</script>
<?php $this->header->closeHead() ?>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div id="menu" class="span2">
				<!--Sidebar content-->
				<?php $this->load->view("administrace/tmpl_menu"); ?>
			</div>
			<div id="main" class="span10">
				<div class='span12 hero-unit'>
					<ul class="nav nav-pills">
						<li><a href="<?php echo base_url( "administrace/priznani" ) ?>">Seznam všech přiznání</a></li>
						<li><a href="<?php echo base_url( "administrace/priznani/seznam" ) ?>">Odsouhlasená přiznání</a></li>
						<li class="active"><a href="<?php echo base_url( "administrace/priznani/zamitnute" ) ?>">Smazaná přiznání</a></li>
					</ul>
					<?php if ($this->session->flashdata("admin") != false): ?>
						<div class="alert alert-success"><?php echo $this->session->flashdata("admin") ?></div>
					<?php endif; ?>
					<?php if ($this->session->flashdata("error") != false): ?>
						<div class="alert alert-error"><?php echo $this->session->flashdata("error") ?></div>
					<?php endif; ?>
						
					<h1>Zamitnuté přiznání</h1>
					<p>Tato přiznání se berou zamítnutá a neveřejná. Nedá se k nim přes žádnou url veřejně dostat.</p>
					<div class='span12'>
						<?php if ( $confessions === FALSE ): ?>
							<div class="alert">
								<strong>Paráda!</strong> nemáte žádné zamítnuté přiznání!
							</div>
						<?php else: ?>

							<?php foreach ( $confessions as $confession ): ?>
								<div class="alert">
									<p><?php echo $confession->text; ?></p>

									<div class="pull-left approve-panel">
										<a class="btn" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
										<a class="btn approving-btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/1" ) ?>" title="schválit přiznání"><i class="icon-thumbs-up"></i></a>
										<a class="btn delete" href="<?php echo Secure::csrf_url( "administrace/priznani/smazat-natrvalo/" . $confession->id . "/zamitnute" ) ?>" title="smazat na trvalo"><i class="icon-trash"></i></a>
									</div>
									<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
									<?php if ( $confession->hashtag != null ): ?>
										<strong class="pull-left">tag: <a href="<?php echo base_url("administrace/hashtagy/ukazat/".HashtagsModel::hashtag2url($confession->hashtag)) ?>" class="hashtag"><?php echo $confession->hashtag; ?></a></strong>
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