<?php
	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}


class Wolly_Content_Creation_Newletter_Create{

	
    /**
     * newsletter_cpt
     * 
     * @var mixed
     * @access private
     */
    private $newsletter_cpt;
    
    /**
     * short_news_cpt
     * 
     * @var mixed
     * @access private
     */
    private $short_news_cpt;
    
    /**
     * newsletter_checkbox
     * 
     * @var mixed
     * @access private
     */
    private $newsletter_checkbox;
    
    /**
     * short_news
     * 
     * @var mixed
     * @access private
     */
    private $short_news;
    
    /**
     * meta_inserted_in_newsletter
     * 
     * @var mixed
     * @access private
     */
    private $meta_inserted_in_newsletter;
    
    /**
     * newsletter_title_content
     * 
     * @var mixed
     * @access private
     */
    private $newsletter_title_content;
    
    /**
     * print_html
     * 
     * @var mixed
     * @access private
     */
    private $print_html;
    
    /**
     * newsletter
     * 
     * @var mixed
     * @access private
     */
    private $newsletter;
    
    /**
     * options
     * 
     * @var mixed
     * @access private
     */
    private $options;
    
    /**
     * newsletter_choosen
     * 
     * (default value: '')
     * 
     * @var string
     * @access private
     */
    private $newsletter_choosen = '';
    
    /**
     * terms_extracted
     * 
     * (default value: '')
     * 
     * @var string
     * @access private
     */
    private $terms_extracted = '';
    
    /**
     * data
     * 
     * (default value: '')
     * 
     * @var string
     * @access private
     */
    private $data = '';


    
    /**
     * __construct function.
     * 
     * @access public
     * @return void
     */
    public function __construct(){

	    $this->options = get_option( 'content_creation_for_newsletter' );
	    $this->newsletter_cpt = 'newsletter';
	    $this->short_news_cpt = 'shortnews';
	    $this->newsletter_checkbox = '_newsletter_checkbox';
	    $this->meta_inserted_in_newsletter = '_inserted_in_newsletter';
	    $this->newsletter_title_content = array();

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		
        add_action( 'wp_ajax_ajax_extract_short_news', array( $this, 'ajax_extract_short_news' ) );
  		add_action( 'wp_ajax_nopriv_ajax_extract_short_news', array( $this, 'ajax_extract_short_news' ) );
    }

   
    /**
     * add_plugin_page function.
     * 
     * @access public
     * @return void
     */
    public function add_plugin_page()
    {
        add_submenu_page(
	        'newsletter-setting-admin',
			__( 'Create Newsletter', 'content-creation-newsletter' ),
			__( 'Create Newsletter', 'content-creation-newsletter' ),
			'manage_options',
			'newsletter-creator',
			array( $this, 'create_admin_page' )
			);
    }
	
    /**
     * create_admin_page function.
     * 
     * @access public
     * @return void
     */
    public function create_admin_page()
    {

		$this->newsletters = wolly_get_newletters();		

        ?>
        <div class="wrap">
            <h2><?php _e( 'Newsletter Creator', 'content-creation-newsletter' ) ?></h2>

	        <?php $screen = get_current_screen();
		       

				if ( is_array( $this->newsletters ) && ! empty( $this->newsletters ) ){

					?>
					<select name="choose_newsletter_sections" id="choose_newsletter_sections">
						<option value="-1"><?php _e( 'Choose newsletter', 'content-creation-newsletter' ) ?></option>
					<?php

					foreach ( $this->newsletters as $nl ){
			?>

					<option name="newsletter_sections" value="<?php echo $nl->ID ?>" > <?php echo $nl->post_title ?> </option>

			<?php
				}
				?></select>
				<?php
			}

			?>

			<div id="output_short_news">

				<h3><?php _e( 'Please, choose a Newsletter', 'content-creation-newsletter' ) ?>

			</div>

            <?php

				if ( isset( $_POST['short_check'] ) && check_admin_referer( '_create_newsletter' ) ){

					$this->data = $_POST;
					$this->save();


				} //END $_POST


            ?>

        </div><!--close wrap-->

        <?php
    }

	
    /**
     * get_short_news function.
     * 
     * @access public
     * @return void
     */
    public function get_short_news(){

	$args = array(
				'taxonomy' => 'sections',
				'hide_empty' => false,
				'meta_query' => array(
								array(
								'key' => 'newsletter',
								'value' => $this->newsletter_choosen,
								'compare'   => '=',
								)
								),

			);

	$terms = get_terms( $args );
	
	if ( ! empty( $terms ) ){
		
		$sorted_sections = wolly_order_sections( $terms );
	} else {
		
		$sorted_sections = '';
	}

	$this->terms_extracted = $sorted_sections;

	$short_news_by_section = array();

	foreach ( $sorted_sections as $t ){

	$args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'post_type'        => $this->short_news_cpt,
		'tax_query' => array(
						array(
						'taxonomy' => 'sections',
						'field' => 'id',
						'terms' => $t['term_id'] // Where term_id of Term 1 is "1".
						)
						),
		'meta_query' => array(
						 array(
						 'key' => 'inserted_in_newsletter',
						 'compare' => 'NOT EXISTS' // this should work...
						 )
						 ),
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'author_name'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);

	$sn = get_posts( $args );

	if ( ! empty( $sn ) ){
		$extracted_terms[$t['term_id']] = $t['name'];
		$short_news_by_section[$t['term_id']] = $sn;
		$sn = '';
		}

	}

	$this->terms_extracted = $extracted_terms;
	$this->short_news = $short_news_by_section;
    }
	
    /**
     * list_active_short_news function.
     * 
     * @access public
     * @return void
     */
    public function list_active_short_news(){

	    $this->get_short_news();


	    $html = '';
		$html .= '<div id="short-news-listing">';
		$data = array();
		$serialized_data = '';
	    if ( is_array( $this->short_news ) && ! empty( $this->short_news ) ){

			$nr_news = count( $this->short_news );
			
				

		    foreach ( $this->short_news as $key => $sn_divided_for_sections ) {



			    $html .= '<div class="section">';
			    $html .= '<h3>' . $this->terms_extracted[$key] . '</h3>';
			    $html .= '</div>';


	    		$sections_id[] = $key;


			    foreach ( $this->short_news[$key] as $sn ){
					
					$newletter_url 		= get_post_meta( $sn->ID, 'newsletter_url', true );
					$newsletter_lang 	= get_post_meta( $sn->ID, 'newsletter_lang', true );
					
					$url 		= ( ! empty( $newletter_url ) )   ? '<strong>URL:</strong> ' . $newletter_url      : '';
					$language 	= ( ! empty( $newsletter_lang ) ) ? '<strong>Lingua:</strong> ' . $newsletter_lang : '';
					
				    $print_author = get_user_meta( $sn->post_author, 'print_author_checkbox', true );

				    if ( 'yes' == $print_author ){

					    $news_author = get_userdata( $sn->post_author );

					    $author_display_name = $news_author->display_name;

				    } else {

					    $author_display_name = false;
				    }


			    $html .= '<div class="single-news">';

			    $html .= '<input type="checkbox" name="short_check[' . $key . '][' . $sn->ID . '][short_new_id]" value="' . $sn->ID . '" />';


			    $html .= '<span class="news-title">' . $sn->post_title . '</span>';

			    if ( false != $author_display_name ){

				  	$html .= '<div class="author-name">' . __( 'by ', 'content-creation-newsletter' ) . $author_display_name . '</div>';
			    }

				if ( ! empty( $newletter_url ) || ! empty( $newsletter_lang ) ){
					
					$html .= '<div class="news-content">' . $url . '<br />'  . $language . '</div>';
				}


			    if ( ! empty( $sn->post_content ) ){

				    $html .= '<div class="news-content">' . $sn->post_content . '</div>';
				    			    } else {

				    $html .= '<span class="news-excerpt">' . __( 'There is no content, it should be better to write a content before publish the newsletter', 'content-creation-newsletter' )  . '</span>';
			    }

			    $html .= '</div>';


		    }

		   }

		   $sections_id = implode( "-", $sections_id );

		   $html .= '<input type="hidden" name="sections_id" value="' . $sections_id . '" />';
	    } else {

		    $html .= __( 'No short news available', 'content-creation-newsletter' );
	    }
		$html .= '</div>';

	    $this->print_html = $html;

    }

    	
	/**
	 * ajax_extract_short_news function.
	 * 
	 * @access public
	 * @return void
	 */
	public function ajax_extract_short_news(){


		if ( '-1' == $_POST['id']) {


			$output = '<h3>' . __( 'Please, choose a Newsletter', 'content-creation-newsletter' ) . '</h3>';

		} else {

			$this->newsletter_choosen = $_POST['id'];

			$this->list_active_short_news();

			$output .= '<form method="post" action="">
			<h3>' . __( 'Newsletter title', 'content-creation-newsletter' ) . '</h3>
	        <input type="text" name="newsletter-title" value="" class="newsletter-title-creation" />
	        <p><input type="checkbox" value="dont" name="dont" /> <strong>' . __( 'Do not print sections name', 'content-creation-newsletter' ) . '</strong></p>
	        <h3>' . __( 'Choose short news to add to the newsletter', 'content-creation-newsletter' ) . '</h3>';
            $output .= $this->print_html;


            $output .= wp_nonce_field( '_create_newsletter' );
			$output .= '<input type="hidden" name="newsletter-choosen" value="' . $this->newsletter_choosen . '" />';
            $output .=  '<input type="submit" value="SUBMIT" class="button"/>
            </form>';


		}


		echo $output;
		die();
	}
	
	
	/**
	 * save function.
	 * 
	 * @access public
	 * @return void
	 */
	public function save(){
		
		$newsletter = '';
		$short_news_ids = array();

		foreach ( $_POST['short_check'] as $key_id => $s ){

			if ( isset( $_POST['short_check'][$key_id] ) ){

				$section_name = get_term_by( 'id', $key_id, 'sections' );

				if ( ! isset( $_POST['dont'] )  ){

				$newsletter .= '<h2>' . $section_name->name . '</h2>';

				}

				foreach ( $_POST['short_check'][$key_id] as $key => $sn ){

					$short_news_ids[] = $sn['short_new_id'];

					$newsletter_object = get_post( $sn['short_new_id'] );

					
					$newsletter_url     = get_post_meta( $newsletter_object->ID, 'newsletter_url', true );
					$newsletter_lang 	= get_post_meta( $newsletter_object->ID, 'newsletter_lang', true );
					
					$language = ( ! empty( $newsletter_lang ) ) ? '(' . $newsletter_lang . ')': '';
					
					if ( ! empty( $newsletter_url ) ){
						
						$shortnews_title = '<a href="' . esc_url( $newsletter_url ) . '">' . $newsletter_object->post_title  . '</a>  ' . $language;
						
					} else {
						
						$shortnews_title = $newsletter_object->post_title . ' ' . $language;
					}
					
					
					$print_author = get_user_meta( $newsletter_object->post_author, 'print_author_checkbox', true );

					if ( 'yes' == $print_author ){

					    $news_author = get_userdata( $newsletter_object->post_author );

					    $author_display_name = $news_author->display_name;

				    } else {

					    $author_display_name = false;
				    }

					

					$newsletter .= '<h3>' . $shortnews_title . '</h3>';

					if ( false != $author_display_name ){

						$newsletter .= '<div class="author-name">' . __( 'by ', 'content-creation-newsletter' ) . $author_display_name . '</div>';

					}
					$newsletter .= '<p>' . $newsletter_object->post_content . '</p>';

			}

			}
		}

		 if ( isset( $_POST['newsletter-title'] ) && ! empty( $_POST['newsletter-title'] ) ){

			 $newsletter_title = $_POST['newsletter-title'];

		 } else {

			 $newsletter_title = __( 'Title not defined', 'content-creation-newsletter' );
		 }

		$args = array(

        'post_content' => $newsletter,
        'post_title' => $newsletter_title,
        'post_status' => 'draft',
        'post_type' => 'newsletter',
 		);

		$newsletter_id = wp_insert_post( $args );

		update_post_meta( $newsletter_id, 'choosen_newsletter_full', $_POST['newsletter-choosen'] );

		//for each short news create a meta inserted_in_newsletter with $newsletter_id

		foreach ( $short_news_ids as $s ){

			update_post_meta( $s, 'inserted_in_newsletter', $newsletter_id );

		}

	$newsletter_admin_url = admin_url( 'post.php?post=' . $newsletter_id ) . '&action=edit';

	echo "<meta http-equiv='refresh' content='0;url=$newsletter_admin_url' />"; exit;

	}

}

if( is_admin() )
    $Wolly_Content_Creation_Newletter_Create = new Wolly_Content_Creation_Newletter_Create();