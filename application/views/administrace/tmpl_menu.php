<?php  $s = $this->uri->segment(2) ?>
<?php  $s2 = $this->uri->segment(3) ?>
<ul class="nav nav-list">
	<li class="nav-header">Rozcestník</li>
	<li <?php echo $s == false ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/" ) ?>"><i class="icon-home icon-black"></i> Dashboard</a></li>
	<li class="divider"></li>
	<li <?php echo $s == "priznani" && !$s2 ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/priznani" ) ?>"><i class="icon-comment icon-black"></i> Přiznání <span id="count-confession" class="badge badge-warning"><?php echo $count_confessions ?></span></a></li>
	<ul class="nav nav-list">
		<li <?php echo $s2 == "seznam" ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/priznani/seznam" ) ?>"><i class="icon-ok icon-black"></i> Přijatá přiznání</a></li>
		<li <?php echo $s2 == "zamitnute" ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/priznani/zamitnute" ) ?>"><i class="icon-remove icon-black"></i> Smazaná přiznání</a></li>

	</ul>
	<li class="divider"></li>
	<li <?php echo $s == "hashtagy" ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/hashtagy" ) ?>"><i class="icon-tags icon-black"></i> Hashtagy <span id="count-hashtags" class="badge badge-warning"><?php echo $count_hashtags ?></span></a></li>
	<li class="divider"></li>
	<li class="nav-header">přihlášen jako <?php echo User::get_username() ?></li>
	<li <?php echo $s == "ucty" ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/ucty/zmena-hesla" ) ?>"><i class="icon-qrcode icon-black"></i> Změnit profil</a></li>
	<li <?php echo $s == "nastaveni" ? 'class="active"' : '' ?>><a href="<?php echo base_url( "administrace/nastaveni" ) ?>"><i class="icon-hdd icon-black"></i> Osatní nastavení</a></li>
	<li><a href="<?php echo base_url( "administrace/login/out" ) ?>"><i class="icon-off icon-black"></i> Odhlásit se</a></li>
</ul>