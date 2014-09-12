<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<script src="<?php echo base_url( 'js/respond.min.js' ) ?>"></script>
<![endif]-->
<?php if ( ENVIRONMENT == 'development' ): ?>
<!-- [development mode = console pollyfill]-->
	<script>
		(function(){var e;var t=function(){};var n=["assert","clear","count","debug","dir","dirxml","error","exception","group","groupCollapsed","groupEnd","info","log","markTimeline","profile","profileEnd","table","time","timeEnd","timeline","timelineEnd","timeStamp","trace","warn"];var r=n.length;var i=window.console=window.console||{};while(r--){e=n[r];if(!i[e]){i[e]=t}}})()
	</script>
<?php endif;
