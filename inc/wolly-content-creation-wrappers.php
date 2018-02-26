<?php
	
	/**
	 * Wrappers for common methods
	 *
	 * @package Content & newsletter management
	 * @subpackage useful functions
	 */
	 
	 if ( ! defined( 'ABSPATH' ) ) {
	     exit; // Exit if accessed directly
	 	}
	
	/**
	 * add_newsletter_mngt_to_newslsetter_menu function.
	 * 
	 * @access public
	 * @return void
	 */
	function add_newsletter_mngt_to_newslsetter_menu() {
   		add_submenu_page(
	        'newsletter-setting-admin',
			__( 'Name your Newsletters', 'content-creation-newsletter' ),
			__( 'Name your Newsletters', 'content-creation-newsletter' ),
			'manage_options',
			'edit.php?post_type=newsletter_mngt'			
			);
			
	}
	add_action('admin_menu' , 'add_newsletter_mngt_to_newslsetter_menu');
	
	
	/**
	 * wolly_order_sections function.
	 * 
	 * @access public
	 * @param obj $terms
	 * @return $sorted_terms by key ASC
	 */
	function wolly_order_sections( $terms ){
		 
		$sorted_terms =  array();
					
		$terms_number = count( $terms );
		$empty_terms = $terms_number;
					
		foreach ( $terms as $t ){ 
						
			$term_order = get_term_meta( $t->term_id, 'sort-order', true );
						
			if ( ! empty( $term_order ) && is_numeric( $term_order ) ){
							
				$sorted_terms[$term_order] = array(
											'term_id'	=> $t->term_id,
											'name'		=> $t->name,
											);
							 
				} else {
					
					$empty_terms ++;
					
					$sorted_terms[$empty_terms] = array(
												'term_id'	=> $t->term_id,
												'name'		=> $t->name,
												);

			}
						
						
		}
					
		ksort( $sorted_terms );
		
		return $sorted_terms;
	}
	
	
	/**
	 * wolly_get_newletters function.
	 * 
	 * @access public
	 * @return obj $newsletters
	 */
	function wolly_get_newletters(){

		$args = array(
			'post_type' => 'newsletter_mngt',
			'posts_per_page' => -1,
			
		);
		
		$newsletters = get_posts( $args );
		
		
		
		return $newsletters;
	}
