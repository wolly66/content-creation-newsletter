<?php
	
	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}

class Wolly_Content_Newsletter_Creation_Options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $newsletters;
    private $myurl;

    /**
     * Start up
     */
    public function __construct()
    {
	    $this->myurl = get_site_url() . '/';
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_menu_page(
            'Newletter Settings',
            __( 'Newsletter', 'content-creation-newsletter' ),
            'manage_options',
            'newsletter-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }


    /**
     * Options page callback
     */
    public function create_admin_page()
    {
	    // Set class property
        $this->options = get_option( 'content_creation_for_newsletter' );
          
        ?>
        <div class="wrap">
            <h2><?php _e( 'Newsletter Settings', 'content-creation-newsletter' )?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'newsletter_option_group' );
                do_settings_sections( 'newsletter-setting-admin' );

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
	    $newsletter_options = get_option( 'content_creation_for_newsletter' );
        register_setting(
            'newsletter_option_group', // Option group
            'content_creation_for_newsletter', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'main-query', // ID
            __( 'Do you want Newsletters added to the main query?', 'content-creation-newsletter' ), // Title
            array( $this, 'print_main_query_section_info' ), // Callback
            'newsletter-setting-admin' // Page
        );

        add_settings_field(
            'main-query-settings', // ID
            __( 'Add to main query?', 'content-creation-newsletter' ), // Title
            array( $this, 'main_query_settings' ), // Callback
            'newsletter-setting-admin', // Page
            'main-query' // Section
        );

		add_settings_section(
            'woocommerce', // ID
            __( 'Choose Newsletters Management', 'content-creation-newsletter' ), // Title
            array( $this, 'print_settings_info' ), // Callback
            'newsletter-setting-admin' // Page
        );

        add_settings_field(
            'newletter-management', // ID
            __( 'Newsletter Management', 'content-creation-newsletter' ),  // Title
            array( $this, 'newsletter_management_callback' ),// Callback
            'newsletter-setting-admin', // Page
            'woocommerce'// Section
        );

                
        add_settings_section(
            'mailchimp', // ID
            __( 'Mailchimp', 'content-creation-newsletter' ), // Title
            array( $this, 'print_mailchimop_sections_info' ), // Callback
            'newsletter-setting-admin' // Page
        );
        
        add_settings_field(
            'mailchimp_in_use', // ID
            __( 'Use Mailchimp?', 'content-creation-newsletter' ),  // Title
            array( $this, 'sections_mailchimp_use_callback' ),// Callback
            'newsletter-setting-admin',// Page
            'mailchimp'// Section
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
        if( isset( $input['main_query'] ) ){
	        if ( ( 0 == $input['main_query'] ) || ( 1 == $input['main_query'] ) ){
            	$new_input['main_query'] = absint( $input['main_query'] );
            }
        }
        if( isset( $input['use-mailchimp'] ) ){
	        if ( ( 0 == $input['use-mailchimp'] ) || ( 1 == $input['use-mailchimp'] ) ){
            	$new_input['use-mailchimp'] = absint( $input['use-mailchimp'] );
            }
        }
        

        if( isset( $input['management'] ) ){


		        switch ( $input['management'] ) {
				case -1:
				    $new_input['management'] = '';
				    break;
				case 'woo':
				    $new_input['management'] = 'woo';
				    break;
				case 'int':
				    $new_input['management'] = 'int';
				    break;
				default:
				   $new_input['management'] = '';
				}
           
        }
        

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_main_query_section_info()
    {
        _e( 'Main query settings', 'content-creation-newsletter' );
    }

	/**
     * Print the Section text
     */
    public function print_settings_info()
    {
        _e( 'Choose newsletter management for this website', 'content-creation-newsletter' );
    }

    /**
     * Print the Section text
     */
    public function print_sections_info()
    {
        _e( 'Create newsletter sections', 'content-creation-newsletter' );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function main_query_settings()
    {

    ?>
        <input type="radio" id="main-yes" name="content_creation_for_newsletter[main_query]" value="1" <?php checked( $this->options['main_query'], 1 ); ?>/> <?php _e( 'Yes', 'content-creation-newsletter' ) ?> <br />


        <input type="radio" id="main-no" name="content_creation_for_newsletter[main_query]" value="0" <?php checked( $this->options['main_query'], 0 ); ?>/>  <?php _e( 'No', 'content-creation-newsletter' ) ?> <br />

		
     <?php
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function newsletter_management_callback()
    {
	   
		    ?>
			<input type="hidden" name="content_creation_for_newsletter[management]" value="int" /><?php _e( 'Internal management', 'content-creation-newsletter' ) ?> 
		   <!-- <select name="content_creation_for_newsletter[management]">
			    <option value='-1' <?php selected( $this->options['management'], -1 ); ?>><?php _e( 'Please, choose newsletter management', 'content-creation-newsletter' ) ?></option>
			    <option value='int' <?php selected( $this->options['management'], 'int' ); ?>><?php _e( 'Internal management', 'content-creation-newsletter' ) ?></option>
			    <option value='woo' <?php selected( $this->options['management'], 'woo' ); ?>><?php _e( 'WooCommerce', 'content-creation-newsletter' ) ?></option>
		    </select>-->
		    <?php
			 
			}

      
    
     public function sections_mailchimp_use_callback(){
	     
	     ?>
        <input type="radio" id="mailchimp-yes" name="content_creation_for_newsletter[use-mailchimp]" value="1" <?php checked( $this->options['use-mailchimp'], 1 ); ?>/> <?php _e( 'Yes', 'content-creation-newsletter' ) ?> <br />


        <input type="radio" id="mailchimp-no" name="content_creation_for_newsletter[use-mailchimp]" value="0" <?php checked( $this->options['use-mailchimp'], 0 ); ?>/>  <?php _e( 'No', 'content-creation-newsletter' ) ?> <br />

		<?php	     
	     
     }
     

}

if( is_admin() )
    $Wolly_Content_Newsletter_Creation_Options = new Wolly_Content_Newsletter_Creation_Options();