<?php
/**
 * @package Content Creation Newsletter 
 * @author Paolo Valenti
 * @version 1.0 First release
 */
/*
 Plugin Name: Content Creation Newsletter
 Plugin URI: http://goodpress.it
 Description: Create wonderful content for yours newsletters
 Author: Paolo Valenti aka Wolly 
 Version: 1.0
 Author URI: http://www.paolovalenti.info
 Text Domain: content-creation-newsletter
 Domain Path: /languages
 */
/*
 Copyright 2016  Paolo Valenti aka Wolly  (email : wolly66@gmail.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}

function wolly_content_for_newsletter_management_init() {
	  load_plugin_textdomain( 'content-creation-newsletter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
add_action('plugins_loaded', 'wolly_content_for_newsletter_management_init');

/** Plugin path */
define ( 'WOLLY_CONFORNEW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
/** Plugin dir */
define ( 'WOLLY_CONFORNEW_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
/** Plugin slug */
define ( 'WOLLY_CONFORNEW_PLUGIN_SLUG', basename( dirname( __FILE__ ) ) );
/** Plugin version */
define ( 'WOLLY_CONFORNEW_PLUGIN_VERSION', '1.0' );
/** Plugin version option name */
define ( 'WOLLY_CONFORNEW_PLUGIN_VERSION_NAME', 'WOLLY_CONFORNEW_version' );



/**
 * Load all files and classes
 *
 * @since 1.0
 */
require_once 'wolly-content-creation-newsletter-load.php';



/**
 * Wolly_Content_Newsletter class.
 */
class Wolly_Content_Creation_Newsletter {

	/**
	 * Wolly_Content_Newsletter::__construct()
	 *
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */

	public function __construct() {

		//check for plugin update (put in construct)
		add_action( 'init', array( $this, 'update_check' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'newsletter_admin_styles' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'jquery_effectts_core' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'jquery_validate' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'radiotax' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'jquery_select_newsletter' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'jquery_select_newsletter_create' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'jquery_sort_sections' ) );
		
		
		


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

	    	if ( version_compare( get_site_option( WOLLY_CONFORNEW_PLUGIN_VERSION_NAME ), WOLLY_CONFORNEW_PLUGIN_VERSION ) != 0  ) {

	    	$this->do_update();

	    	}

	 	} //end if only in the admin
	 }

	/**
	 * do_update function
	 *
	 * @access private
	 *
	 */
	public function do_update(){

	   //DO NOTHING, BY NOW, MAYBE IN THE FUTURE

	   //Update option

	   update_option( WOLLY_CONFORNEW_PLUGIN_VERSION_NAME , WOLLY_CONFORNEW_PLUGIN_VERSION );
	}


	/**
	 * newsletter_admin_styles function.
	 *
	 * @access public
	 * @return void
	 */
	public function newsletter_admin_styles() {

    	wp_register_style( 'newsletter_admin_stylesheet', plugins_url( '/css/newsletter.css', __FILE__ ) );
		wp_enqueue_style( 'newsletter_admin_stylesheet' );
	}

	/**
	 * jquery_validate function.
	 *
	 * @access public
	 * @return void
	 */
	public function jquery_validate() {

		wp_enqueue_script( 'jquery-validate-js',
		'' . WOLLY_CONFORNEW_PLUGIN_DIR .'js/jquery.validate.min.js',
		array( 'jquery' ),
		time(),
		true );
	}

	/**
	 * jquery_radiotax function.
	 *
	 * @access public
	 * @return void
	 */
	public function radiotax() {

		wp_enqueue_script( 'radiotax-js',
		'' . WOLLY_CONFORNEW_PLUGIN_DIR .'js/radiotax.js',
		array( 'jquery' ),
		time(),
		true );
	}
	
	/**
	 * jquery_select_newsletter function.
	 * 
	 * @access public
	 * @return void
	 */
	public function jquery_select_newsletter(){

		$screen = get_current_screen();

		if ( 'shortnews' == $screen->post_type && is_admin() ){

		wp_enqueue_script( 'select_newsletter-js',
		'' . WOLLY_CONFORNEW_PLUGIN_DIR .'js/choose.newsletter.in.short.news.js',
		array( 'jquery' ),
		time(),
		true );

		}


	}
	
	/**
	 * jquery_select_newsletter_create function.
	 * 
	 * @access public
	 * @return void
	 */
	public function jquery_select_newsletter_create(){

		$screen = get_current_screen();

		if ( 'mynewsletters_page_newsletter-creator' == $screen->id && is_admin() ){

		wp_enqueue_script( 'jquery_select_newsletter_create-js',
		'' . WOLLY_CONFORNEW_PLUGIN_DIR .'js/create.newsletter.js',
		array( 'jquery' ),
		time(),
		true );

		}


	}
	
	public function jquery_sort_sections(){

		$screen = get_current_screen();

		if ( 'newsletter_mngt' == $screen->id && is_admin() ){

		wp_enqueue_script( 'jquery_sort_sections-js',
		'' . WOLLY_CONFORNEW_PLUGIN_DIR .'js/sort.newsletter.sections.js',
		array( 'jquery', 'jquery-ui-core' ),
		time(),
		true );

		}


	}
	
	
	public function jquery_effectts_core(){
		
		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-effects-core' );
		
		$protocol = is_ssl() ? 'https' : 'http';
		$url = $protocol . '://ajax.googleapis.com/ajax/libs/jqueryui/1.12/themes/smoothness/jquery-ui.min.css';
		wp_enqueue_style('jquery-ui-smoothness', $url, false, null);

	}



}// chiudo la classe

//istanzio la classe

$Wolly_Content_Creation_Newsletter = new Wolly_Content_Creation_Newsletter();