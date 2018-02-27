<?php
	
	/**
	 * Wolly_Utility_User_Second_User_role
	 *
	 * @package wolly's utility
	 * @subpackage Add a second role to a user
	 */	 
	
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly
		}
		
	 
	 /**
	  * Wolly_Utility_User_Second_User_role class.
	  */
	class Wolly_Content_Newsletter_Creation_Utility_User_Second_User_Role {
		
		/**
		 * version
		 * 
		 * @var mixed
		 * @access private
		 */
		private $version;
		
		/**
		 * roles_caps
		 * 
		 * (default value: array())
		 * 
		 * @var array
		 * @access private
		 */
		private $roles_caps = array();
		
		/**
		 * option_name
		 * 
		 * @var mixed
		 * @access private
		 */
		private $option_name;
				
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct(){
		    
		    $this->version 		= '100';
		    $this->option_name 	= 'wolly_user_second_role';
		    
		    add_action( 'show_user_profile', 		array( $this, 'newsletter_extra_fields' ) );
			add_action( 'edit_user_profile', 		array( $this, 'newsletter_extra_fields' ) );
			add_action( 'personal_options_update', 	array( $this, 'save_newsletter_extra_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_newsletter_extra_fields' ) );
			
		    //check for roles update 
			add_action( 'init', array( $this, 'update_check' ) );
		    
		    
		}
		
		/**
		 * update_UTILITY_check function.
		 *
		 * @access public
		 * @return void
		 */
		public function update_check() {
		// Do checks only in backend
		   if ( is_admin() ) {
		
		   	if ( version_compare( get_option( $this->option_name ), $this->version ) != 0  ) {
		
		   	$this->do_update();
		
		   	}
		
			} //end if only in the admin
		}
		
		/**
		 * do_update function.
		 *
		 * @access private
		 *
		 */
		public function do_update(){
		
		   //Update option
		   $this->add_new_roles_and_caps();
		   //update_option( $this->option_name , $this->version );
		}
		
		
		/**
		 * newsletter_extra_fields function.
		 * 
		 * @access public
		 * @param mixed $user
		 * @return void
		 */
		public function newsletter_extra_fields( $user ){
		
		global $wp_roles;
		
		if ( current_user_can( 'manage_options' ) ) { 

			//get newsletter roles
			$newsletter_roles = array();
			foreach ( $wp_roles->roles as $role => $role_value ) {
				
				if ( substr( $role, 0, 10 ) != 'newsletter' ) {
					continue;
				}
				
				$newsletter_roles[ $role ] = $role_value;
			}
		
			//only if user has other than newsletter role
			if ( ! empty( $user->roles[ 0 ] ) && in_array( $user->roles[ 0 ], array_keys( $newsletter_roles ), true ) ) {
				return;
			}
		
			?>

			<h2><?php _e( 'Choose a role for newsletter', 'content-creation-newsletter' ); ?></h2>
		
				<table class="form-table">
					<tbody>
						<tr class="user-newsletter role">
							<th><?php _e( 'Newsletter Role', 'content-creation-newsletter' ); ?></th>
							<td>
								<select name="newsletter_role">
									<option value="-1"><?php _e( '-- No additional Role for Newletter --', 'content-creation-newsletter' ); ?></option>
									<?php foreach ( $newsletter_roles as $key => $role ){ ?>
							
									<option value="<?php echo $key; ?>" <?php echo selected( $user->has_cap( $key ), TRUE, FALSE ); ?>><?php echo $role[ 'name' ];?></option>
							
									<?php } ?>
								</select>
							</td>
						</tr>
				
					</tbody>
				</table>

		<?php	}

	}


	/**
	 * save_custom_user_profile_fields function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
	public function save_newsletter_extra_fields( $user_id ) {
		global $wp_roles;

				//if ( ! is_super_admin() && ! current_user_can( 'backwpup_admin' ) ) {
		//	return;
		//}

		if ( empty( $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST[ 'newsletter_role' ] ) ) {
			return;
		}
		
		// ! TODO DEBUG DA RIMUOVERE
		//echo '<pre>' . print_r( $_POST , 1 ) . '</pre>';
		//wp_die();

		$newsletter_role = esc_attr( $_POST[ 'newsletter_role' ] );

		//get BackWPup roles
		$newsletter_roles = array();
		foreach ( array_keys( $wp_roles->roles ) as $role ) {
			if ( ! strstr( $role, 'newsletter_' ) ) {
				continue;
			}
			$newsletter_roles[] = $role;
		}

		//get user for adding/removing role
		$user = new WP_User( $user_id );
		//a admin needs no extra role
		//if ( $user->has_cap( 'administrator' ) && $user->has_cap( 'backwpup_settings' ) ) {
		//	$backwpup_role = '';
		//}

		//remove BackWPup role from user if it not the actual
		foreach ( $user->roles as $role ) {
			if ( ! strstr( $role, 'newsletter_' ) ) {
				continue;
			}
			if ( $role !== $newsletter_role ) {
				$user->remove_role( $role );
			} else {
				$newsletter_role = '';
			}
		}

		//add new role to user if it not the actual
		if ( $newsletter_role && in_array( $newsletter_role, $newsletter_roles, true ) ) {
			$user->add_role( $newsletter_role );
		}
				
		return;
		
		
	}
		
	
	/**
	 * add_new_roles_and_caps function.
	 * 
	 * @access public
	 * @return void
	 */
	public function add_new_roles_and_caps(){
				
		/**
		 * Create new roles
		 *
		 * @since 1.0.0
		 */
		add_role( 'newsletter_admin', 					__( 'Newsletter admin', 'content-creation-newsletter' ), array( 'read' => true  ) );
		add_role( 'newsletter_editor',					__( 'Newsletter editor', 'content-creation-newsletter' ), array( 'read' => true  ) );
		add_role( 'newsletter_shortnews_editor', 		__( 'Shortnews editor', 'content-creation-newsletter' ), array( 'read' => true  ) );
		add_role( 'newsletter_shortnews_author', 		__( 'Shortnews author', 'content-creation-newsletter' ), array( 'read' => true  ) );
		add_role( 'newsletter_shortnews_contributor', 	__( 'Shortnews contributor', 'content-creation-newsletter' ), array( 'read' => true  ) );
		
		
		
		
		
		/**
		 * Add caps to roles
		 *
		 * @since 1.0.0
		 */
		 
		 
		/**
		 * Role administrator (WordPress Admin)
		 * 
		 * has all newsletter CAPS
		 * @since 1.0.0
		 */ 
		$role = get_role( 'administrator' );
			$role->add_cap( 'edit_shortnew' );
			$role->add_cap( 'edit_shortnews' );
			$role->add_cap( 'edit_other_shortnews' );
			$role->add_cap( 'publish_shortnews' );
			$role->add_cap( 'read_shortnew' );
			$role->add_cap( 'read_private_shortnews' );
			$role->add_cap( 'delete_shortnew' );
		/**
		 * Role newsletter_admin
		 *
		 * @since 1.0.0
		 */ 
		$role = get_role( 'newsletter_admin' );
			$role->add_cap( 'edit_shortnew' );
			$role->add_cap( 'edit_shortnews' );
			$role->add_cap( 'edit_other_shortnews' );
			$role->add_cap( 'publish_shortnews' );
			$role->add_cap( 'read_shortnew' );
			$role->add_cap( 'read_private_shortnews' );
			$role->add_cap( 'delete_shortnew' );
		
	}

	
}// END class
	 
	 $Wolly_Content_Newsletter_Creation_Utility_User_Second_User_Role = new Wolly_Content_Newsletter_Creation_Utility_User_Second_User_Role();