<?php
/**
Plugin Name: Ajax Comment Form CST
Plugin URI: https://wordpress.org/plugins/ajax-comment-form-cst
Version: 1.2
Author: Codesoftech
Author uri: http://codesoftech.com/
Description: Convert your default wordpress comment form into ajax submit.
Text Domain: ajaxcommentformcst
*/

/*
 * Copyright 2019 Codesoftech
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 */
define( 'ACFORM_CST_FILE', __FILE__ ); // this file
define( 'ACFORM_CST_BASENAME', plugin_basename( ACFORM_CST_FILE ) ); // plugin base name
define( 'ACFORM_CST_DIR', dirname( ACFORM_CST_FILE ) ); // our directory
define( 'ACFORM_CST', ucwords( str_replace( array('-','cst'), array(' ','CST'), dirname( ACFORM_CST_BASENAME ) ) ) );
define( 'ACFORM_CST_VERSION', '1.0' );

define( 'ACFORM_CST_LIB', ACFORM_CST_DIR . '/lib' );

if ( defined( 'ACFORM_CST_BASENAME' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( is_plugin_active( ACFORM_CST_BASENAME ) ) {
		require_once ACFORM_CST_LIB . '/acform-cst-base-class.php'; // Base class for settings to be loaded
		$acformCstBase = new ACForm_CST_Base();
		$acform_cst_settings = $acformCstBase->init();
		
	    require_once ACFORM_CST_LIB . '/acform-cst-class.php';
		add_filter( 'plugin_action_links_' . ACFORM_CST_BASENAME, 'acform_cst_plugin_action_links' );
		register_deactivation_hook( __FILE__, 'acform_cst_deactivate' );
	}
}

function acform_cst_deactivate(){
		delete_option('acform_cst_settings');
}

function acform_cst_plugin_action_links( $links ) {
		array_unshift( $links, '<a href="options-general.php?page=acform_cst_settings">' . __( 'Settings') . '</a>' );
		return $links;
}
?>
