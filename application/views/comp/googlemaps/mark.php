<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
	if (!is_null($description)):
?>
	var contentString = "<?php echo addslashes($description)?>";
        
	var <?php echo 'infowindow_'.$name ?> = new google.maps.InfoWindow({
        content: contentString
    });
<?php endif; ?>
	var <?php echo $name ?> = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $coord[0]?>, <?php echo $coord[1]?>),
		map: map,
		title: "<?php echo $title ?>"
    }); 
 <?php 
	if (!is_null($description)):
?>
	google.maps.event.addListener(<?php echo $name?>, 'click', function() {
		<?php echo 'infowindow_'.$name ?>.open(map,<?php echo $name?>);
    });
<?php endif; ?>   
    
    