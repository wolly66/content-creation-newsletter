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
class Wolly_Content_Creation_Newsletter_Cpt {
	
	
	/**
	 * options
	 * 
	 * @var mixed
	 * @access private
	 */
	private $options;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){

		$this->options = get_option( 'content_creation_for_newsletter' );
		

		add_action( 'init', array( $this, 'newsletter_custom_post_type' ) );
		add_action( 'init', array( $this, 'short_news_custom_post_type' ) );
		
		if ( 'int' == $this->options['management'] ){
			add_action( 'init', array( $this, 'news_mngt_post_type' ), 0 );
		}

		add_action( 'add_meta_boxes', array( $this, 'meta_box_shortnews_add' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box_shortnews_link_add' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box_newsletter_mngt_add' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box_newsletter_mngt_sort_sections_add' ) );
		
		
		add_action( 'add_meta_boxes', array( $this, 'meta_box_newsletter_add' ) );
				
		
		add_action( 'save_post', array( $this, 'shortnews_save' ) , 10, 2 );
		add_action( 'save_post', array( $this, 'newsletter_save' ), 10, 2 );
		add_action( 'save_post', array( $this, 'newsletter_mngt_save' ), 10, 2 );
		

	}

	/**
	 * newsletter_custom_post_type function.
	 *
	 *  @since version 1.0
	 *
	 * @package Content & newsletter management
	 *
	 * @access public
	 * @return void
	 */
	public function newsletter_custom_post_type() {

		$labels = array(
			'name'                  => _x( 'Newsletters', 'Post Type General Name', 'content-creation-newsletter' ),
			'singular_name'         => _x( 'Newsletter', 'Post Type Singular Name', 'content-creation-newsletter' ),
			'menu_name'             => __( 'Newsletter', 'content-creation-newsletter' ),
			'name_admin_bar'        => __( 'Newsletter', 'content-creation-newsletter' ),
			'archives'              => __( 'Item Archives', 'content-creation-newsletter' ),
			'parent_item_colon'     => __( 'Parent Item:', 'content-creation-newsletter' ),
			'all_items'             => __( 'All Items', 'content-creation-newsletter' ),
			'add_new_item'          => __( 'Add New Item', 'content-creation-newsletter' ),
			'add_new'               => __( 'Add New', 'content-creation-newsletter' ),
			'new_item'              => __( 'New Item', 'content-creation-newsletter' ),
			'edit_item'             => __( 'Edit Item', 'content-creation-newsletter' ),
			'update_item'           => __( 'Update Item', 'content-creation-newsletter' ),
			'view_item'             => __( 'View Item', 'content-creation-newsletter' ),
			'search_items'          => __( 'Search Item', 'content-creation-newsletter' ),
			'not_found'             => __( 'Not found', 'content-creation-newsletter' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'content-creation-newsletter' ),
			'featured_image'        => __( 'Featured Image', 'content-creation-newsletter' ),
			'set_featured_image'    => __( 'Set featured image', 'content-creation-newsletter' ),
			'remove_featured_image' => __( 'Remove featured image', 'content-creation-newsletter' ),
			'use_featured_image'    => __( 'Use as featured image', 'content-creation-newsletter' ),
			'insert_into_item'      => __( 'Insert into item', 'content-creation-newsletter' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'content-creation-newsletter' ),
			'items_list'            => __( 'Items list', 'content-creation-newsletter' ),
			'items_list_navigation' => __( 'Items list navigation', 'content-creation-newsletter' ),
			'filter_items_list'     => __( 'Filter items list', 'content-creation-newsletter' ),
		);
		$args = array(
			'label'                 => __( 'Newsletter', 'content-creation-newsletter' ),
			'description'           => __( 'Newsletter', 'content-creation-newsletter' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', ),
			//'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-email-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'newsletter', $args );

	}

	/**
	 * short_news_custom_post_type function.
	 *
	 *  @since version 1.0
	 *
	 * @package Content & newsletter management
	 *
	 * @access public
	 * @return void
	 */

	function short_news_custom_post_type() {

		$labels = array(
			'name'                  => _x( 'Short news', 'Post Type General Name', 'content-creation-newsletter' ),
			'singular_name'         => _x( 'Short news', 'Post Type Singular Name', 'content-creation-newsletter' ),
			'menu_name'             => __( 'Short news', 'content-creation-newsletter' ),
			'name_admin_bar'        => __( 'Short news', 'content-creation-newsletter' ),
			'archives'              => __( 'Item Archives', 'content-creation-newsletter' ),
			'parent_item_colon'     => __( 'Parent Item:', 'content-creation-newsletter' ),
			'all_items'             => __( 'All Items', 'content-creation-newsletter' ),
			'add_new_item'          => __( 'Add New Item', 'content-creation-newsletter' ),
			'add_new'               => __( 'Add New', 'content-creation-newsletter' ),
			'new_item'              => __( 'New Item', 'content-creation-newsletter' ),
			'edit_item'             => __( 'Edit Item', 'content-creation-newsletter' ),
			'update_item'           => __( 'Update Item', 'content-creation-newsletter' ),
			'view_item'             => __( 'View Item', 'content-creation-newsletter' ),
			'search_items'          => __( 'Search Item', 'content-creation-newsletter' ),
			'not_found'             => __( 'Not found', 'content-creation-newsletter' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'content-creation-newsletter' ),
			'featured_image'        => __( 'Featured Image', 'content-creation-newsletter' ),
			'set_featured_image'    => __( 'Set featured image', 'content-creation-newsletter' ),
			'remove_featured_image' => __( 'Remove featured image', 'content-creation-newsletter' ),
			'use_featured_image'    => __( 'Use as featured image', 'content-creation-newsletter' ),
			'insert_into_item'      => __( 'Insert into item', 'content-creation-newsletter' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'content-creation-newsletter' ),
			'items_list'            => __( 'Items list', 'content-creation-newsletter' ),
			'items_list_navigation' => __( 'Items list navigation', 'content-creation-newsletter' ),
			'filter_items_list'     => __( 'Filter items list', 'content-creation-newsletter' ),
		);
		$args = array(
			'label'                 => __( 'Short news', 'content-creation-newsletter' ),
			'description'           => __( 'Short news', 'content-creation-newsletter' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-email',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => false,
			'capabilities' => array(
				'edit_post' 		 => 'edit_shortnew',
				'edit_posts' 		 => 'edit_shortnews',
				'edit_others_posts'  => 'edit_other_shortnews',
				'publish_posts'		 => 'publish_shortnews',
				'read_post' 		 => 'read_shortnew',
				'read_private_posts' => 'read_private_shortnews',
				'delete_post' 		 => 'delete_shortnew'
				),
			'map_meta_cap' => true,		
		);
		register_post_type( 'shortnews', $args );

	}
	
	/**
	 * news_mngt_post_type function.
	 *
	 *  @since version 1.0
	 *
	 * @package Content & newsletter management
	 *
	 * @access public
	 * @return void
	 */

	function news_mngt_post_type() {

	$labels = array(
		'name'                  => _x( 'Newsletters management', 'Post Type General Name', 'content-creation-newsletter' ),
		'singular_name'         => _x( 'Post TypeNewsletter management', 'Post Type Singular Name', 'content-creation-newsletter' ),
		'menu_name'             => __( 'Newsletter MNGT', 'content-creation-newsletter' ),
		'name_admin_bar'        => __( 'Newsletter MNGT', 'content-creation-newsletter' ),
		'archives'              => __( 'Item Archives', 'content-creation-newsletter' ),
		'attributes'            => __( 'Item Attributes', 'content-creation-newsletter' ),
		'parent_item_colon'     => __( 'Parent Item:', 'content-creation-newsletter' ),
		'all_items'             => __( 'All Items', 'content-creation-newsletter' ),
		'add_new_item'          => __( 'Add New Item', 'content-creation-newsletter' ),
		'add_new'               => __( 'Add New', 'content-creation-newsletter' ),
		'new_item'              => __( 'New Item', 'content-creation-newsletter' ),
		'edit_item'             => __( 'Edit Item', 'content-creation-newsletter' ),
		'update_item'           => __( 'Update Item', 'content-creation-newsletter' ),
		'view_item'             => __( 'View Item', 'content-creation-newsletter' ),
		'view_items'            => __( 'View Items', 'content-creation-newsletter' ),
		'search_items'          => __( 'Search Item', 'content-creation-newsletter' ),
		'not_found'             => __( 'Not found', 'content-creation-newsletter' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'content-creation-newsletter' ),
		'featured_image'        => __( 'Featured Image', 'content-creation-newsletter' ),
		'set_featured_image'    => __( 'Set featured image', 'content-creation-newsletter' ),
		'remove_featured_image' => __( 'Remove featured image', 'content-creation-newsletter' ),
		'use_featured_image'    => __( 'Use as featured image', 'content-creation-newsletter' ),
		'insert_into_item'      => __( 'Insert into item', 'content-creation-newsletter' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'content-creation-newsletter' ),
		'items_list'            => __( 'Items list', 'content-creation-newsletter' ),
		'items_list_navigation' => __( 'Items list navigation', 'content-creation-newsletter' ),
		'filter_items_list'     => __( 'Filter items list', 'content-creation-newsletter' ),
	);
	$args = array(
		'label'                 => __( 'Newsletter management', 'content-creation-newsletter' ),
		'description'           => __( 'Newsletter management', 'content-creation-newsletter' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => 5,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'newsletter_mngt', $args );

}




	/**
	 * meta_box_shortnews_addfunction.
	 *
	 * @access public
	 * @return void
	 */
	 public function meta_box_shortnews_add() {
		 add_meta_box( 'shortnews_box', __( 'Choose Newsletter', 'content-creation-newsletter') , array( $this , 'shortnews_settings' ), 'shortnews', 'side', 'high' );
		 }//close function

	
	/**
	 * shortnews_settings function.
	 * 
	 * @access public
	 * @return void
	 */
	public function shortnews_settings() {

	// $post is already set, and contains an object: the WordPress post
	global $post;

	$choosen_newsletter = get_post_meta( $post->ID, 'choosen_newsletter', true );


	$this->newsletters = wolly_get_newletters();

	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'shortnews_nonce', 'shortnews_nonce' );
	?>

	<p>



	    <div  class="choose-newsletter">

				<?php
				if ( is_array( $this->newsletters ) && ! empty( $this->newsletters ) ){

				?>

				<select id="choose-newsletter-shortnews" name="newsletter-choosen">
				<option value="-1"><?php _e( 'Please, choose newsletter', 'content-creation-newsletter' ) ?></option>

				<?php
					foreach ( $this->newsletters as $nl ){
			?>

					<option name="newsletter_sections" value="<?php echo $nl->ID ?>" <?php selected( $choosen_newsletter, $nl->ID ); ?> /> <?php echo $nl->post_title ?> </option>

			<?php
				}
				?>
			</select>

    <?php
	}
	?>
	</div>

     </p>
     <p>
	<?php
	    
		$args = array(
			
				'id' => '',
	 			'user_email' =>'',
	 			'user_first_name'	=>'',
	 			'user_last_name'	=>'',
	 			'status'	=>'',
		);
		
		// ! TO DO ISCRITTI ALLA NEWSLETTER
	    //$mailchimp = new Wolly_Newsletter_Mailchimp( $args );
	    // 
	    //$member_count = $mailchimp->get_mailchimp_member_count();
	    //
	    //$mailchimp_options = get_option( 'wolly-mailchimp-options' );
	    // 	   
		//
		//	echo '<p>Iscritti alla mia Newsletter: <strong>' . $mailchimp_options['member_count'] . '</strong></p>';
		//	echo '<p>Ultimo aggiornamento iscritti: <strong>' . date( 'd-m-Y H:i e', $mailchimp_options['timestamp'] ) . '</strong></p>';
		//	echo '<p>Prossimo aggiornamento iscritti: <strong>' . date( 'd-m-Y H:i e', ( $mailchimp_options['timestamp'] + 86400 ) ) . '</strong></p>';
    ?>
     </p>
    
     
     <?php
	}//close function

	
	 /**
	  * meta_box_shortnews_link_add function.
	  * 
	  * @access public
	  * @return void
	  */
	 public function meta_box_shortnews_link_add() {
		 add_meta_box( 'shortnews_link_box', __( 'News link', 'content-creation-newsletter' ) , array( $this , 'shortnews_link_settings' ), 'shortnews', 'normal', 'high' );
		 }//close function
		 
	
	/**
	 * shortnews_link_settings function.
	 * 
	 * @access public
	 * @return void
	 */
	public function shortnews_link_settings() {

	// $post is already set, and contains an object: the WordPress post
	global $post;
	
	$newletter_url 		= get_post_meta( $post->ID, 'newsletter_url', true );
	$newsletter_lang 	= get_post_meta( $post->ID, 'newsletter_lang', true );
	


	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'shortnews_nonce', 'shortnews_nonce' );
	?>

	<p>



	    <div  class="link">

			<p><?php _e( 'URL (include http:// or https://)', 'content-creation-newsletter' ); ?>   <input type="url" name="newsletter-url" value="<?php echo esc_url( $newletter_url ); ?>" /></p>
			
			<p><?php _e( 'Language', 'content-creation-newsletter' ); ?>   <input type="text" name="newsletter-lang" value="<?php echo esc_attr( $newsletter_lang ); ?>" /></p>

		</div>

     </p>
        
     
     <?php
	}//close function

	/**
	 * meta_box_newsletter_addfunction.
	 *
	 * @access public
	 * @return void
	 */
	 public function meta_box_newsletter_add() {
		 add_meta_box( 'newsletter_box', __( 'Choose Newsletter', 'content-creation-newsletter') , array( $this , 'newsletter_settings' ), 'newsletter', 'side', 'high' );
		 }//close function

	public function newsletter_settings() {

	// $post is already set, and contains an object: the WordPress post
	global $post;

	$choosen_newsletter = get_post_meta( $post->ID, 'choosen_newsletter_full', true );

	

	$this->newsletters = wolly_get_newletters();

	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'newsletter_nonce', 'newsletter_nonce' );
	?>

	<p>



	    <div  class="choose-newsletter">

				<?php
				if ( is_array( $this->newsletters ) && ! empty( $this->newsletters ) ){

				?>

				<select id="choose-newsletter-newsletter" name="newsletter-choosen">
				<option value="-1"><?php _e( 'Please, choose newsletter', 'content-creation-newsletter' ) ?></option>

				<?php
					foreach ( $this->newsletters as $nl ){
			?>

					<option name="newsletter_sections" value="<?php echo $nl->ID ?>" <?php selected( $choosen_newsletter, $nl->ID ); ?> /> <?php echo $nl->post_title ?> </option>

			<?php
				}
				?>
			</select>

    <?php
	}
	?>
	</div>

     </p>
     <?php
	}//close function
	
	public function meta_box_newsletter_mngt_add() {
		 add_meta_box( 'newsletter_box', __( 'Choose Mailchimp List', 'content-creation-newsletter') , array( $this , 'newsletter_mngt_settings' ), 'newsletter_mngt', 'side', 'high' );
		 }//close function

	public function newsletter_mngt_settings() {

	// $post is already set, and contains an object: the WordPress post
	global $post;

	$choosen_list = get_post_meta( $post->ID, 'choosen_list', true );

	$Wolly_Newsletter_Mailchimp_Utility = new Wolly_Newsletter_Mailchimp_Utility();
	$all_lists = $Wolly_Newsletter_Mailchimp_Utility->get_mailchimp_lists();
	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'newsletter_nonce', 'newsletter_nonce' );
	?>

	<p>



	    <div  class="choose-list">

				<?php
				if ( is_array( $all_lists ) && ! empty( $all_lists ) ){

				?>

				<select id="choose-list-newsletter" name="list-choosen">
				<option value="-1"><?php _e( 'Please, choose a list', 'content-creation-newsletter' ) ?></option>

				<?php
					foreach ( $all_lists as $key => $list ){
			?>

					<option name="newsletter_list" value="<?php echo $key ?>" <?php selected( $choosen_list, $key ); ?> /> <?php echo $list ?> </option>

			<?php
				}
				?>
			</select>

    <?php
	}
	?>
	</div>

     </p>
     <?php
	}//close function

	public function meta_box_newsletter_mngt_sort_sections_add() {
		 add_meta_box( 'sections_box', __( 'Order Sections', 'content-creation-newsletter' ) , array( $this , 'newsletter_mngt_sort_sections_settings' ), 'newsletter_mngt', 'normal', 'high' );
		 }//close function
		 
	public function newsletter_mngt_sort_sections_settings(){
		
		global $post;
						
		$args = array(
				'taxonomy' => 'sections',
				'hide_empty' => false,
				'meta_query' => array(
								array(
								'key' => 'newsletter',
								'value' => $post->ID,
								'compare'   => '=',
								)
								),

			);

		$terms = get_terms( $args );
		
				if ( ! empty( $terms ) ){
					
					wp_nonce_field( 'newsletter_nonce', 'newsletter_nonce' );
					
					$sorted_terms = wolly_order_sections( $terms );
			?>
			<ul id="sortable">
			<?php
			foreach ( $sorted_terms as $key => $te ){ ?>
				
				<li id="<?php echo $te['term_id'] ?>" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<?php echo $te['name']; ?> 
				<input type="hidden" name="sort-order[]" value="<?php echo $te['term_id']; ?>" />
				</li>
			<?php
			}
			?>
			</ul>
	
			<?php
		} else {
			
			_e( 'Please, add at least one section', 'content-creation-newsletter' );
		}
	
	

		
		
		
		?>
		
<?php 	
}
	public function shortnews_save( $post_id, $post ){


    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    	return;

    $slug = 'shortnews';

    // If this isn't a 'shortnews' post, don't update it.
    if ( $slug != $post->post_type ) {
        return;
    }

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['shortnews_nonce'] ) || !wp_verify_nonce( $_POST['shortnews_nonce'], 'shortnews_nonce' ) )
    	return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) )
    	return;


    // Make sure your data is set before trying to save it
    if(  isset ( $_POST['newsletter-choosen'] ) && -1 !=  $_POST['newsletter-choosen'] ){

		update_post_meta( $post_id, 'choosen_newsletter', $_POST['newsletter-choosen'] );

		} else {

			delete_post_meta( $post_id, 'choosen_newsletter' );
		}
		
	 // Make sure your data is set before trying to save it
    if(  isset ( $_POST['newsletter-url'] ) && ! empty(  $_POST['newsletter-url'] ) ){

	
		update_post_meta( $post_id, 'newsletter_url', esc_url_raw( $_POST['newsletter-url'] ) );

		} else {

			delete_post_meta( $post_id, 'newsletter_url' );
		}
	
	// Make sure your data is set before trying to save it
    if(  isset ( $_POST['newsletter-lang'] ) && ! empty(  $_POST['newsletter-lang'] ) ){

		update_post_meta( $post_id, 'newsletter_lang', esc_attr( $_POST['newsletter-lang'] ) );

		} else {

			delete_post_meta( $post_id, 'newsletter_lang' );
		}
		
		
		
	}

	public function newsletter_save( $post_id, $post ){

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    	return;

    $slug = 'newsletter';

    // If this isn't a 'book' post, don't update it.
    if ( $slug != $post->post_type ) {
        return;
    }


    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['newsletter_nonce'] ) || !wp_verify_nonce( $_POST['newsletter_nonce'], 'newsletter_nonce' ) )
    	return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) )
    	return;


    // Make sure your data is set before trying to save it
    if(  isset ( $_POST['newsletter-choosen'] ) && -1 !=  $_POST['newsletter-choosen'] ){

		update_post_meta( $post_id, 'choosen_newsletter_full', $_POST['newsletter-choosen'] );

		} else {

			delete_post_meta( $post_id, 'choosen_newsletter_full' );
		}

	// Get default category ID from options
	$forum_created = get_post_meta( $post_id, 'forum_created', true);

	// Check if this post is in default category
	if ( ! empty( $forum_created ) ) {

		return;

		} else {

			$newsletter_id = get_post_meta( $post_id, 'choosen_newsletter_full', true);

			if ( ! empty( $newsletter_id ) ){

				$has_forum = get_post_meta( $newsletter_id, '_has_forum_checkbox', true);
				$forum_id = get_post_meta( $newsletter_id, '_forum_id', true);

				if ( ! empty( $has_forum ) && ! empty( $forum_id ) ){

					// unhook this function so it doesn't loop infinitely
					remove_action( 'save_post', array( $this, 'newsletter_save' ), 10, 2 );

					$post_permalink = '<p>' . __( 'You can read this newsletter at this ', 'content-creation-newsletter' ) . '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" >link</a></p>';

					$forum_content = $post->post_excerpt . $post_permalink;

					$topic_data =  array(
						'post_author'    => $post->post_author,
						'post_title'     => $post->post_title,
						'post_content'   => $forum_content,
						'post_status'    => 'publish',
						'post_parent'    => $forum_id,
						'post_type'      => 'topic',

						);

					$topic_meta = array(
						'forum_id' => $forum_id,
						'author_ip' => '0.0.0.0',
						'last_active_time' => $post->post_date
						);

					$topic_id = bbp_insert_topic( $topic_data, $topic_meta );
					$get_topic_permalink = get_permalink( $topic_id );
					$topic_permalink = '<p>' . __( 'You can discuss this newsletter on our forum at this ', 'content-creation-newsletter' ) . '<a href="' . $get_topic_permalink . '" >link</a></p>';

					$post->post_content = $post->post_content . $topic_permalink;

					// update the post, which calls save_post again
					wp_update_post( $post );

					update_post_meta( $post->ID, 'forum_created', $topic_id );

					// re-hook this function
					add_action( 'save_post', array( $this, 'newsletter_save' ), 10, 2 );



				}
			}


		}
	}
	
	public function newsletter_mngt_save( $post_id, $post ){
	
	 // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    	return;

    $slug = 'newsletter_mngt';

    // If this isn't a 'book' post, don't update it.
    if ( $slug != $post->post_type ) {
        return;
    }


    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['newsletter_nonce'] ) || !wp_verify_nonce( $_POST['newsletter_nonce'], 'newsletter_nonce' ) )
    	return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) )
    	return;

	if(  isset ( $_POST['sort-order'] ) && is_array( $_POST['sort-order'] ) ){
		
		$i = 1;
		foreach ( $_POST['sort-order'] as $so ){
			
			update_term_meta( $so, 'sort-order', $i );
			
			$i++;
		}
		
		}
    // Make sure your data is set before trying to save it
    if(  isset ( $_POST['list-choosen'] ) && -1 !=  $_POST['list-choosen'] ){

		update_post_meta( $post_id, 'choosen_list', $_POST['list-choosen'] );

		} else {

			delete_post_meta( $post_id, 'choosen_list' );
		}


	}




}// close class

$Wolly_Content_Creation_Newsletter_Cpt = new Wolly_Content_Creation_Newsletter_Cpt();