<?php $this->header->generate( "Administrace", FALSE ); ?>
<script>
	$(document).ready(function() {
		$('.approve-panel a').not("#edit").click(function() {
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
								if ($('.alert-success').length === 0)
									$('.no-overflow').append('<div class="alert"><strong>Paráda!</strong> přečetli jste všechna nová přiznání!</div>');

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
					<h1>Administrace MakeConfession.com</h1>
					<div class='row'>
						<div class='span8 no-overflow'>
							<h2>Nová přiznání</h2>
							<?php if ( $confessions === FALSE ): ?>
								<div class="alert">
									<strong>Paráda!</strong> přečetli jste všechna nová přiznání!
								</div>
							<?php else: ?>

								<?php foreach ( $confessions as $confession ): ?>
									<div class="alert alert-success">
										<p><?php echo $confession->text; ?></p>

										<div class="pull-left approve-panel">
											<a class="btn" id="edit" href="<?php echo base_url( "administrace/priznani/editace/" . $confession->id ) ?>" title="editovat přiznání"><i class="icon-edit"></i></a>
											<a class="btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/1" ) ?>" title="schválit přiznání"><i class="icon-thumbs-up"></i></a>
											<a class="btn" href="<?php echo Secure::csrf_url( "administrace/priznani/schvaleni/" . $confession->id . "/0" ) ?>" title="zamítnout přiznání"><i class="icon-thumbs-down"></i></a>
										</div>
										<small class="pull-right"><?php echo date( "d.m.Y H:i", strtotime( $confession->created ) ) ?></small>
										<?php if ( $confession->hashtag != null ): ?>
											<strong class="pull-left">tag: 
												<a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . HashtagsModel::hashtag2url( $confession->hashtag ) ) ?>" class="hashtag"><?php echo $confession->hashtag; ?></a></strong>
										<?php endif; ?>
										<div class="clearfix"></div>
									</div>
								<?php endforeach; ?>

							<?php endif; ?>
						</div>
						<div class='span4'>
							<h2>Seznam #hashtagů</h2>
							<?php if ( $hashtags === FALSE ): ?>
								<div class="alert">
									<strong>:-(</strong> Nemáte žádné tagy.
								</div>
							<?php else: ?>
								<ul id="hashtags" class='unstyled inline'>
									<?php foreach ( $hashtags as $hashtag ): ?>
										<li><a href="<?php echo base_url( "administrace/hashtagy/ukazat/" . HashtagsModel::hashtag2url( $hashtag->value ) ) ?>" class="hashtag"><?php echo $hashtag->value; ?></a></li>
									<?php endforeach; ?>

								</ul>
							<?php endif; ?>
						</div>
					</div>
					<div class='row'>
						
						<div class='span12'>
							<h2>Changelog</h2>
							<ul>30.6.
								<li>Upraven script pro potvrzování a mazání přiznání na všech stránkách. Nyní fungují všechna tlačítka :)</li>
								<li>Přidána možnost změnit si login a heslo.</li>
								<li>Přidány metatagy pro facebook sdílení jednotlivých přiznání</li>
								<li>Vytvořena facebook aplikace MakeConfession, přes kterou jde nyní postovat jednotlivé přiznání přímo na zeď.</li>
								<li>Upraveny věci dle připomínek od Ondry. (texty, odkazy..)</li>
								<li>Přibyla možnost smazat úplně přiznání (ikona popelnice)</li>
							</ul>
							<ul>29.6.
								<li>Doděláno automatické generování sitemapy pro lepší spolupráci s vyhledávačema.</li>
								<li>Opravena chyba, kdy při přejmenování hashtagu daný hashtag zmizel.</li>
							</ul>
							<ul>28.6.
								<li>Dodělaná hlavní stránka a celkově zbytek:)</li>
								<li>Stárnky přesunuty na makeconfession.com doménu.</li>
							</ul>
							<ul>27.6.
								<li>Přidána možnost editovat přiznání, včetně hashtagů.</li>
							</ul>
							<ul>26.6.
								<li>Založení všech potřebných stránek.</li>
								<li>Připravení databáze.</li>
								<li>provoznení základní komunikace mezi stránkami a databází.</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view("administrace/view_footer"); ?>

</body>
</html>