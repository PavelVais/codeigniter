<?php Head::generate("strom stránek") ?>
<body>
		<?php echo $this->sitemap->generate();?>
		<?php $this->load->view('footer'); ?>

</body>

</html>