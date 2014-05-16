<?php Head::generate( "Seznam článků", FALSE ); ?>
<script>
	$(document).ready(function() {

		$('#table-context-menu button').tooltip({
			container: '#table-context-menu'
		});


		$('#table-context-menu .close').click(function() {
			$('#table-context-menu').stop().fadeTo(200, 0);
			$('#articles-table tr.active').removeClass('active');
			$('#fnc-storno-article').trigger('click');
		});
		$('#fnc-edit-article').click(function() {
			var url = '<?php echo site_url( 'administrace/articles/edit/' ) ?>/' + $('#table-context-menu').data('aid');
			window.location.href = url;
		});
		$('#fnc-delete-article').click(function() {
			var url = '<?php echo site_url( 'administrace/articles/delete/' ) ?>/' + $('#table-context-menu').data('aid');

			var t = $(this);
			t.css('left',-22).animate('left',0);
			$('#fnc-confirm-article').css('width',0).show().animate({'width': 73}, 300);
			setTimeout(function() {
				t.hide();
				
				$('#fnc-storno-article').css('left','').show();
			}, 400);


			return false;

		});
		$('#fnc-storno-article').click(function() {
			var t = $(this);
			setTimeout(function() {
				t.hide();
				$('#fnc-delete-article').css('left',0).show();
				$('#fnc-confirm-article').hide();
				$('#fnc-storno-article').css('left','');
			}, 400);
			$('#fnc-storno-article').animate({'left':-20});
			$('#fnc-confirm-article').css('width', 0);
			//var d = $('#fnc-delete-article');
			//d.html('<i class="fa fa-trash-o"></i>').css('width',d.data('origw')).removeClass('btn-danger');
		});


		$('#articles-table').on('click', 'tr', function(e) {
			var x = e.pageX - $('#articles-table').offset().left;
			var y = e.pageY - $('#articles-table').offset().top;
			var t = $('#table-context-menu');
			var tt = $(this);
			t.data('aid', tt.data('aid'));
			$('#articles-table tr.active').removeClass('active');
			tt.addClass('active');
			t.stop().fadeTo(200, 1).css({
				top: y - 23,
				left: x - 80
			});
			t.find('h5').text(tt.find('td.tbl-title').text());
		});

		$('#help-facebook').popover({
		});

		$('#help-author').popover({
			placement: 'left',
			title: 'Nápověda k autorovi',
			content: 'Článek je vždy sdružován k Vašemu účtu, ale i tak ho můžete vydat pod pseudonymem.'
		});
		$('#help-facebook').popover({
			placement: 'left',
			html: true,
			title: 'Nápověda k facebook sekci',
			content: 'Článek může být sdílen na síti Facebook a proto by měl mít správně vyplněný všechny informace.<br>'
		});

	});
</script>
<?php Head::close(); ?>
<body>
	<div id="wrapper">

		<?php $this->load->view( 'administrace/view_headnav' ); ?>

		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Seznam vytvořených článků</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-file-text-o fa-fw"></i> Články
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="table-responsive">
								<table id="articles-table" class="table table-hover table-striped">
									<thead>
										<tr class="header-blue">
											<th><i class="fa fa-user"></i> Autor</th>
											<th><i class="fa fa-adjust"></i> Titulek</th>
											<th><i class="fa fa-list-alt"></i> Popis</th>
											<th><i class="fa fa-clock-o"></i> Vznik</th>
										</tr>
									</thead>
									<tbody data-toggle="tooltip" data-placement="top" title="Smazat článek">
										<?php foreach ( $articles as $article ): ?>
											<tr data-aid="<?php echo $article->id ?>">
												<td><?php echo $article->username . (strlen( $article->author_name ) > 0 ? " <br><span class='text-muted'>($article->author_name)</span>" : '') ?></td>
												<td class="tbl-title tbl-bold"><?php echo $article->title ?></td>
												<td class="tbl-i"><?php echo $article->description ?></td>
												<td title="<?php echo date( 'd.m.Y H:i:s', strtotime( $article->date_created ) ) ?>"><?php echo date( 'd.m.Y', strtotime( $article->date_created ) ) ?></td>
											</tr>	
										<?php endforeach; ?>

									</tbody>
								</table>
								<div id="table-context-menu" class="customHide" data-aid="0">
									<span class="customPopover text-center">
										<h5></h5>
										<button type="button" class="close" aria-hidden="true">&times;</button>
										<div class="btn-group ">
											<button id="fnc-edit-article" class="btn btn-sm btn-small btn-default"  data-toggle="tooltip" data-placement="bottom" title="Editovat článek">
												<i class="fa fa-pencil"></i>
											</button>
											<button id="fnc-show-article" class="btn btn-sm btn-small btn-default <?php echo $article->visible == 1 ? 'customHide' : '' ?>"  data-toggle="tooltip" data-placement="bottom" title="Zviditelnit článek">
												<i class="fa fa-eye"></i>
											</button>	
											<button id="fnc-hide-article" class="btn btn-sm btn-small btn-default <?php echo $article->visible == 1 ? '' : 'customHide' ?>"  data-toggle="tooltip" data-placement="bottom" title="Skrýt článek">
												<i class="fa fa-eye-slash"></i>
											</button>
											<button  id="fnc-confirm-article" class="customHide btn btn-sm btn-small btn-danger" data-toggle="tooltip" data-placement="bottom" title="Opravdu chcete smazat článek?">
												opravdu?
											</button>
											<button  id="fnc-delete-article" class="btn btn-sm btn-small btn-default" data-toggle="tooltip" data-placement="bottom" title="Smazat článek">
												<i class="fa fa-trash-o"></i>
											</button>
											<button  id="fnc-storno-article" class="customHide btn btn-sm btn-small btn-default" data-toggle="tooltip" data-placement="bottom" title="Storno smazání">
												<i class="fa fa-mail-reply"></i>
											</button>
										</div>
									</span>
								</div>
							</div>
						</div>
						<!-- /.panel-body -->
					</div>


				</div>
				<!-- /.col-lg-8 -->
				<div class="col-md-3">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> Statistika článků
							<i id="help-author" class="fa fa-question-circle pull-right pointer"></i>
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="list-group">
								<div class="list-group-item">
									<i class="fa fa-file-text fa-fw"></i> Počet článků
									<span class="pull-right text-muted small">
										<?php echo count( $articles ) ?>
									</span>
								</div>
								<div class="list-group-item">
									<i class="fa fa-tags fa-fw"></i> Počet kategorií
									<span class="pull-right text-muted small">
										<?php echo count( $articles ) + 9 ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- /.col-lg-4 -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /#page-wrapper -->

	</div>
	<?php $this->load->view( "administrace/view_footer" ); ?>

</body>
</html>