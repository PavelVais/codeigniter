<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property MY_Calendar $calendar
 * @property CI_Session $session
 * @property Roles $roles
 * @property File_Browser $file_browser
 */
class Articles extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
		$this->navigator->add( 'administrace/articles/list', 'Seznam článků','fa-files-o' );
	}

	public function index()
	{
		$this->entries();
	}

	public function add()
	{
		$this->navigator->add( 'administrace/articles/add', 'Přidání článku','fa-pencil' );
		$data['form'] = $this->_createFormArticle();
		$data['title'] = 'Přidání nového článku';
		//$data['form_author'] = $this->_create_form_author();
		//$data['form_visibility'] = $this->_create_form_roles();
		//$data['form_fcb'] = $this->_create_form_fcb_app();
		$this->load->view( "administrace/articles/view_editor", $data );
	}
	
	public function edit($id)
	{
		$nw = new \Model\Article\ArticleModel();

		$news = $nw->get_news( $id );
		$data['news'] = $news;
		$data['form'] = $this->_createFormArticle( $news );
		$data['title'] = 'Editace článku: '.$news->title;

		$this->navigator->add( 'administrace/articles/add', 'Editace článku #'.$news->id,'fa-pencil' );
		$this->load->view( "administrace/articles/view_editor", $data );
	}
		
	
	
	public function preview($id)
	{
		$nw = new NovinkyModel();
		$news = $nw->get_news( $id );

		/* $config['header']['meta'][] = array(
		  'property' => 'og:mark',
		  'content' => 'blahblah',
		  //'restriction' => 'fcb' */

		$meta['title'] = array(
			 'property' => "og:title",
			 "content" => isset( $news->meta['og:title'] ) ? $news->meta['og:title'] : $news->title
		);
		$meta['url'] = array(
			 'property' => "og:url",
			 "content" => current_url()
		);
		$meta['description'] = array(
			 'property' => "og:description",
			 "content" => isset( $news->meta['og:description'] ) ? $news->meta['og:description'] : $news->description
		);
		$meta['type'] = array(
			 'property' => "og:type",
			 "content" => isset( $news->meta['og:type'] ) ? $news->meta['og:type'] : "article"
		);


		if ( isset( $news->meta['og:image'] ) )
		{
			$image = array(
				 'property' => "og:image",
				 "content" => base_url( "ftp/uploads/facebook/" . $news->meta['og:image'] )
			);
			$this->header->addParam( "meta", $image );
		}

		foreach ( $meta as $m )
		{
			$this->header->addParam( "meta", $m );
		}


		$data['news'] = $news;
		$data['rightPanel']['title'] = "Dodatečné informace";
		$data['rightPanel']['inner'] = "";
		$data['rightPanel']['footer'] = "";
		$this->load->view( "administrace/novinky/view_preview", $data );
	}

	public function entries()
	{
	
		$nw = new Model\Article\ArticleModel();
		$data['articles'] = $nw->get_all_news( 1, 1 );
		$this->load->view( "administrace/articles/view_list", $data );
	}


	public function ulozit($arg = null)
	{
		$title = $this->input->post( "title" );
		$news_id = $this->input->post( "id" );
		$description = $this->input->post( "description" );
		$keywords = $this->input->post( "keywords" );
		$message = $this->input->post( "message" );

		$data = array(
			 "title" => $title,
			 "description" => $description,
			 "keywords" => $keywords,
			 "message" => $message,
			 "author_id" => 1,
			 "date_created" => DMLHelper::now( TRUE )
		);

		$nm = new NovinkyModel();
		$nm->fetch_data( $data );

		if ( intval( $news_id ) > 0 )
			$nm->add_data( "id", $news_id );

		$result = $nm->save_news();

		if ( $result !== FALSE )
		{
			$inserted_id = intval( $news_id ) > 0 ? $news_id : $nm->last_id();
			$this->output->json_append( "response", "Článek byl úspěšně uložen." )
					  ->json_append( "news_id", $inserted_id );
		}
		else
		{
			$this->output->json_append( "response", "Nastala chyba při ukládání.", 500 );
		}


		$this->output->json_flush();
	}

	public function ulozit_meta()
	{
		$title = $this->input->post( "og:title" );
		$news_id = $this->input->post( "news_id" );
		$description = $this->input->post( "og:description" );

		$nm = new NovinkyModel();
		$nm->add_news_meta( "og:title", $title )
				  ->add_news_meta( "og:description", $description )
				  ->save_news_meta( $news_id );

		$this->output->json_append( "response", "Facebook informace k článku byly úspěšně uloženy." )
				  ->json_flush();
	}

	public function table_change()
	{
		$request = $this->input->post( "request" );
		$value = $this->input->post( "value" );
		$news_id = $this->input->post( "id" );

		$nw = new NovinkyModel();

		if ( strpos( "title|description", $request ) === FALSE )
		{
			//TODO log hack attempt!
			$this->output->json_append( "response", "Provedla se nepovolená operace, nic se neuložilo.", 500 );
		}
		else
		{
			if ( $nw->update_news_value( $news_id, $request, $value ) !== FALSE )
			{
				$this->output->json_append( "response", "Údaj se úspěšně aktualizoval" );
			}
			else
				$this->output->json_append( "response", "Provedla se nepovolená operace, nic se neuložilo.", 500 );
		}

		$this->output->json_flush();
	}

	/**
	 * Pripravi formular pro editaci a pridani clanku
	 * @param type $news_active_row
	 * @return \Form\Generator
	 */
	private function _createFormArticle($news_active_row = null)
	{
		$form = new Form\Form( "novinky/ulozit" );

		$form->set_form_attribute( "id", "news-editor" );

		$form->addText( "title", "Titulek:", 20, 100 )
				  ->setRule( Form\Form::RULE_FILLED, "Novinka musí obsahovat titulek." )
				  ->setRule( Form\Form::RULE_MIN_CHARS, "Titulek je moc krátký, zkuste ho rozvést.", 5 )
				  ->setAttribute( "placeholder", "vložte titulek novinky" )
				  ->addTextArea( "description", "Krátký popis:", 38, 5 )
				  ->setAttribute( "placeholder", "krátký popis vaší zprávy." )
				  ->addHook( \HTML\Element::open( 'input' )
							 ->addAttribute( 'type', 'hidden' )
							 ->addAttribute( 'id', 'frm_keywords' ), 'Klíčová slova:' )
				  ->addTextArea( "message", "Tělo zprávy:", 30, 10 )
				  ->setAttribute( "class", "mceditor" );
		$form->setSubmit( "save", "uložit zprávu" )
				  ->setAttribute( "class", "btn btn-primary btn-block" );

		if ( $news_active_row !== null )
			$form->setValue( $news_active_row );

		$formGenerator = new Form\Generator( $form );
		$formGenerator->setTemplate( new Form\BootstrapTemplate() );

		return $formGenerator;
	}

	private function _create_form_roles($news_meta_active_row = null)
	{
		$form = new Form( "novinky/ulozit/role" );
		$this->load->library( "roles" );
		$form->addText( "visible_since", "viditelné od:", 12, 12 )
				  ->set_attribute( "placeholder", "dd/mm/yyyy" );

		$form->addCheckbox( "hide_news", "1", FALSE, "Skrýt novinku:" );

		//= buildovani dropdown menu pro vyber role pro dany ucet
		if ( $this->roles->allowed( "set_news_visibility", "change" ) )
		{
			$form->addDropdown( "role_visibility", "Viditelné od", "unregistered" );

			foreach ( $this->roles->get_roles() as $role )
			{
				if ( $this->roles->allowed( "news_visibility", $role ) )
				{
					$form->addDropdownOption( $role, $role );
				}
			}
		}



		$form->setSubmit( "save", "uložit informace" )
				  ->set_attribute( "class", "button first last center" );

		if ( $news_meta_active_row !== null )
			$form->set_value( $news_meta_active_row );

		return $form;
	}

	private function _create_form_fcb_app($news_meta_active_row = null)
	{
		$form = new Form( "novinky/ulozit-meta" );
		$form->set_form_attribute( "id", "news-fcb" );
		$form->addText( "og:title", "Nadpis:", 10, 100 )
				  ->set_attribute( "placeholder", "nadpis novinky" );
		$form->addTextArea( "og:description", "Popisek:", 19, 5 )
				  ->set_attribute( "placeholder", "popisek vyskytující se na fcb zdi" );

		$form->setSubmit( "save", "uložit informace" )
				  ->set_attribute( "class", "button first last center" );

		if ( isset( $news_meta_active_row->meta ) && count( $news_meta_active_row->meta ) > 0 )
			$form->set_value( json_decode( json_encode( $news_meta_active_row->meta ), FALSE ) );

		return $form;
	}

	private function _create_form_author($news_active_row = null)
	{
		$form = new Form( "novinky/ulozit/author" );
		$form->addText( "author_name", "Autor:", 10, 100 )
				  ->set_attribute( "placeholder", "Název autora" );

		$form->setSubmit( "save", "uložit název autora" )
				  ->set_attribute( "class", "button first last center" );

		if ( $news_active_row !== null )
			$form->set_value( $news_active_row );

		return $form;
	}

	public function prochazet($path = "")
	{
		$this->load->helper( 'text' );
		$data = array();
		$a = $path;
		$path = rawurldecode( rawurldecode( $path ) );
		$this->load->library( "File_Browser" );
		//dump($this->file_browser->get_directory("ftp"));

		$this->file_browser->set_path( $path );
		$data['folder'] = $this->file_browser->get_directory();
		$data['links'] = $this->file_browser->generate_link_map();

		$data['rightPanel'] = array(
			 'title' => "",
			 'inner' => "",
			 'footer' => ""
		);


		$this->load->view( 'administrace/soubory/view_prochazet', $data );
	}

	public function adresar($path = null)
	{
		$path = $this->input->post( 'dir' ) == false ? $path : $this->input->post( 'dir' );
		if ( !$this->input->is_ajax_request() )
			return false;
		$this->load->library( "File_Browser" );
		$this->load->helper( 'text' );

		$path = rawurldecode( rawurldecode( $path ) );
		$this->file_browser->set_path( $path );
		$data['folder'] = $this->file_browser->get_directory();
		$data['links'] = $this->file_browser->generate_link_map();
		$this->output->json_append( 'response', $this->load->view( 'administrace/soubory/tmpl_directory', $data, TRUE ) )
				  ->json_append( 'current_url', $path )
				  ->json_flush();
	}

	public function informace($path = null)
	{
		if ( !$this->input->is_ajax_request() )
			return false;
		$path = $this->input->post( 'dir' );

		if ( $path != null )
			$path .= "/";

		$files = $this->input->post( 'files' );

		$this->load->library( "File_Browser" );
		$this->load->helper( "czech" );
		$this->file_browser->set_path( $path );
		$result = $this->file_browser->get_file_info( $files );

		$data['title'] = $result['count'] == 1 ? "Detail souboru: " . $files[0] : "Detail " . $result['count'] . " " . cestina( $result['count'], "souboru", "souborů", "souborů" );

		if ( $result['count'] == 1 )
		{
			if ( $result['is_img'] )
			{
				$prop = $this->_get_proporcion( $result['img_width'], $result['img_height'], 190 );
				$style = "style='margin-left: " . ((180 / 2) - ($prop[0] / 2)) . "px'";
				$data['inner'] = "<img src='" . base_url( "ftp/" . $path . $files[0] ) . "' $style width='$prop[0]' height='$prop[1]' />";
				$data['inner'] = anchor_popup( "ftp/" . $path . $files[0], $data['inner'] );
			}
			else
			{
				$prop = $this->_get_proporcion( 512, 512, 115 );
				$style = "style='margin-left: " . ((180 / 2) - ($prop[0] / 2)) . "px'";
				$a = $this->file_browser->get_ico_image( $path . $files[0] );
				$data['inner'] = "<img src='" . base_url( "images/file_types/512px/" . $a ) . "' $style width='$prop[0]' height='$prop[1]'/>";
			}
		}
		else
			$data['inner'] = "<img src='" . base_url( "images/file_types/512px/_multiple-files.png" ) . "' style='margin-left:31px' width='117'/>";



		$data['inner'] .= "<p>Velikost: " . $this->file_browser->format_file_size( $result['size'] ) . "</p>";
		$data['inner'] .= "<p>Označeno " . $result['count'] . " " . cestina( $result['count'], "souboru", "souborů", "souborů" ) . "</p>";
		$data['footer'] = null;

		$this->output->json_append( 'response', $this->load->view( 'administrace/soubory/tmpl_right_panel', $data, TRUE ) )
				  ->json_flush();
	}

	private function _get_proporcion($x, $y, $max_x)
	{
		if ( $x < $max_x )
			return array($x, $y);

		$r = $x / $max_x;
		$x = $max_x;
		$y = $y / $r;
		return array($x, $y);
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */