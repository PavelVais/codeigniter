<?php Head::generate( "404 - strana nenalezena", FALSE ); ?>
<script>
	$(document).ready(function() {



	});
</script>
<?php Head::close(); ?>
<body>
	<div id="wrapper">

		<?php $this->load->view( 'administrace/view_headnav' ); ?>

		<div id="page-wrapper" style="min-height: 400px;">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">To co hledáte zde není...</h1>
				</div>
				<div class="col-lg-12">
					<h2 style="font-size: 80px;"><strong>404</strong> strana nenalezena <i class="fa fa-frown-o"></i></h2> 
					<button onclick="window.history.back()" class="btn btn-lg btn-block btn-warning"><i class="fa fa-mail-reply"></i> zpět</button>
				</div>
					
				<!-- /.col-lg-12 -->
			</div>

		</div>
		<!-- /#page-wrapper -->

	</div>
	<?php $this->load->view( "administrace/view_footer" ); ?>

</body>
</html>