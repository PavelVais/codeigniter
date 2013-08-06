<div title="<?php echo $title ?>" class="<?php echo $prefix ?>panel <?php echo $clickable ? "hand" : "" ?>" <?php echo $clickable ? "onclick=\"debugpanel.open('" . $prefix . $name . "')\"" : "" ?> >
	<?php if ( $img != FALSE ): ?>
		<img src="<?php echo base_url( 'images/console/' . $img ) ?>" <?php echo $label == "" ? "style='padding-right: 0;'" : "" ?>>
	<?php endif; ?>

	<span><?php echo $label ?></span>
	<?php if ( $clickable ): ?>
		<div class="<?php echo $prefix ?>window" id='<?php echo $prefix . $name ?>' data-windowlabel="<?php echo $heading ?>">
			<table>
				<?php if ( isset( $window_data['heading'] ) ): ?>
					
					<?php echo $window_data['heading'] ?>
				
						<?php endif; ?>
				<?php foreach ( $window_data['rows'] as $tr ): ?>
					<?php echo $tr ?>
				<?php endforeach; ?>
			</table>
		</div>
	<?php endif; ?>

</div>

