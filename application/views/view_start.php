<?php \Head\Head2::generate( 'Úvodní stránka' ); ?>
<body>
	<div id="main-wrapper" class="container">
		<div class="hero-unit">
			<h1 class=""><i class='fa fa-bullhorn'></i> Codeigniter <?php echo CI_VERSION ?>, PHP <?php echo PHP_VERSION ?> je nainstalován a běží...</h1>
			<div class="well">
				<h2>pack verze: 1.6.0</h2>
				<ul>
					<li>Přidáno: Složka custom_controller do složky libraries, ve které se ukládají ostatní controllery. Momentálně je zde <strong>API_Controller</strong>, který je přizpůsoben na vnější komunikaci</li>
					<li>Přidáno: Do Head configu přibyl k jednotlivým objektům atribut "localhost", který se určuje, že se objekt vypíše pouze, pokud se ke stránce přistupuje jako z lokálního zdroje.</li>
					<li>Přidáno: Do packu byla zahrnuta offline verze jQuery (1.11)</li>
					<li>Opraveno: MY_Language: Nyní neníčí kusy url adresy, když je nastaven cizí jazyk.</li>
					<li>Přidáno: Routování nyí umožňuje rekurzivní adresování: Stačí před danou routu dát "!" (vykřičník). Vhodné pro delegování vícejazyčních rout.</li>
					<li>Přepsáno: MY_Exception třída: Přidán config, ve kterém se dá nastavit, že se errory budou ukládat do databáze; zárověň se při produkčním módu ukazuje error v templatu: errors/error_php_production.php</li>
					<li>Opraveno: V tříde Head2 byly opraveny (všechny) chybné bugy, zapřičiňující prázdné Common_Objekty.</li>
					<li>Přidáno: konstanta IS_LOCALHOST. Pozor, využívat jen v necitlivých věcech. Dá se oblafnout.</li>
					<li>Přidáno: Base\CommonModel, který obsahuje základní funkce pro práci s db (get,modify,delete...)</li>
					<li>Přidáno: Controller Pages, který je určen pro statické stránky.</li>
					<li>Přidáno: EmailForm knihovna, která lehce umožnuje vytvořit kontaktní formulář.</li>
					<li>Přidáno: soubor robots.txt</li>
					<li>Aktualizováno: Do .htaccess byla přidána podmínka na přesměrování při nedodržení www. (nutno vždy zapnout)</li>
					<li>Aktualizováno: Bootstrap na verzi 3.2</li>
					<li>Třída Head by se již neměla používat! (@deprecated)</li>
					<li>Opraveno: Chyba v chybě při 404.</li>
					<li>Opraveno: MY_exception třída a chyba v chybě</li>
					<li>Přidáno: Production 404 template</li>
					<li>Přidáno: JS knihovna pro css3 animace (scale-out, scale-in, vibrate, flash)</li>
					<li>Přidáno: CSS knihovna s bootstrap updatama (.img-center,.no-overflow)</li>
					<li>Upraveno: CSS knihovna mystyle.css, vymazány nepotřebné věci</li>
				</ul>
				<h2>pack verze: 1.5.0</h2>
				<ul>
					<li>Přepsán HTML/JS::insert($minify,$data,$name,$save_to_cache)</li>
					<li>Pokud není jinak, nastavení JS::insert bere nastavení z config složky</li>
					<li>Bere se důraz na ENVIROMENT - pokud je v modu "development", všechny debugy jsou  zapnutý, pokud
					je v módu "production", tak FB,BASE::debug a momentálně í JS::insert se chová jinak.
					</li>
					<li>Ve třídě User přibyla static funkce "can", která supluje dotaz na role. (User::can("article","edit"))</li>
					<li>Třída roles byla aktualizována a přibyla funkce reload_data() (vhodné po použití ihned po nalogování)</li>
					<li>Zavedena jednotná verze Hooku, který se dělí na Holder, který drží při sobě hooky a pak Placer, neboli jednotlivý hook, který v sobě nese všechny důležité informace.
						viz: dev/table
					
					</li>
					<li>přidána funkce DML/Helper::to_array(), vše převede na array a provede trim na jednotlivé prvky.</li>
					<li>Přidána třida URI/Link::URL(), která přináší větší čitelnost odkazů, víze níže.
						<br>
						<div class="label label-success">dev</div> Příklady na <?php echo anchor(URI\Link::URL("dev/uri")) ?>
					</li>
					<li>Kompletně přepsána Head třída:</li>
					<ul>
						<li>Nyní má namespace Head\</li>
						<li>Místo funkce add() a vracení nové třídy využívá funkce addJS(),addCSS(),addView(),addString(),addMeta()</li>
						<li>Nyní každý prvek je v head tabulce je tvořen třídou Head\Common_object($url = null, $type = Common_object::TYPE_JAVASCRIPT)</li>
						<li>Při vložení nového parametru do tabulky lze tedy vyuzit konstruktor: Head::addJS(new Head\Common_object(..))</li>
						<li>Při PRODUCTION ENVIRONMENT všechny CSS a JS zkombinuje do jednoho a minimalizuje.</li>
						<li>Pro facebook meta tag existuje funkce Head::Facebook_metas()</li>
						
					</ul>
				</ul>
				<h2>pack verze: 1.4.1</h2>
				<ul>
					<li>Přidána jQuery funkce pro validaci formulářů</li>
					<li><span class="label label-success">dev</span> K dispozici na <?php echo anchor( 'dev/form/validate' ) ?></li>
				</ul>
				<h2>pack verze: 1.4.0</h2>
				<ul>
					<li>HTMLElement změněn na HTML\Element a třída HTMLConstruct byla odstraněna.</li>
					<li>Byl přidán ModelSorter, který umožňuje lehce měnit v databízi pořadí určitých prvků (vhodné pro změny pořadí vypisovaných položek atp.)</li>
					<li>Třída message byla smazána a nahrazena třídou FlashMessage (obsahuje statické funkce ::set() a ::get()</li>
					<li>Třída UserAutoLoginModel byla upravena a tím opravena.</li>
					<li>Projekt ci_administrace se pomalu přesouvá do submodulu administrace v tomto projektu. <?php echo anchor( 'administrace', 'odkaz na administraci' ) ?></li>
					<li>Přidána funkce \HTML\JS::insert($alwaysCompile = false, $data = array(), $name = null,$compile_adv_mode = false), která vkládá do stránky odkaz na zkompilovaný JS kód</li>
				</ul>
				<h2>pack verze: 1.3.0</h2>
				<ul>
					<li>změny u HTMLElement:</li>
					<ul>
						<li>Pokud je apostrof (") v hodnotě např. u classu, zvolí se jiné ohraničení, takže se html element vždy správně vypíše.</li>
						<li>do prikazu addAttribute je mozne vlozit array a objekt. Ten je nasledne preveden do JSON formatu.</li>
						<li>byl přidán příkaz appendToAttribute($name,$value), který nepřemaže, ale vloži již do existujícího atributu další část</li>
					</ul>
					<li>Kompletní předělání Form Generatoru:</li>
					<ul>
						<li>Nyní je oddělen generátor od formuláře.</li>
						<li>Form generator pro generování využíva form\template ktery ma interface templateInterface.</li>
						<li>Template se vlozi do generatoru pres $generator->setTemplate(new BootstrapTemplate) (napr.)</li>
					</ul>
				</ul>
				<h2>pack verze: 1.2.0</h2>
				<ul>
					<li>Formulářový prvek dropdown a hidden nyní podporují extra atributy</li>
					<li>Pár věcí odstranění z administrace, stále je potřeba ji předělat / připravit</li>
					<li>DMLHelper nyní obsahuje funkci getValuesFromArrays()</li>
					<li>DML nyní obsahuje static proměnou DEBUG, která zapne detailní debugování do FireBugu</li>
					<li>Opraven bug v DML funkci dbCountRows()</li>
					<li>Přidan příkaz DML->dbDelete()</li>
					<li>DML: Při změně názvu tabulky na stejný název se nyní nenačítá znova celá cache</li>
					<li>Opravena chyba s retina tridou</li>
					<li>Zbrusu nový autoloader! viz kolonka autoloaderu</li>
					<li>Třída Head byla přesunuta do knihovny</li>
					<li>Kompletní změna DML:</li>
					<ul>
						<li>DML využívá namespace DML</li>
						<li>Hlavní soubor DML byl přejmenován na "Base"</li>
						<li>Všechny modely byly aktualizovány</li>
						<li>Soubor dml byl přejmenován na "Base"</li>
						<li>Soubor dmlbuilder byl přejmenován na "Builder"</li>
						<li>Soubor dmltable byl přejmenován na "Table"</li>
						<li>Soubor dmlhelper byl přejmenován na "Helper"</li>
						<li>Soubor dmlexception byl přejmenován na "DBException"</li>
						<li>Soubor dmlvalditator byl přejmenován na "Validator"</li>
						<li>Soubor dmlvalditatorinterface byl přejmenován na "ValidatorInterface"</li>
						<li>Soubor dmlcache byl smazán</li>

					</ul>
					<li>Přidána knihovna na user autologing. + sql create table dotaz</li>
					<li>Přidán Benchmark/Timer</li>

				</ul>
				<h2>pack verze: 1.1.1</h2>
				<ul>
					<li>Aktualizován login do administrace pro boostrap 3</li>
					<li>Knihovna Roles nyni podporuje role z jine tabulky (pres join)</li>
					<li>Menší cleanup</li>
					<li>TODO: administrace</li>
				</ul>
				<h2>pack verze: 1.1.0</h2>
				<ul>
					<li>Aktualizován bootstrap na verzi 3.1.0, bez glyphicons modulu</li>
					<li>Přidán fontAwesome css (4.0.3)</li>
					<li>Mobilní rendering nyní neumožňuje zoom</li>
					<li>Přidán respond.js pro @media v IE6-8</li>
					<li>jQuery aktualizováno na 1.10.2</li>
					<li>Tato stránka přesunuta do controlleru <strong>start</strong>.</li>
					<li>Config Header.php byl přejmenován na Head.php, aby kopíroval konvenci z head_helper.php</li>
					<li>Stránka <?php echo anchor( 'homepage' ) ?>, je ihned připravena pro psaní</li>
					<li>Pár nepotřebných souborů odstraněno</li>
					<li>DML update:</li>
					<ul>
						<li>byl přidán příkaz dbJoin(), který vrací třídu dmlJoin</li>
						<li>byl přidán příkaz dbJoinMN(), který vrací třídu dmlJoinMN</li>
						<li>Kod byl zpřehledněn, odstraněny zbytečné hooky</li>
						<li>DML nyní podporuje vnořené několikanásobné joiny</li>

					</ul>
				</ul>

			</div>
			<div class="well">
				<span class="label label-success">nově od 1.5</span>
				<h2>URI modul:</h2>
				<p>URI modul, jinak řečeno Link třída je standartizace pro zapisování url adres. Zadaná URL adresa
				může začínat i takzvanýma modifikátorama. Ty jsou momentálně tři.
				<br>
				Druhým argumentem jsou argumenty pro url, které se zapisují na konec uri. Zde je možné vložit string nebo array.
				</p>
				<h3>URI modifikátory:</h3>
				<ul>
					<li><strong>!</strong> - Určuje, že odkaz bude mířit do současnýho modulu </li>
					<li><strong>?</strong> - Na konec url adresy se vloží segment s CSRF tokenem </li>
					<li><strong>:</strong> - signál, že se jedná o externí url adresu a tak se zkontroluje "http://" prefix a nic víc se s tím dělat nebude.</li>
					<li><strong>$</strong> - Danou url přidá za stávající. Dejte si pozor, aby adresa neodkazovala na stejnou, pak dojde k řetězení.</li>
				</ul>
				<h3>Přiklady použití: (uvažujme, že jsme na adrese <?php echo site_url('dev/form') ?>) </h3>
				<pre class="prettyprint">
echo URI\Link::URL("form");</pre>
				výsledek: <?php echo URI\Link::URL("form") ?>
				<br>
				<br>
				<pre class="prettyprint">
echo URI\Link::URL("!form");</pre>
				výsledek: <?php echo URI\Link::URL("dev/form") ?>
				<br>
				<br>
				<pre class="prettyprint">
echo URI\Link::URL("!?form");</pre>
				výsledek: <?php echo URI\Link::URL("?dev/form") ?>
				<br>
				<br>
				<pre class="prettyprint">
echo URI\Link::URL(":example.com");</pre>
				výsledek: <?php echo URI\Link::URL(":example.com") ?>
				<br>
				<br>
				<pre class="prettyprint">
echo URI\Link::URL("!articles",1);</pre>
				výsledek: <?php echo URI\Link::URL("dev/articles",1) ?>
				<br>	
				<br>	
				<pre class="prettyprint">
echo URI\Link::URL("$articles");</pre>
				výsledek: <?php echo URI\Link::URL('$articles') ?>
				<br>	
				<br>	
				<pre class="prettyprint">
echo URI\Link::URL("!articles",array(1,"edit","key" => "value"));</pre>
				výsledek: <?php echo URI\Link::URL("dev/articles",array(1,"edit","key" => "value")) ?>
			</div>
			
			
			<div class="well">
				<span class="label label-success">nově od 1.4.1</span>
				<h2>jQuery validation plugin:</h2>
				<p>Plugin obstarává všechen typ formulářových pravidel, které se normálně používají.</p>
				<h3>Použití: (všude jsou dané defaultní hodnoty)</h3>
				<pre class="prettyprint">
$('#formular').validate({
	ajax: false,					// Formular se odesle na pozadí
	showTooltip: true,				// Zobrazi error v bootstrap tooltipu
	tooltipPosition: 'right',			// Pozice bootstrap tooltipu
	ajaxText: "Odesílám...",			// Text submitu při odesílání formuláře	
	validateMsg: "Validuji formulář...",		// Text submitu při validaci
	defaultSubmitOnSuccess: "Uloženo",		// Text submitu po validaci
	customValidation: undefined,			// Pridani dalsich validacnich funkci (nazevValidace: function(value, ref, arg) {...})
	onShowError: undefined,				// Prepsani show error funkce (attributy: element, message)
	onBeforeCheck: undefined,			// Event volany PRED validaci
	onSuccess: undefined,				// Event volany po uspesnem odeslani - pri return false se form. neodesle
	onFail: undefined				//	Event volany pri nezdaru validace
});					
				</pre>
			</div>
			<div class="well">
				<span class="label label-success">nově od 1.4.0</span>
				<h2>JSShrink plugin:</h2>
				<p>Pakliže vkládáte do view HTML souboru mnoho javascriptu, nelíbí se to různým SEO analyzátorům. 
					A práve tento JSShrinker umožní psát javascript kód automaticky vygenerované view šabloně (plně podporuje PHP), kterou 
					následně překonvertuje do javascriptu a zkompiluje. Do výsledné view složky vypíše akorát odkaz na soubor.
				</p>
				<h3>Použití:</h3>
				<p>Do spodu stránky (pod poslední div) vložte:</p>
				<pre class="prettyprint">
\HTML\JS::insert();						
				</pre>
				<p>Toto stačí, aby se automaticky dalším načtením stránky vygeneroval soubor určený pro javascript ve složce "view/js/...".
					Nebojte, značky -script- se samy vymažou.
					<br>
					<br>
					Další atributy se dočtete z anotací.
				</p>
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
				<p>Nejlepší autoloading na světě! Vše co jednou zavoláte, tak se uloží do cache a příště už to nic nehledá</p>
				<p>Není potřeba statické třídy nějak inicializovat, resp. to už si každej udelá sám</p>
				<h3>Nová třída autoload_finder:</h3>
				<p>Slouží k hledání souborů. Jediná funkce, která je potřeba je autoload_finder->find($file);
					<br>
					ke které se přistupuje přes statickou proměnou ve třídě Autoloader;
				</p>
				<pre class="prettyprint">
Autoloader::$finder->find($file);
				</pre>
				<h3>Nová třída autoload_cache:</h3>
				<p>Obsahuje všechny cesty k souborům, které se načítají. Zde je výčet potřebných funkcí:</p>
				<ul>
					<li>ban($filename)</li>
					<li>unBan($filename)</li>
					<li>commit($overwrite = false)</li>
				</ul>

			</div>
			<div class="well">
				<h2>\HTML\Element plugin:</h2>
				<p>Skvělá static třída pro buildování HTML prvků.
					<br>
					volá se přes: $this->load->helper('htmlelement');
					<br>
					Obsahuje inteligentní pretty formatting, takže vše v kódu odsazuje jak má.
					Počáteční odsazení můžete nastavit přes setFirstIndent($int);	
				</p>
				<h3>Příklad:</h3>
				<pre class="prettyprint">
$a = \HTML\Element::open('a')->addAttribute('href',base_url('hlavnistrana'));
$img = \HTML\Element::open( 'img' )->isPair( false )
				  ->addAttribute( 'class', 'big' )
				  ->addAttribute( 'src','xxx.jpg');
$a->append($img)->generate();
				</pre>
				Vygeneruje:
				<pre class="prettyprint">
&lt;a href='http://xxxx/hlavnistrana'&gt;
	&lt;img src='xxx.jpg' class='big'&gt;
&lt;/a&gt;
				</pre>

				<h3>Příklad 2, využití příkazu next() a appendString():</h3>
				<pre class="prettyprint">
$div = \HTML\Element::open('div')
	->addAttribute('id','main');

$div2 = \HTML\Element::open('div');

$div->next($div2);
$div2->appendString('&lt;h1&gt;Titulek&lt;/h1&gt;')	// appendString vlozi pouze string
		->append(\HTML\Element::open('p')
		->appendString('Text v paragrafu'));
				</pre>
				Vygeneruje:
				<pre class="prettyprint">
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
	<?php \Head\Head2::generateDeferred(); ?>
</body>
</html>