<?php

	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}

	/**
	 * we load all files and classes
	 *
	 * @since version 1.0
	 *
	 * @package Content & newsletter management
	 *
	 */
	 
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-cpt.php';
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-tax.php';	

	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-radio-tax.php';

	// backend options
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-options.php';
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-mailchimp-options.php';
		
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-create-newsletter.php';
	// end backend options
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-utility-user-second-role.php';
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-mailchimp-utility.php';

	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/classes/class-wolly-content-creation-newsletter-user-meta.php';
	
	require_once WOLLY_CONFORNEW_PLUGIN_PATH . '/inc/wolly-content-creation-wrappers.php';
	
	
	


