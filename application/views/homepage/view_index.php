<?php $this->header->generate( 'Make confession', FALSE ); ?>
<script>

	if (!String.prototype.trim) {
		String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, '');
		};
	}
	$(document).ready(function() {


		$("#input-confession").charCounter(300, {
			container: "<em></em>",
			classname: "counter",
			format: "%1",
			pulse: true,
			delay: 5
		});

		ci.formValidation.run({
			elements: '#form-makeconfession',
			ajax: true,
			onAjaxBefore: function() {
				if ($('#btn-confession').hasClass("disabled"))
					return false;

				$('#btn-confession').hide();
				$('.bubblingG').show();


			},
			onAjaxComplete: function(data) {
				cl = data.status === 500 ? "alert-error" : "alert-success";
				var new_obj = jQuery("<div/>", {
					'class': "alert " + cl,
					html: data.response
				});

				$('#input-confession,#input-hashtag-wrapper,.counter,.bubblingG').fadeOut(200, function() {
					$('#input-confession-wrapper').append(new_obj).find(".alert").hide().fadeIn(180);
				});
				return false;

			},
			customValidation: {
				nospace: function(value) {
					return value.trim().indexOf(" ") === -1 ? true : false;
				},
				onlychars: function(value) {
					return value.search(/[^a-zA-Z]+/) === -1 ? true : false;
				}
			}

		});

		$('#input-confession-wrapper').on("click", "#insert-new", function() {
			$('.alert').fadeOut(200, function() {
				$(this).remove();
				$('#input-confession,#input-hashtag').val("");
				$('#input-confession,#input-hashtag-wrapper,.counter,#btn-confession').fadeIn(200);
				$('#input-confession').focus();
			});
			return false;
		});
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
			<?php echo $form->open(); ?>
			<div id="input-confession-wrapper">
				<?php if ( $this->session->flashdata( "admin" ) != false ): ?>
					<div class="alert alert-success"><?php echo $this->session->flashdata( "admin" ) ?></div>
				<?php endif; ?>
				<?php if ( $this->session->flashdata( "error" ) != false ): ?>
					<div class="alert alert-error"><?php echo $this->session->flashdata( "error" ) ?></div>
				<?php endif; ?>
				<div class="control-group">
					<?php echo $form->generate( "txt_confession", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
				</div>
			</div>
			<div id="input-hashtag-wrapper">
				<div class="control-group">
					<span>#</span><?php echo $form->generate( "inp_hashtag", Form::WITHOUT_LABEL + Form::WITHOUT_WRAPPER ) ?>
				</div>

				<div class="bubblingG">
					<span id="bubblingG_1">
					</span>
					<span id="bubblingG_2">
					</span>
					<span id="bubblingG_3">
					</span>
					<p>submitting..</p>
				</div>
				<button id="btn-confession" type="submit" class="btn btn-large btn-primary">Confess!</button>
			</div>

			<?php echo $form->close(); ?>
			<p class="text-center">The most interesting confessions will be made public on...</p>
			<?php $this->load->view( "view_footer" ); ?>



		</div>

	</div>

</body>
</html>