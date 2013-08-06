<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Administracni rozcestnik
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth
 * @property DbBackup $dbbackup
 * @property Roles $roles
 * @property Cache $cache
 * @property Facebook $facebook
 */
class Priznani extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library( 'message' );
		$this->load->helper( "text" );
	}

	public function index()
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['confessions_new'] = $cm->get_new();
		$data['confessions_old'] = $cm->get_old( 10 );
		$data['confessions_deleted'] = $cm->get_deleted( 5 );
		$data['hashtags'] = $hm->get();
		$data['count_hashtags'] = $hm->count();

		$this->load->view( 'administrace/priznani/view_index', $data );
	}

	public function seznam($strana = 1)
	{
		$this->load->library( 'pagination' );

		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['count_hashtags'] = $hm->count();
		$vm = new VariablesModel();
		$data['page_id'] = $vm->get("fcb_target_id");
		$data['access_token'] = $vm->get("fcb_access_token");
		$this->_init_paginator( $cm->count(), "administrace/priznani/seznam" );



		$data['confessions'] = $cm->page( $strana )->get_old();

		$this->load->view( 'administrace/priznani/view_seznam', $data );

		$this->pagination->create_links();
	}

	public function zamitnute($strana = 1)
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['count_hashtags'] = $hm->count();
		$data['confessions'] = $cm->page( $strana )->get_deleted();

		$this->_init_paginator( $cm->count( true ), "administrace/priznani/seznam" );

		$this->load->view( 'administrace/priznani/view_zamitnute', $data );

		$this->pagination->create_links();
	}

	public function editace($id)
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['count_hashtags'] = $hm->count();
		$data['confession'] = $cm->get_by_id( $id );

		if ( !$data['confession'] )
		{
			$this->session->set_flashdata( "error", "Přiznání #$id nebylo nalezeno." );
			redirect( "administrace/priznani" );
		}
		$form = new Form( "administrace/priznani/ulozit" );
		$form->addTextArea( "conf_text", "Text přiznání", 30, 5 )
				  ->set_rule( Form::RULE_FILLED, "Přiznání musí mít text" )
				  ->set_value( $data['confession']->text )
				  ->set_attribute( "class", "span8" )
				  ->addHidden( "conf_id", $id )
				  ->addText( "conf_hashtag" )
				  ->set_value( $data['confession']->hashtag === NULL ? "" : HashtagsModel::prepare_hashtag( $data['confession']->hashtag )  )
				  ->set_attribute( "class", "span8" )
				  ->set_rule( "nospace", "Hashtag nesmí obsahovat žádnou mezeru." )
				  ->set_rule( "onlychars", "Hashtag musí obsahovat jenom písmena." )
				  ->set_form_attribute( "id", "form-edit" )
				  ->setSubmit( "save", "Uložit přiznání" );
		$data['form'] = $form;

		$this->load->view( 'administrace/priznani/view_editace', $data );
	}

	public function ulozit()
	{
		$text = $this->input->post( "conf_text" );
		$id = $this->input->post( "conf_id" );
		$hashtag = $this->input->post( "conf_hashtag" );

		$hashtag = HashtagsModel::prepare_hashtag( $hashtag ); // odstranime # a mezery

		$cm = new ConfessionModel;
		if ( $cm->edit( $id, $text, $hashtag ) == true )
		{
			$this->session->set_flashdata( "admin", "Přiznání #$id bylo úspěšně upraveno." );
			redirect( "administrace/priznani" );
		}
		else
		{
			$this->session->set_flashdata( "error", "Přiznání #$id se nepovedlo uložit." );
			redirect( "administrace/priznani" );
		}
	}

	public function smazat($id)
	{


		if ( !Secure::csrf_check( 5 ) )
		{
			$this->session->set_flashdata( "error", "S bezpečnostních důvodů nebylo přání smazáno. Proces opakujte" );
			redirect( "administrace/priznani" );
		}
		$cm = new ConfessionModel;
		$cm->approve( $id, false );

		$this->session->set_flashdata( "admin", "Přiznání #$id bylo úspěšně smazáno." );
		redirect( "administrace/priznani" );
	}

	public function smazat_natrvalo($id, $redirect = "")
	{

		if ( !Secure::csrf_check( 5 ) && !Secure::csrf_check( 6 ) )
		{
			$this->session->set_flashdata( "error", "S bezpečnostních důvodů nebylo přání smazáno. Proces opakujte" );
			redirect( "administrace/priznani" );
		}

		$cm = new ConfessionModel;
		$cm->remove( $id );

		$this->session->set_flashdata( "admin", "Přiznání #$id bylo úspěšně natrvalo smazáno." );

		if ( $this->uri->segment( 6 ) == false )
			$redirect = "";

		redirect( "administrace/priznani/$redirect" );
	}

	public function schvaleni($id, $approve, $csrf)
	{
		$cm = new ConfessionModel;
		if ( !Secure::csrf_check( $csrf ) )
		{
			$this->output->json_append( "response", "Kvůli bezpečnosti nebyl požadavek zpracován. Obnovte stránku a akci zopakujte." )
					  ->json_append( "status", 500 )
					  ->json_flush();
			return;
		}
		$approve = $approve == 1 ? TRUE : FALSE;
		$cm->approve( $id, $approve );
		if ( $approve )
			$output = "Přiznání bylo úspěšně přijato.";
		else
			$output = "Přiznání bylo úspěšně zamítnuto.";

		$this->output->json_append( "response", $output )
				  ->json_append( "approve", $approve )
				  ->json_flush();
	}

	private function _init_paginator($total_rows, $url)
	{
		$this->load->library( 'pagination' );
		$config['base_url'] = base_url( $url );
		$config['total_rows'] = $total_rows;
		$config['per_page'] = ConfessionModel::ROWS_PER_PAGE;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
		$config['full_tag_close'] = '</div></ul>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><span>';

		$config['cur_tag_close'] = '</span></li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$this->pagination->initialize( $config );
	}

	public function aa()
	{
		$this->config->load( 'facebook' );
		$config = $this->config->item( 'facebook' );
		$this->load->library( 'facebook', $config );
		$app_id = 'YOUR_APP_ID';
		$app_secret = "YOUR_APP_SECRET";
		$my_url = "TARGET_URL_ONCE_PERMISSION_IS_GIVEN";

		$code = isset( $_REQUEST["code"] ) ? $_REQUEST["code"] : false;


		if ( !$code )
		{
			$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" . $this->facebook->getAppId() . "&scope=publish_stream,offline_access&redirect_uri=" . urlencode( $my_url );
			echo("<script> top.location.href='" . $dialog_url . "'</script>");
		}

		$token_url = "https://graph.facebook.com/oauth/access_token?client_id="
				  . $app_id . "&redirect_uri=" . urlencode( $my_url ) . "&client_secret="
				  . $app_secret . "&code=" . $code;

		$access_token = file_get_contents( $token_url );

		$graph_url = "https://graph.facebook.com/me?" . $access_token;

		$user = json_decode( file_get_contents( $graph_url ) );

		echo("Hello " . $user->name);
	}

	public function test($confession_id = 0)
	{
		$this->config->load( 'facebook' );
		$config = $this->config->item( 'facebook' );
		$this->load->library( 'facebook', $config );
		$this->load->helper( 'text' );

		$vm = new VariablesModel();
		$page_access_token = $vm->omit_user()->get( "fcb_access_token" );
		$page_id = $vm->omit_user()->get( "fcb_target_id" );
		if ( $page_id != false )
			$page_id = substr( $page_id, 1 );


		$cm = new ConfessionModel;
		$result = $cm->get_by_id( $confession_id );

		if ( !$result )
			return false;

		$include_url = $vm->omit_user()->get( "fcb_inc_url" );
		$title = "Confession #$result->id | makeconfession.com";
		if ( $include_url == "ano" )
			$args = array(
				 'access_token' => $result->text,
				 'message' => $result->text,
				 'name' => 'Confession #' . $result->id . ' | makeconfession.com',
				 'caption' => 'Confession #' . $result->id . ($result->hashtag !== "" ? " with hashtag " + $result->hashtag : ""),
				 'link' => base_url( "confession/" . $result->id ),
				 'description' => character_limiter( $result->text, 90 ),
				 'picture' => base_url("images/fcb_sharer.png"),
			);
		else
			$args = array(
				 'access_token' => $page_access_token,
				 'message' => $result->text,
			);

		//$page_id = '589091771131640';
		//$page_id = '208125939336641';




		//$page_access_token = 'CAACYWTlzXl0BAAhS5gTHnmWF0wOTsPXZAqNfeJWlETgyCtVBIHGPkW3iFZAJJwa1RlWZCZBnfJMg9RBlJ8AJVxJQOztNSF0ZB16kIP5lSaHrh9iIc7HKrv6ZAUmnMgKxcZC0OrM9e3p2ZC60ySGgvoIzIKzZCdnShVTRBjZCiZAZCgOwq8AB6ZCF9K8Qv';
		// Get User ID
		// $user supposed to be page admin

		try
		{
			$post_id = $this->facebook->api( "/$page_id/feed", "post", $args );
			dump( "post", $post_id );
		}
		catch (FacebookApiException $e)
		{
			dump( $e );
			$user = null;
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */