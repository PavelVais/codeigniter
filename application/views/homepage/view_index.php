<?php Head::generate( 'Úvodní stránka', FALSE ); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="hero-unit">
			<h1 class="">Codeigniter je nainstalován a běží...</h1>
			<div class="well">
				<h2>il8n plugin:</h2>
				<p><strong>argument test: </strong>
					<?php echo $this->lang->line( 'debug.arguments','Pavel',25 ) ?>
					<br>
					<span style="color: #9F6000;">
						<?php echo strtr(' $this->lang->line( \'debug.arguments\',\'Pavel\',25 )',Array("<"=>"&lt;","&"=>"&amp;")); ?>
					</span>
					
				</p>
				<p><strong>actual language: </strong>
					<?php echo $this->lang->lang() ?>
					<br>
					<strong>switch to <?php echo $this->lang->lang() == 'en' ? 'czech' : 'english' ?>: </strong>
					<?php echo anchor($this->lang->switch_uri($this->lang->lang() == 'en' ? 'cs' : 'en')); ?>
					<br>
					<span style="color: #9F6000;">
						<?php echo strtr(' echo anchor($this->lang->switch_uri(\''.($this->lang->lang() == 'en' ? 'cs' : 'en').'\'));',Array("<"=>"&lt;","&"=>"&amp;")); ?>
					</span>
				</p>
				<p>Plugin byl upraven takto:</p>
				<ul>
					<li>První jazyk je i defaultní, v url se nemusí zadat a automaticky se nastaví.</li>
					<li>Pokuď se defaultní jazyk vloží do url, automaticky se url přesměruje tak, aby jazykový identifikátor v url nebyl.</li>
					<li>Při dotazování se na <span style="color: #9F6000;">$this->uri->segment()</span> se jazykový identifikátor nebere v potaz.</li>
				</ul>
				
			</div>

		</div>
	</div>
</body>
</html>