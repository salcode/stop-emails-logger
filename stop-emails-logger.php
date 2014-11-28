<?php
/*
 * Plugin Name: Stop Emails Logger
 * Plugin URI: http://salferrarello.com/stop-emails-wordpress-plugin/
 * Description: Add-on to Stop Emails plugin. This plugin logs stopped emails to a CPT
 * Version: 0.1.0
 * Author: Sal Ferrarello
 * Author URI: http://salferrarello.com/
 * Text Domain: stop-emails-logger
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// setup CPT
add_action( 'init', 'fe_stop_emails_logger_cpt' );

// add filter capture emails and log them as instances of CPT
add_filter( 'fe_stop_emails_log', 'fe_stop_emails_logger_log' );

// override template on display
add_filter( 'template_include', 'fe_stop_emails_logger_template_include' );

function fe_stop_emails_logger_template_include( $template ) {
	if (
		is_single() && 'fe_stopped_emails' === get_post_type()
	) {
		$template = dirname( __FILE__ ) . '/views/template.php';
	}

	return $template;
}

function fe_stop_emails_logger_log( $phpmailer ) {
	$new_post = array(
		'post_type'    => 'fe_stopped_emails',
		'post_status'  => 'publish',
		'post_title'   => $phpmailer->Subject,
		'post_content' => $phpmailer->Body,
	);
	$post_id = wp_insert_post( $new_post );

	$from_post_meta = $phpmailer->From;
	update_post_meta( $post_id, 'Email_From', $from_post_meta );

}


function fe_stop_emails_logger_cpt() {
	// CPT: Stopped Emails
	$labels = array(
		'name'                => _x( 'Stopped Emails', 'Plural Stopped Emails General Name', 'stop_emails_logger' ),
		'singular_name'       => _x( 'Stopped Email', 'Stopped Emails Singular Name', 'stop_emails_logger' ),
		'menu_name'           => __( 'Stopped Emails', 'stop_emails_logger' ),
		'parent_item_colon'   => __( 'Parent Stopped Email:', 'stop_emails_logger' ),
		'all_items'           => __( 'All Stopped Emails', 'stop_emails_logger' ),
		'view_item'           => __( 'View Stopped Emails', 'stop_emails_logger' ),
		'add_new_item'        => __( 'Add New Stopped Email', 'stop_emails_logger' ),
		'add_new'             => __( 'Add New', 'stop_emails_logger' ),
		'edit_item'           => __( 'Edit Stopped Email', 'stop_emails_logger' ),
		'update_item'         => __( 'Update Stopped Email', 'stop_emails_logger' ),
		'search_items'        => __( 'Search Stopped Email', 'stop_emails_logger' ),
		'not_found'           => __( 'Not found', 'stop_emails_logger' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'stop_emails_logger' ),
	);
	$rewrite = array(
		'slug'                => 'stopped-emails',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'fe_stopped_emails', 'stop_emails_logger' ),
		'description'         => __( 'Stopped Emails', 'stop_emails_logger' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'custom-fields', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => false,
		'menu_position'       => 10,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'fe_stopped_emails', $args );
}
