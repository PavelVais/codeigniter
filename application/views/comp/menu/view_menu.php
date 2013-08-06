<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>
<div id="<?php echo $div_id ?>">
	<ul>
		<?php foreach ($entries as $entry): ?>
		
			<li <?php echo is_null($entry['additional'])? '' : 'class="'.$entry['additional'].'"' ?>>
				<?php echo anchor($entry['url'],$entry['name']) ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>