<script>
	(function() {
		var cn = 'ci_devicePixelRation';
		if (document.cookie.indexOf(cn) == -1
				  && 'devicePixelRatio' in window
				  && window.devicePixelRatio > 1) {

			var date = new Date();
			date.setTime(date.getTime() + 3600000);
			document.cookie = cn + '=' + window.devicePixelRatio + ';' + ' expires=' + date.toUTCString() + '; path=/';
			//if cookies are not blocked, reload the page
			if (document.cookie.indexOf(cn) != -1) {
				window.location.reload();
			}
		}
	})();
</script>