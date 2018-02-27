<?php

	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}
	
	/**
	 * class-wolly-newsletter-mailchimp-options.php
	 *
	 * @package Content & newsletter management
	 * @subpackage Backend Options for Mailchimp
	 */
	 
	 
	 Class Wolly_Content_Newsletter_Creation_Mailchimp_Options{
		 
		 /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $newsletters;
    

    /**
     * Start up
     */
    public function __construct()
    {
	  	$newsletter_options =  get_option( 'content_creation_for_newsletter' );
	  	
	  	if ( 1 == $newsletter_options['use-mailchimp'] ){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        
        }
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_submenu_page(
	        'newsletter-setting-admin',
			__( 'Mailchimp options', 'content-creation-newsletter' ),
			__( 'Mailchimp options', 'content-creation-newsletter' ),
			'manage_options',
			'mailchimp-settings-admin',
			array( $this, 'create_admin_page' )
			);    }


    /**
     * Options page callback
     */
    public function create_admin_page()
    {
	    // Set class property
        $this->options = get_option( 'wolly_mailchimp_options' );
        
        ?>
        <div class="wrap">
            <h2><?php _e( 'Mailchimp Settings', 'content-creation-newsletter' )?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'mailchimp_option_group' );
                do_settings_sections( 'mailchimp-setting-admin' );

                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
	    
        register_setting(
            'mailchimp_option_group', // Option group
            'wolly_mailchimp_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'mailchimp', // ID
            __( 'Mailchimp', 'content-creation-newsletter' ), // Title
            array( $this, 'print_mailchimp_sections_info' ), // Callback
            'mailchimp-setting-admin' // Page
        );
                
         
        add_settings_field(
            'mailchimp_api', // ID
            __( 'Mailchimp Api', 'content-creation-newsletter' ),  // Title
            array( $this, 'sections_mailchimp_api_callback' ),// Callback
            'mailchimp-setting-admin',// Page
            'mailchimp'// Section
        );
        
        add_settings_field(
            'mailchimp_url', // ID
            __( 'Mailchimp URL', 'content-creation-newsletter' ),  // Title
            array( $this, 'sections_mailchimp_url_callback' ),// Callback
            'mailchimp-setting-admin',// Page
            'mailchimp'// Section
        );
                
        add_settings_section(
            'mailchimp_lists', // ID
            __( 'Mailchimp Lists', 'content-creation-newsletter' ), // Title
            array( $this, 'print_mailchimp_lists_sections_info' ), // Callback
            'mailchimp-setting-admin' // Page
        );
        
        add_settings_section(
            'mailchimp_single_subscribe', // ID
            __( 'Add a subscribe form on single page', 'content-creation-newsletter' ), // Title
            array( $this, 'print_mailchimp_single_subscribe_sections_info' ), // Callback
            'mailchimp-setting-admin' // Page
        );
		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        
        if ( isset( $input['mailchimp-api'] ) ){
        
        	if ( preg_match('/([0-9a-z-].+)?/', $input['mailchimp-api'] ) ) 
            	$new_input['mailchimp-api'] = $input['mailchimp-api'];
		}
        
        if( isset( $input['mailchimp-url'] ) )
            $new_input['mailchimp-url'] = esc_url_raw( $input['mailchimp-url'] );
           
        
        return $new_input;
    }
	
	/**
     * Print the Section text
     */
    public function print_mailchimp_sections_info()
    {
	    	    
        _e( 'Mailchimp Settings', 'content-creation-newsletter' );
    }

         
    public function sections_mailchimp_api_callback(){
	    
	    ?>
	    
	    <input type="text" name="wolly_mailchimp_options[mailchimp-api]" value="<?php echo $this->options['mailchimp-api']; ?>" />
	    
	    <?php
    }
    
       
    public function sections_mailchimp_url_callback(){
	    
	    ?>
	    
	    <input type="text" name="wolly_mailchimp_options[mailchimp-url]" value="<?php echo $this->options['mailchimp-url']; ?>" />
	    
	    <?php
    }
    
    

	/**
     * Print the Section text
     */
    public function print_mailchimp_lists_sections_info()
    {
	    $Wolly_Newsletter_Mailchimp_Utility = new Wolly_Newsletter_Mailchimp_Utility();
	    $all_lists = $Wolly_Newsletter_Mailchimp_Utility->get_mailchimp_lists();
	    	    
        _e( 'All Mailchimp Lists', 'content-creation-newsletter' );
        
        if ( ! empty( $all_lists ) && is_array( $all_lists ) ){
	        
	        foreach ( $all_lists as $key => $list ){
	        	
	        	$format = __( 'List name: %s List ID: %s', 'content-creation-newsletter' );
				echo '<p>' . sprintf( $format, $list, $key ) . '</p>';
			}
			
        } else {
	        
	        _e( 'You have no list', 'content-creation-newsletter' );
        }
    }


		 
		 
	 }
	 
	 if ( is_admin(  )){
	 $Wolly_Content_Newsletter_Creation_Mailchimp_Options = new Wolly_Content_Newsletter_Creation_Mailchimp_Options();
	 }