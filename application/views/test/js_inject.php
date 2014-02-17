<script>
	$(document).ready(function() {
		console.log("**INJECT**");
		$.ajaxSetup({
			crossDomain: true
		});

		$(document).ajaxSend(function(e,a,settings ) {
			console.log(settings);
			var url = settings.url;
			settings.url = "<?php echo site_url( 'test/doAjaxCalling' ) ?>";
			if (settings.data === undefined)
				settings.data = "ci_url="+escape(url);
			else
			settings.data = '&ci_url='+url;
		});
	});
</script>
