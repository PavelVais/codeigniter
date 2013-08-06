<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript">
function initialize() {
	var latlng = new google.maps.LatLng(<?php echo $coord[0]?>, <?php echo $coord[1]?>);
	var myOptions = {
		zoom: <?php echo $zoom?>,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.<?php echo $map?>,
    }
	var map = new google.maps.Map(document.getElementById("<?php echo $id?>"), myOptions);