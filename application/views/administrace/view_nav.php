<div id="right-navigation" class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav" id="side-menu">
			<li class="sidebar-search">
				<div class="input-group custom-search-form">
					<input type="text" class="form-control" placeholder="Search...">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button">
							<i class="fa fa-search"></i>
						</button>
					</span>
				</div>
				<!-- /input-group -->
			</li>
			<li class="text-center">
				<br>
				<img class="img-circle" alt="User Avatar" src="http://placehold.it/90/55C1E7/fff">
				<p>Pavel Vais (administrátor)</p>
			</li>
			<li>
				<a href="<?php echo site_url( 'administrace' ) ?>"><i class="fa fa-dashboard fa-fw"></i> Hlavní strana</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-th-list fa-fw"></i> Články<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="<?php echo site_url( 'administrace/articles/add' ) ?>"><i class="fa fa-pencil"></i> nový článek</a>
					</li>
					<li>
						<a href="<?php echo site_url( 'administrace/articles/list' ) ?>"><i class="fa fa-list"></i> seznam článků</a>
					</li>
					<li>
						<a href="<?php echo site_url( 'administrace/keywords' ) ?>"><i class="fa fa-tags"></i> správa klíčových slov</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="#"><i class="fa fa-users fa-fw"></i> Účty<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="<?php echo site_url( 'administrace/accounts/new' ) ?>"><i class="fa fa-plus"></i> nový účet</a>
					</li>
					<li>
						<a href="<?php echo site_url( 'administrace/accounts/' ) ?>"><i class="fa fa-list"></i> seznam účtů</a>
					</li>
					
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="#"><i class="fa fa-files-o fa-fw"></i> Soubory<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="<?php echo site_url( 'administrace/articles/new' ) ?>"><i class="fa fa-floppy-o"></i> nahrát nový soubor</a>
					</li>
					<li>
						<a href="<?php echo site_url( 'administrace/articles/new' ) ?>"><i class="fa fa-table"></i> prohlížeč souborů</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="<?php echo site_url( 'administrace/visitors/' ) ?>"><i class="fa fa-bar-chart-o fa-fw"></i> Statistika přístupů</a>

				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="<?php echo site_url( 'administrace/errors/' ) ?>"><i class="fa fa-bolt fa-fw"></i> Chyby na serveru</a>

				<!-- /.nav-second-level -->
			</li>

		</ul>
		<!-- /#side-menu -->
	</div>
	<!-- /.sidebar-collapse -->
</div>