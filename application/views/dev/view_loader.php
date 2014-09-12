<?php \Head\Head2::generate( 'dev: JS Loader' ); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="row">
			<div class="col-sm-4">
				<p>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
					labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
					nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>

				<p>
					Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. 
					Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida. Duis ac tellus et risus vulputate vehicula. Donec lobortis risus a elit. Etiam tempor. Ut ullamcorper, ligula eu tempor congue, eros est euismod turpis, id tincidunt sapien risus a quam. Maecenas fermentum consequat mi. Donec fermentum. Pellentesque malesuada nulla a mi. Duis sapien sem, aliquet nec, commodo eget, consequat quis, neque. Aliquam faucibus, elit ut dictum aliquet, felis nisl adipiscing sapien, sed malesuada diam lacus eget erat. Cras mollis scelerisque nunc. Nullam arcu. Aliquam consequat. Curabitur augue lorem, dapibus quis, laoreet et, pretium ac, nisi. Aenean magna nisl, mollis quis, molestie eu, feugiat in, orci. In hac habitasse platea dictumst.
				</p>
				<button id="loader-normal" class="btn btn-large btn-primary">Loader inline</button>
			</div>
			<div class="col-sm-4">
				<p>

					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
					<a id="loader-a2" class="btn btn-large btn-primary"><i class="fa fa-android"></i> Loader inline s custom ikonou</a>
					labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
					nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
				<a id="loader-a1" class="btn btn-large btn-primary"><i class="fa fa-info-circle"></i> Loader inline s ikonou</a>
				<a id="loader-a3" class="btn btn-large btn-primary hidden"><i class="fa fa-bug"></i> Vypnout loader</a>
			</div>
			<div class="col-sm-4">
				<p id="loader-mask" style="position: relative;">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
					labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
					nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
				<button id="loader-mask-trigger" class="btn btn-large btn-primary">Loader maska</button>
			</div>
		</div>

	</div>
	<?php \Head\Head2::generateDeferred() ?>
	<script>
		$(document).ready(function() {
			$('#loader-normal').click(function() {
				$(this).loader();
			});

			$('#loader-mask-trigger').click(function() {
				$('#loader-mask').loader('mask');
			});
			$('#loader-a2').click(function() {
				$(this).loader({
					icon: 'fa-circle-o-notch fa-spin',
					disabled: false
				});

			});

			$('#loader-a1').click(function() {
				$(this).loader();
				$('#loader-a3').show().css('visibility', 'visible');
			})
			$('#loader-a3').on('click', function() {
				$('#loader-a1').loader();
				$('#loader-a3').hide();
			});
		});
	</script>
</body>
</html>