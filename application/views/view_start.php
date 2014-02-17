<?php Head::generate( 'Úvodní stránka', FALSE ); ?>
<script>
	$(document).ready(function() {
	});
</script>
<?php Head::close(); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="hero-unit">
			<h1 class=""><i class='fa fa-bullhorn'></i> Codeigniter <?php echo CI_VERSION ?> je nainstalován a běží...</h1>
			<div class="well">
				<h2>pack verze: 1.1.0</h2>
				<ul>
					<li>Aktualizován bootstrap na verzi 3.1.0, bez glyphicons modulu</li>
					<li>Přidán fontAwesome css (4.0.3)</li>
					<li>Mobilní rendering nyní neumožňuje zoom</li>
					<li>Přidán respond.js pro @media v IE6-8</li>
					<li>jQuery aktualizováno na 1.10.2</li>
					<li>Tato stránka přesunuta do controlleru <strong>start</strong>.</li>
					<li>Config Header.php byl přejmenován na Head.php, aby kopíroval konvenci z head_helper.php</li>
					<li>Stránka <?php echo anchor('homepage') ?>, je ihned připravena pro psaní</li>
					<li>Pár nepotřebných souborů odstraněno</li>

				</ul>

			</div>
			<div class="well">
				<h2>il8n plugin:</h2>
				<p><strong>argument test: </strong>
					<?php echo $this->lang->line( 'debug.arguments', 'Pavel', 25 ) ?>
					<br>
					<span style="color: #9F6000;">
						<?php echo strtr( ' $this->lang->line( \'debug.arguments\',\'Pavel\',25 )', Array("<" => "&lt;", "&" => "&amp;") ); ?>
					</span>
				</p>
				<p><strong>actual language: </strong>
					<?php echo $this->lang->lang() ?>
					<br>
					<strong>switch to <?php echo $this->lang->lang() == 'en' ? 'czech' : 'english' ?>: </strong>
					<?php echo anchor( $this->lang->switch_uri( $this->lang->lang() == 'en' ? 'cs' : 'en'  ) ); ?>
					<br>
					<span style="color: #9F6000;">
						<?php echo strtr( ' echo anchor($this->lang->switch_uri(\'' . ($this->lang->lang() == 'en' ? 'cs' : 'en') . '\'));', Array("<" => "&lt;", "&" => "&amp;") ); ?>
					</span>
				</p>
				<p>Plugin byl upraven takto:</p>
				<ul>
					<li>První jazyk je i defaultní, v url se nemusí zadat a automaticky se nastaví.</li>
					<li>Pokuď se defaultní jazyk vloží do url, automaticky se url přesměruje tak, aby jazykový identifikátor v url nebyl.</li>
					<li>Při dotazování se na <span style="color: #9F6000;">$this->uri->segment()</span> se jazykový identifikátor nebere v potaz.</li>
				</ul>
			</div>
			<div class="well">
				<h2>Maintenance plugin:</h2>
				<p>v configu je možnost nastavit maintenance mod: Nikdo z návštěvníku se nebude moci dostat na tento web. Místo toho uvidí informační hlášku.</p>
			</div>
			<div class="well">
				<h2>Autoloader plugin:</h2>
				<p>Přibyla nová autoloader třída:</p>
				<ul>
					<li>Autoload::load($názevtřídy) -> načte novou třídu, tato funkce je automaticky volána.</li>
					<li>Autoload::loadStatic($path, $class) -> načte dodatečně třídu. Pokud je v názvu třídy "::" automaticky se volá i funkce., která je po tom obsažená</li>
				</ul>
			</div>
			<div class="well">
				<h2>HTMLElement plugin:</h2>
				<p>Skvělá static třída pro buildování HTML prvků.
					<br>
					volá se přes: $this->load->helper('htmlelement');
					<br>
					Obsahuje inteligentní pretty formatting, takže vše v kódu odsazuje jak má.
					Počáteční odsazení můžete nastavit přes setFirstIndent($int);	
				</p>
				<h3>Příklad:</h3>
				<pre>
$a = HTMLElement::open('a')->addAttribute('href',base_url('hlavnistrana'));
$img = HTMLElement::open( 'img' )->isPair( false )
				  ->addAttribute( 'class', 'big' )
				  ->addAttribute( 'src','xxx.jpg');
$a->append($img)->generate();
				</pre>
				Vygeneruje:
				<pre>
&lt;a href='http://xxxx/hlavnistrana'&gt;
	&lt;img src='xxx.jpg' class='big'&gt;
&lt;/a&gt;
				</pre>
				
				<h3>Příklad 2, využití příkazu next() a appendString():</h3>
				<pre>
$div = HTMLElement::open('div')
	->addAttribute('id','main');

$div2 = HTMLElement::open('div');

$div->next($div2);
$div2->appendString('&lt;h1&gt;Titulek&lt;/h1&gt;')	// appendString vlozi pouze string
		->append(HTMLElement::open('p')
		->appendString('Text v paragrafu'));
				</pre>
				Vygeneruje:
				<pre>
&lt;div id='main'&gt;
&lt;/div&gt;
&lt;div&gt;
	&lt;h1&gt;Titulek&lt;/h1&gt;
	&lt;p&gt;Text v paragrafu&lt;/p&gt;
&lt;/div&gt;
				</pre>
				
			</div>
			<div class="well">
				<h2>UhOh a FB plugin:</h2>
				<p>Vše pro debugování.UhOh generuje nnázorné chyby s výpisem přesného řádku.
				</p>
				<br>
				<p>
					FB plugin posílá debug informace do PHP firebugu.<br>
					volá se statickou funkcí FB::info($mixed,$label); nebo FB::warning($mixed,$label);
				</p>
					
			</div>
			<div class="well">
				<h2>Annotation plugin:</h2>
				<p>
					Přibyla možnost přidávat k jednotlivým funkcím anotace!
				</p>
				<ul>
					<li>@ajax-only: Při neajaxovem požadavku se zavolá 404</li>
					<li>@logged(redirect,negation): Zkoumá, jeslti k funkci přistupuje přihlášený uživatel</li>
					<li>@role(nazev_role,redirect): Jakou roli musi mit pristupujici</li>
					<li>@role-method(typ,metoda): Splnuje dotycny danou roli?</li>
				</ul>
				Příklad:
				<pre>
/**
 * @ajax-only
 */
 public function funkcePouzeProAjax(){}
				</pre>
			</div>
			<div class="well">
				<h2>Retina hook:</h2>
				<p>Třída, která automaticky zjistí, jestli má uživatel retina display nebo ne.
					Díky tomu může přes funkci Retina::retinaImg() volat img tag, který vrátí správnou adresu na správný obrázek.
				</p>
				<p>Uživatel má retina display: <strong><?php echo Retina::isRetina() ? "ANO" : "NE" ?></strong> (Retina::isRetina())</p>
				<p>Uživatel má HD display: <strong><?php echo Retina::isHighDPI() ? "ANO" : "NE" ?></strong> (Retina::isHighResolution())</p>
				<p>Uživatel má low-res display: <strong><?php echo Retina::isLowDPI() ? "ANO" : "NE" ?></strong> (Retina::isLowResResolution())</p>
					
			</div>
			<div class="row">
				<div class="col-md-5">
					<h3>Normální snímek</h3>
					<img src="<?php echo base_url( 'images/ci/retina_test.jpg' ) ?>" title='normální snímek'>
				</div>
				<div class="col-md-5">
					<h3>Retina snímek</h3>
					<?php echo Retina::retinaImg( 'ci/retina_test.jpg' ) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-5">
					<h3>Normální snímek</h3>
					<img src="<?php echo base_url( 'images/ci/retina_test2.jpg' ) ?>" title='normální snímek'>
				</div>
				<div class="col-md-5">
					<h3>Retina snímek</h3>
					<?php echo Retina::retinaImg( 'ci/retina_test2.jpg' ) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 offset1">
					<h3>Normální snímek</h3>
					<img src="<?php echo base_url( 'images/ci/retina_test3.jpg' ) ?>" title='normální snímek'>
				</div>
				<div class="col-md-4">
					<h3>Retina snímek</h3>
					<?php echo Retina::retinaImg( 'ci/retina_test3.jpg', null, null, 'ret-300' ) ?>
				</div>
			</div>
		</div>

	</div>
</body>
</html>