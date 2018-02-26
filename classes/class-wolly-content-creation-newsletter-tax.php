<?php
	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}

/**
 * Wolly_Newsletter_Cpt class.
 *
 * @since version 1.0
 *
 * @package Content & newsletter management
 *
 */
class Wolly_Content_Creation_Newsletter_Tax {
	
	
	/**
	 * options
	 * 
	 * @var mixed
	 * @access private
	 */
	private $options;
	
	/**
	 * newletters
	 * 
	 * @var mixed
	 * @access private
	 */
	private $newletters;
	
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){

		$this->options = get_option( 'content_creation_for_newsletter' );
		
		
		add_action( 'init', array( $this, 'sections_tax' ) );
		
		
		add_action( 'init', array( $this, 'register_sections_meta' ) );
		

		add_action( 'sections_add_form_fields', array( $this,  'new_newsletter_sections_meta' ) );
		add_action( 'sections_edit_form_fields', array( $this,  'edit_newsletter_sections_meta' ) );

		add_action( 'edit_sections',  array( $this, 'save_newsletter_sections' ) );
		add_action( 'create_sections', array( $this, 'save_newsletter_sections' ) );

		add_filter( 'manage_edit-sections_columns', array( $this, 'edit_sections_columns' ), 10, 2 );
		add_filter( 'manage_sections_custom_column', array( $this,  'manage_term_newsletter_column' ),10, 3 );

				

	}


	/**
	 * sections_tax function.
	 * 
	 * @access public
	 * @return void
	 */
	function sections_tax() {

	$labels = array(
		'name'                       => _x( 'Sections', 'Taxonomy General Name', 'content-creation-newsletter' ),
		'singular_name'              => _x( 'Section', 'Taxonomy Singular Name', 'content-creation-newsletter' ),
		'menu_name'                  => __( 'Sections', 'content-creation-newsletter' ),
		'all_items'                  => __( 'All Items', 'content-creation-newsletter' ),
		'parent_item'                => __( 'Parent Item', 'content-creation-newsletter' ),
		'parent_item_colon'          => __( 'Parent Item:', 'content-creation-newsletter' ),
		'new_item_name'              => __( 'New Item Name', 'content-creation-newsletter' ),
		'add_new_item'               => __( 'Add New Item', 'content-creation-newsletter' ),
		'edit_item'                  => __( 'Edit Item', 'content-creation-newsletter' ),
		'update_item'                => __( 'Update Item', 'content-creation-newsletter' ),
		'view_item'                  => __( 'View Item', 'content-creation-newsletter' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'content-creation-newsletter' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'content-creation-newsletter' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'content-creation-newsletter' ),
		'popular_items'              => __( 'Popular Items', 'content-creation-newsletter' ),
		'search_items'               => __( 'Search Items', 'content-creation-newsletter' ),
		'not_found'                  => __( 'Not Found', 'content-creation-newsletter' ),
		'no_terms'                   => __( 'No items', 'content-creation-newsletter' ),
		'items_list'                 => __( 'Items list', 'content-creation-newsletter' ),
		'items_list_navigation'      => __( 'Items list navigation', 'content-creation-newsletter' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'sections', array( 'shortnews' ), $args );

}



	
	/**
	 * register_sections_meta function.
	 * 
	 * @access public
	 * @return void
	 */
	public function register_sections_meta(){

		register_meta( 'sections', 'shortnews', array( $this, 'sanitize_shortnews_meta' ) );


	}

	/**
	 * new_newsletter_sections_meta function.
	 * 
	 * @access public
	 * @param mixed $term_id
	 * @return void
	 */
	public function new_newsletter_sections_meta( $term_id ){

		wp_nonce_field( basename( __FILE__ ), 'newsletter_sections_nonce' );
		
		
		$this->newsletters = wolly_get_newletters();
		
		$term_meta =  get_term_meta( $term_id, 'newsletter', true );
		
		
		?>
		
		<div class="form-field newsletter_sections-wrap">
		<label for="newsletter_sections"><?php _e( 'Choose Newsletter', 'content-creation-newsletter' ); ?></label>
		
		<?php
			if ( is_array( $this->newsletters ) && ! empty( $this->newsletters ) ){
		
				?>
				<select name="newsletter_sections">
					<option value="-1"><?php _e( 'Choose newsletter', 'content-creation-newsletter' ) ?></option>
				<?php
		
				foreach ( $this->newsletters as $nl ){ ?>

					<option name="newsletter_sections" value="<?php echo $nl->ID ?>" <?php selected( $term_meta, $nl->ID ); ?> /> <?php echo $nl->post_title ?> </option>

					<?php
					} ?>
				</select>
			<?php
			}
			?>
			<br />
		</div>

	<?php
	}

	
	/**
	 * edit_newsletter_sections_meta function.
	 * 
	 * @access public
	 * @param mixed $term
	 * @return void
	 */
	public function edit_newsletter_sections_meta( $term ){

		$this->newsletters = wolly_get_newletters();

		$term_meta =  get_term_meta( $term->term_id, 'newsletter', true ); ?>

		<tr class="form-field newsletter_sections-wrap">

			<?php wp_nonce_field( basename( __FILE__ ), 'newsletter_sections_nonce' ); ?>
			<th scope="row"><label for="newsletter_sections"><?php _e( 'Choose Newsletter', 'content-creation-newsletter' ); ?></label></th>
				<td>
				<?php
				if ( is_array( $this->newsletters ) && ! empty( $this->newsletters ) ){ ?>
					<select name="newsletter_sections">
						<option value="-1"><?php _e( 'Choose newsletter', 'content-creation-newsletter' ) ?></option>
						<?php
						foreach ( $this->newsletters as $nl ){ ?>

							<option name="newsletter_sections" value="<?php echo $nl->ID ?>" <?php selected( $term_meta, $nl->ID ); ?> /> <?php echo $nl->post_title ?> </option>

						<?php
						} ?>
					</select>
				</td>
    	</tr>
    			<?php

				}
	}

	
	/**
	 * sanitize_shortnews_meta function.
	 * 
	 * @access public
	 * @param mixed $newsletter
	 * @return void
	 */
	public function sanitize_shortnews_meta( $newsletter ){

		wp_die('passo anche di qui');


	}
		
	
	/**
	 * save_newsletter_sections function.
	 * 
	 * @access public
	 * @param mixed $term_id
	 * @return void
	 */
	public function save_newsletter_sections( $term_id ){


		if ( ! isset( $_POST['newsletter_sections_nonce'] ) || ! wp_verify_nonce( $_POST['newsletter_sections_nonce'], basename( __FILE__ ) ) )
			return;

		if ( isset( $_POST['newsletter_sections'] ) && -1 != $_POST['newsletter_sections'] ){

			update_term_meta( $term_id, 'newsletter', $_POST['newsletter_sections'] );

			} else {

				delete_term_meta( $term_id, 'newsletter' );
		}
	}
	
	/**
	 * edit_sections_columns function.
	 * 
	 * @access public
	 * @param mixed $columns
	 * @return void
	 */
	public function edit_sections_columns( $columns ) {

    	$columns['newsletter'] = __( 'NewsL.', 'content-creation-newsletter' );

		return $columns;
	}
	
	/**
	 * manage_term_newsletter_column function.
	 * 
	 * @access public
	 * @param mixed $out
	 * @param mixed $column
	 * @param mixed $term_id
	 * @return $out
	 */
	public function manage_term_newsletter_column( $out, $column, $term_id ) {

		if ( 'newsletter' === $column ) {

			$term_meta =  get_term_meta( $term_id, 'newsletter', true );

			$newsletter_title = get_the_title( $term_meta );

			$out = sprintf( '<span class="color-block" >%s</span>', $newsletter_title );
    	}

		return $out;
	}

}// close class

$Wolly_Content_Creation_Newsletter_Tax = new Wolly_Content_Creation_Newsletter_Tax();