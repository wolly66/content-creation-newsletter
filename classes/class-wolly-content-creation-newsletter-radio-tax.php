<?php
/*
Author: Stephen Harris http://profiles.wordpress.org/stephenh1988/
Github: https://github.com/stephenh1988

This is a class implementation of the wp.tuts+ tutorial: http://wp.tutsplus.com/tutorials/creative-coding/how-to-use-radio-buttons-with-taxonomies/

To use it, just add to your functions.php and add the javascript file to your theme’s js folder (call it radiotax.js).

Better still, make make a plug-in out of it, including the javascript file, and being sure to point the wp_register_script to radiotax.js in your plug-in folder.

The class constants are
  - taxonomy: the taxonomy slug
  - taxonomy_metabox_id: the ID of the original taxonomy metabox
  - post type - the post type the metabox appears on
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}



class Wolly_Content_Newsletter_Creation_WordPress_Radio_Taxonomy {
	private $taxonomy = '';
	private $taxonomy_metabox_id = '';
	private $post_type= '';
	private $newsletter_associated = '';
	private $idp = '';
	private $name = '';

	public function __construct(){

		//add_action( 'admin_footer', array( $this,  'jquery_select_newsletter' ) );
		add_action( 'wp_ajax_extract', array( $this, 'extract_sections' ) );
		add_action( 'wp_ajax_nopriv_extract_sections', array( $this, 'extract_sections' ) );

		$this->taxonomy = 'sections';

		$this->post_type = 'shortnews';

		$this->taxonomy_metabox_id = 'sectionsdiv';

		$this->name = 'tax_input[' . $this->taxonomy . ']';

		$this->get_newsletter_associated ();

		//Remove old taxonomy meta box
		add_action( 'admin_menu', array( $this,'remove_meta_box'));

		//Add new taxonomy meta box
		add_action( 'add_meta_boxes', array( $this,'add_meta_box'));

		//Load admin scripts
		//add_action('admin_enqueue_scripts',array( $this,'admin_script'));

		//Load admin scripts
		add_action('wp_ajax_radio_tax_add_taxterm',array( $this,'ajax_add_term'));




	}

	public function remove_meta_box(){
   		remove_meta_box( $this->taxonomy_metabox_id, $this->post_type, 'normal');
	}


	public function add_meta_box() {
		add_meta_box( 'slownews_sections', 'Sections',array( $this,'metabox'), $this->post_type ,'side','core');
	}


	//Callback to set up the metabox
	public function metabox( $post ) {
		//Get taxonomy and terms


       	 //Set up the taxonomy object and get terms
       	 $tax = get_taxonomy( $this->taxonomy );

       	 //$terms = get_terms( $this->taxonomy, array( 'hide_empty' => 0 ) );

       	 $args = array(
				'taxonomy' => $this->taxonomy,
				'hide_empty' => false,
				'meta_query' => array(
								array(
								'key' => 'newsletter',
								'value' => $this->newsletter_associated,
								'compare'   => '=',
								)
								),

			);

			$terms = get_terms( $args );

       	 //Name of the form
       	 //$name = 'tax_input[' . $this->taxonomy . ']';

       	 //Get current and popular terms
       	 $popular = get_terms( $this->taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
       	 $postterms = get_the_terms( $post->ID, $this->taxonomy );
       	 $current = ($postterms ? array_pop( $postterms ) : false);
       	 $current = ($current ? $current->term_id : 0);
       	 ?>

		<!-- Ajax -->
		<div id="output_short_news">

			<?php if ( -1 == $this->newsletter_associated || empty( $this->newsletter_associated ) ){ ?>

				<h3><?php _e( 'Please, choose a Newsletter', 'wolly-content-newsletter-management' ) ?></h3>

			<?php } else { ?>

				<div id="taxonomy-<?php echo $this->taxonomy; ?>" class="categorydiv">

			<!-- Display taxonomy terms -->
			<div id="<?php echo $this->taxonomy; ?>-all" class="tabs-panel">
				<ul id="<?php echo $this->taxonomy; ?>checklist" class="list:<?php echo $this->taxonomy?> categorychecklist form-no-clear">
				<?php foreach($terms as $term){
       				 $id = $this->taxonomy.'-'.$term->term_id;
					$value= (is_taxonomy_hierarchical($this->taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				        echo "<li id='$id'><label class='selectit'>";
				        echo "<input type='radio' id='in-$id' name='{$this->name}'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				        echo "</label></li>";
		       	 }?>
				</ul>
			</div>

		</div>


				<?php }  ?>
			</div>
        <?php
    }


	public function ajax_add_term(){

		$taxonomy = !empty( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '';
		$term = !empty($_POST['term']) ? $_POST['term'] : '';
		$tax = get_taxonomy( $this->taxonomy );

		check_ajax_referer('radio-tax-add-'.$this->taxonomy, '_wpnonce_radio-add-tag');

		if( !$tax || empty( $term ) )
			exit();

		if ( !current_user_can( $tax->cap->edit_terms ) )
			die('-1');

		$tag = wp_insert_term($term, $this->taxonomy);

		if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $this->taxonomy )) ) {
			//TODO Error handling
			exit();
		}

		$id = $this->taxonomy.'-'.$tag->term_id;
		//$name = 'tax_input[' . $this->taxonomy . ']';
		$value= (is_taxonomy_hierarchical($this->taxonomy) ? "value='{$tag->term_id}'" : "value='{$term->tag_slug}'");

		$html ='<li id="'.$id.'"><label class="selectit"><input type="radio" id="in-'.$id.'" name="'.$this->name.'" '.$value.' />'. $tag->name.'</label></li>';

		echo json_encode( array( 'term' => $tag->term_id, 'html' => $html ) );
		exit();
	}

	public function extract_sections(){

		$output = '';

		if ( -1 == $_POST['id'] ){

			$output = '<h3>' . __( 'Please, choose a Newsletter', 'wolly-content-newsletter-management' ) . '</h3>';

		} else {


			$args = array(
				'taxonomy' => $this->taxonomy,
				'hide_empty' => false,
				'meta_query' => array(
								array(
								'key' => 'newsletter',
								'value' => $_POST['id'],
								'compare'   => '=',
								)
								),

			);

			$terms = get_terms( $args );

			//Get current and popular terms
       	 $popular = get_terms( $this->taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
       	 $postterms = get_the_terms( $_POST['id'], $this->taxonomy );
       	 $current = ($postterms ? array_pop( $postterms ) : false);
       	 $current = ($current ? $current->term_id : 0);


			$output .= '<div id="taxonomy-"' . $this->taxonomy . '" class="categorydiv">


			<!-- Display taxonomy terms -->
			<div id="' . $this->taxonomy . '-all" class="tabs-panel">
				<ul id="' . $this->taxonomy . 'checklist" class="list:' . $this->taxonomy . ' categorychecklist form-no-clear">';

			foreach( $terms as $term ){

       				$id = $this->taxonomy.'-'.$term->term_id;
					$value = ( is_taxonomy_hierarchical( $this->taxonomy ) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");

				    $output .= '<li id="' . $id . '"><label class="selectit">';

				    $output .= '<input type="radio" id="in-' . $id . '" name="' . $this->name . ' ' . checked( $current, $term->term_id, false ). '"' . $value . ' />' . $term->name . '<br />';
				    $output .= '</label></li>';
		       	 }
				$output .= '</ul>
			</div>';


		$output .= '</div>';



		}
	echo $output;
	die();
	}


	public function get_newsletter_associated (){

		//global $post;

		if ( isset( $_GET['post'] ) ){

		$this->idp = $_GET['post'];
		$this->newsletter_associated = get_post_meta( $this->idp, 'choosen_newsletter', true );
		}



	}



}
$Wolly_Content_Newsletter_Creation_WordPress_Radio_Taxonomy = new Wolly_Content_Newsletter_Creation_WordPress_Radio_Taxonomy();