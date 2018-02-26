<?php
	
	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}



	/**
	 * Wolly_Newsletter_User_Meta class.
	 */
	class Wolly_Newsletter_User_Meta{

		public function __construct(){
			add_action( 'show_user_profile', array( $this, 'show_print_author_checkbox' ) );
			add_action( 'edit_user_profile', array( $this, 'show_print_author_checkbox' ) );

			add_action( 'personal_options_update', array( $this, 'save_print_author_checkbox' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_print_author_checkbox' ) );
		}



		/**
		 * show_print_author_checkbox function.
		 *
		 * @access public
		 * @param mixed $user
		 * @return void
		 */
		function show_print_author_checkbox( $user ) {

			$print_author = get_user_meta( $user->ID, 'print_author_checkbox', true );

		?>

			<h3><?php _e( 'Newsletter settings', 'content-creation-newsletter' ) ?></h3>

			<table class="form-table">

				<tr>
					<th><label for="print_user"><?php _e( 'Print author name ', 'content-creation-newsletter' ) ?></label></th>

					<td>
					<span class="description"><?php _e( 'If checked, author name will be printed ', 'content-creation-newsletter' ) ?></span> <input type="checkbox" name="print_user" id="print_user" value="yes"  <?php checked( 'yes', $print_author  ); ?>/><br />

					</td>
				</tr>

			</table>
			
			
			
			
		<?php }


		/**
		 * save_print_author_checkbox function.
		 *
		 * @access public
		 * @param mixed $user_id
		 * @return void
		 */
		function save_print_author_checkbox( $user_id ) {

			if ( ! current_user_can( 'edit_user', $user_id ) )
				return false;

			if ( ! isset( $_POST['print_user'] ) ){

				delete_usermeta( $user_id, 'print_author_checkbox' );

				} else {

				if ( 'yes' == $_POST['print_user'] ){

					update_usermeta( $user_id, 'print_author_checkbox', $_POST['print_user'] );

					} else {

						delete_usermeta( $user_id, 'print_author_checkbox' );
				}

			}
		}


	}

	$wolly_newsletter_user_meta = new Wolly_Newsletter_User_Meta();