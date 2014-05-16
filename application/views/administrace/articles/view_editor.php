<?php Head::generate( "Editor článků", FALSE ); ?>
<script>
	$(document).ready(function() {
		$('#frm_message').summernote({
			height: 280
		});

		$("#frm_keywords").css('width', '100%').select2({
			placeholder: "Vyberte klíčové slovo",
			minimumInputLength: 2,
			multiple: true,
			id: function(e) {
				return e.id + ":" + e.text;
			},
			data: {},
			createSearchChoice: function(term, data) {
				if ($(data).filter(function() {
					return this.text.localeCompare(term) === 0;
				}).length === 0) {
					return {id: 0, text: term, isNew: true};
				}
			},
			ajax: {// instead of writing the function to execute the request we use Select2's convenient helper
				url: "<?php echo base_url( 'administrace/keywords/get' ) ?>",
				dataType: 'json',
				type: 'POST',
				data: function(term) {
					return {
						q: term // search term
					};
				},
				results: function(data, page) { // parse the results into the format expected by Select2.
					// since we are using custom formatting functions we do not need to alter remote JSON data
					if (data.status === 404)
						return {results: []};
					return {results: data.response};
				}
			},
			//formatResult: movieFormatResult, // omitted for brevity, see the source of this page
			//formatSelection: movieFormatSelection, // omitted for brevity, see the source of this page
			formatResult: function(exercise) {
				var str = (exercise.isNew) ? '<span class="label label-primary">nový tag</span> ' : '';
				var posttr = exercise.count > 0 ? ' <span class="badge pull-right">' + exercise.count + '</span>' : '';
				return "<div class='select2-user-result'>" + str + exercise.text + posttr + "</div>";
			},
			formatSelection: function(exercise) {
				return exercise.text;
			},
			dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
			escapeMarkup: function(m) {
				return m;
			}, // we do not want to escape markup since we are displaying html in results
			initSelection: function(item, callback) {
				$("#keywords-select").select2("val", '');
				if (item.data('values') === undefined)
					return;
				var data = item.data('values');
				var l = data.length;
				for (i = 0; i < l; i++) {
					data[i].text = data[i].name;
				}
				callback(data);
			}
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
					<h1 class="page-header"><?php echo $title ?></h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-8">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> Editor článku
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<?php echo $form->generate(); ?>
						</div>
						<!-- /.panel-body -->
					</div>


				</div>
				<!-- /.col-lg-8 -->
				<div class="col-lg-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-desktop fa-fw"></i> Autor článku
							<i id="help-author" class="fa fa-question-circle pull-right pointer"></i>

						</div>
						<div class="clearfix"></div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="list-group">

							</div>
							<div id="morris-donut-chart"></div>

						</div>
						<!-- /.panel-body -->
					</div>

					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-facebook fa-fw"></i> Facebook údaje
							<i id="help-facebook" class="fa fa-question-circle pull-right pointer fa-fw"></i>
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body text-center">
							<img class="img-circle" alt="pocet pristupu" src="http://placehold.it/180/55C1E7/fff/&text=tamtam">
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