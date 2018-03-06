<?php
	
/**
 * Wolly_Newsletter_Mailchimp
 *
 * @package Content & newsletter management
 * @subpackage Mailchimp management
 */
 
if ( ! defined( 'ABSPATH' ) ) {
     exit; // Exit if accessed directly
 	}
	 
	 
class Wolly_Content_Newsletter_Creation_Mailchimp_Utility{
	 
	/**
	 * mailchimp_options_name
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mailchimp_options_name;
 

	/**
	 * mailchimp_options
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mailchimp_options;
 
 
	/**
	 * mailchimo_lists_options_name
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mailchimp_lists_options_name;
 
	/**
	 * mailchimo_lists_options
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mailchimp_lists_options;
 
 
	/**
	 * all_lists
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	var $all_lists = array();
 
 
	/**
	 * member_count
	 * 
	 * @var mixed
	 * @access public
	 */
	var $member_count;
 
 
	/**
	 * options
	 * 
	 * @var mixed
	 * @access public
	 */
	var $options;
 
 
	/**
	 * content_options
	 * 
	 * @var mixed
	 * @access public
	 */
	var $content_options;

	 
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
			 
		$this->options 			= get_option( 'wolly_mailchimp_options' );
		$this->content_options 	= get_option( 'content_creation_for_newsletter' );
		
		add_filter ('the_content', array( $this, 'insert_subscribe_newsLetter' ) );
		add_action( 'subscribe_archive_newsletter', array( $this, 'add_subscribe_newsletter_button_to_archive' ) );
		
		if ( 1 == $this->content_options['main_query'] ){
		 	
			add_action( 'pre_get_posts', array( $this, 'add_newsletter_to_query' ) );
			
		}
		
		$this->mailchimp_options_name 			= 'wolly-mailchimp-options';
		$this->mailchimp_options 				= get_option( $this->mailchimp_options_name );
		
		$this->mailchimp_lists_options_name 	= 'wolly-mailchimp-lists-options';
		$this->mailchimp_lists_options 			= get_option( $this->mailchimp_lists_options_name );
	
	}
	 
/**
 * insert_subscribe_newsLetter function.
 * 
 * @access public
 * @param mixed $content
 * @return void
 */
public function insert_subscribe_newsLetter( $content ) {

	if ( is_single() ) {

		$content .= '<div style="border:1px dotted #000; text-align:center; padding:10px;">';
		$content .= __( '<h4>I have a newsletter about WordPress</h4>', 'content-creation-newsletter' );
		$content .= __( '<p><a href="' . site_url( '/newsletter/', 'https' ) . '">This is the archive</a></p>', 'content-creation-newsletter' );
		$content .= do_shortcode( '[mc4wp_form id="6783"]' );
		//$content .= do_shortcode( '[nm-mc-form fid="1"]' );
		$content .= '</div>';


	}
	return $content;
}


/**
 * add_newsletter_to_query function.
 * 
 * @access public
 * @param mixed $query
 * @return void
 */
public function add_newsletter_to_query( $query ) {

	if ( is_home() && $query->is_main_query() ){

		$query->set( 'post_type', array( 'post', 'mynewsletters' ) );

		}

	return $query;
}

/**
 * add_subscribe_newsletter_button_to_archive function.
 * 
 * @access public
 * @return void
 */
public function add_subscribe_newsletter_button_to_archive(){

	if ( is_post_type_archive( 'mynewsletters' ) ){
		
		$all_lists = $this->get_mailchimp_lists();

		$this->get_mailchimp_member_count();
		
		if ( ! empty( $all_lists ) && is_array( $all_lists ) ){
        
        	foreach ( $all_lists as $key => $list ){

				echo '<p>Iscritti alla Newsletter ' . $this->mailchimp_options['lists'][$key]['name'] . ': <strong>' . $this->mailchimp_options['lists'][$key]['count'] . '</strong></p>';
				echo '<p>Ultimo aggiornamento iscritti: <strong>' . date( 'd-m-Y H:i e', $this->mailchimp_options['timestamp'] ) . '</strong></p>';
				echo '<p>Prossimo aggiornamento iscritti: <strong>' . date( 'd-m-Y H:i e', ( $this->mailchimp_options['timestamp'] + 86400 ) ) . '</strong></p>';
				echo do_shortcode( '[mc4wp_form id="6783"]' );
			}
		}
	}
}

/**
 * get_mailchimp_member_count function.
 * 
 * @access private
 * @return void
 */
private function get_mailchimp_member_count(){

	if ( ( time() - $this->mailchimp_options['timestamp'] ) > 86400 ){
		
		$args = array();
		$args['timestamp'] = time();
		
		$all_lists = $this->get_mailchimp_lists();
		
		if ( ! empty( $all_lists ) && is_array( $all_lists ) ){
        
        	foreach ( $all_lists as $key => $list ){
	        
				$curl_json = $this->curl_get_mailchimp_member_count( $key );

				$list_obj =  json_decode( $curl_json );

				$args['lists'][$list_obj->id] = array(
													'name' 	=> $list_obj->name,
													'count'	=> $list_obj->stats->member_count,
													);
		
			}
		}

		update_option( $this->mailchimp_options_name, $args );
		$this->mailchimp_options = get_option( $this->mailchimp_options_name );
		



	} 

	
}

/**
 * get_mailchimp_lists function.
 * 
 * @access public
 * @return void
 */
public function get_mailchimp_lists(){
	
	if ( ( time() - $this->mailchimp_lists_options['timestamp'] ) > 86400 ){
	
		$lists = array();
	
		$all_lists_curl_json = $this->curl_get_mailchimp_lists();
	
		$list_obj = json_decode( $all_lists_curl_json );
		
			
		if ( ! empty( $list_obj ) ){
					
			$lists['timestamp'] = time();
		
			foreach ( $list_obj->lists as $list ){
			
				$lists['lists'][$list->id] = $list->name;
								
			}
		
		}
	
		update_option( $this->mailchimp_lists_options_name, $lists );
		$this->mailchimp_lists_options = get_option( $this->mailchimp_lists_options_name );
		$this->all_lists = $lists['lists'];
	
	} else {
		
		$this->all_lists = $this->mailchimp_lists_options['lists'];
	}
	
	
	return $this->all_lists;
	
	
}

/**
 * curl_get_mailchimp_member_count function.
 * 
 * @access private
 * @param mixed $key
 * @return void
 */
private function curl_get_mailchimp_member_count( $key ){

	$curl = curl_init();
	curl_setopt ( $curl, CURLOPT_URL, $this->options['mailchimp-url'] . 'lists/' . $key );
	curl_setopt($curl, CURLOPT_USERPWD, 'anystring:' . $this->options['mailchimp-api'] );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec ($curl);
	curl_close ($curl);

	return $result;
}

/**
 * curl_get_mailchimp_lists function.
 * 
 * @access private
 * @return void
 */
private function curl_get_mailchimp_lists(){

	$curl = curl_init();
	curl_setopt ( $curl, CURLOPT_URL, $this->options['mailchimp-url'] . 'lists/' );
	curl_setopt($curl, CURLOPT_USERPWD, 'anystring:' . $this->options['mailchimp-api'] );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec ($curl);
	curl_close ($curl);

	return $result;
}

	 
	 
}

$Wolly_Content_Newsletter_Creation_Mailchimp_Utility = new Wolly_Content_Newsletter_Creation_Mailchimp_Utility();