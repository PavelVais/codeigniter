<?php Head::generate( "Statistiky stránek", FALSE ); ?>
<script>
	$(document).ready(function() {
		Morris.Line({
			element: 'morris-area-chart',
			data: [
<?php
$totalCount = 0;
$i = 0;
$cache = $stat_result->getResults();
$l = count( $cache );
foreach ( $cache as $result )
{
	$end = $l > ($i + 1) ? ',' : '';

	echo '{date: "' . substr( $result, 0, 4 ) . '-' . substr( $result, 4, 2 ) . '-' . substr( $result, 6, 2 ) . '",';
	echo 'users: ' . $result->getUsers() . ',';
	echo 'newusers: ' . $result->getNewUsers() . '}' . $end;
	$totalCount += $result->getUsers();
	$i++;
}
?>
			],
			xkey: 'date',
			ykeys: ['users', 'newusers'],
			labels: ['celkem uživatelé', 'z toho noví uživatelé'],
			pointSize: 2,
			hideHover: 'auto',
			resize: true,
			fillOpacity: .5
		});

		Morris.Donut({
			element: 'morris-donut-chart',
			data: [<?php
$i = 0;
$cache = $browser_result->getResults();
$l = count( $cache );
foreach ( $cache as $result )
{
	$end = $l > ($i + 1) ? ',' : '';

	echo '{label: "' . $result . '",';
	echo 'value: ' . $result->getUsers() . '}' . $end;
	$i++;
}
?>],
			resize: true
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
					<h1 class="page-header">Statistiky přístupů</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-8">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> Měsíční Přístupy od <?php echo $dateFrom ?> do <?php echo $dateTo ?>
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div id="morris-area-chart"></div>
							<div class="table-responsive">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr class="header-blue">
											<th><i class="fa fa-calendar"></i> Datum</th>
											<th><i class="fa fa-users"></i> Celkový počet uživatelů</th>
											<th><i class="fa fa-users"></i> z toho nových</th>
											<th><i class="fa fa-clock-o"></i> průměrná doba na webu</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $stat_result->getResults() as $result ): ?>
											<tr>
												<td class="text-muted"><?php echo substr( $result, 0, 4 ) . '-' . substr( $result, 4, 2 ) . '-' . substr( $result, 6, 2 ) ?></td>
												<td><?php echo $result->getUsers() ?></td>
												<td><?php echo $result->getNewUsers() ?></td>
												<td><?php echo number_format( $result->getavgSessionDuration() ) ?> sec</td>
											</tr>
										<?php endforeach; ?>

									</tbody>
								</table>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="panel-body">
										<div class="list-group">
											<div class="list-group-item header-blue">
												<i class="fa fa-exchange fa-fw"></i> Zdroje návštěvnosti
											</div >
											<?php
											foreach ( $source_result->getResults() as $result ):
												?>

												<div class="list-group-item">
													<i class="fa fa-exchange fa-fw text-muted"></i> <?php echo $result ?>
													<span class="pull-right text-muted small"><?php echo $result->getUsers() ?>
													</span>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>

						</div>
						<!-- /.panel-body -->
					</div>


				</div>
				<!-- /.col-lg-8 -->
				<div class="col-lg-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-desktop fa-fw"></i> Přístupy dle prohlížečů (<?php echo $dateString ?>)
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="list-group">
								<?php
								foreach ( $browser_result->getResults() as $result ):
									?>

									<div class="list-group-item">
										<i class="fa fa-eye fa-fw"></i> <?php echo $result ?>
										<span class="pull-right text-muted small"><?php echo $result->getUsers() ?>
										</span>
									</div>
								<?php endforeach; ?>
							</div>
							<div id="morris-donut-chart"></div>

						</div>
						<!-- /.panel-body -->
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-users fa-fw"></i> Celkem přístupů (<?php echo $dateString ?>)
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body text-center">
							<img class="img-circle" alt="pocet pristupu" src="http://placehold.it/180/55C1E7/fff/&text=<?php echo $totalCount ?>">
							<br>
						</div>
						<!-- /.panel-body -->
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