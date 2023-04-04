<?php
/**
 * SyntaxHighlighterAmplified
 *
 * @package SyntaxHighlighterAmplified
 *
 * @wordpress-plugin
 * Plugin Name: SyntaxHighlighter Amplified
 * Description: Syntax highliting plugin which uses highlight.php to add server side syntax highlighting - compatible with AMP pages.
 * Author: Byron Kiourtzoglou, JCG
 * Author URI: https://www.javacodegeeks.com
 * Text Domain:  shamp
 * Version: 1.0.0
 * License: GPLv2 or later
 * Requires at least: 5.7
 * Tested up to: 6.2
 * Requires PHP: 7.0
 */


namespace SyntaxHighlighterAmplified;

require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/sanitizer.php';
include_once __DIR__ . '/shortcodes.php';
include_once __DIR__ . '/settings.php';
include_once __DIR__ . '/blocks.php';


// Load localization domain
load_plugin_textdomain( 'shamp' );

// Display hooks
add_filter( 'the_content', __NAMESPACE__ . '\render_code' );
add_filter( 'widget_text', __NAMESPACE__ . '\render_code' );

 
/**
 * Enqueue our own styles and renders code blocks if present.
 */
function render_code($content) {
	if ( preg_match('/<pre class="(.*)\s*brush\s*:\s*/i', $content) || preg_match('/<div(.*)\s*class="syntaxhighlighter(.*)">/i', $content) ) {
		enqueue_styles();
		$content = sanitize($content);
	}
	return $content;
}

/**
 * Enqueue styles.
 */
function enqueue_styles() {
	$options = get_option( 'shamp_plugin_options' );
	wp_enqueue_style(
		'hjjs-default',
		plugin_dir_url( __FILE__ ) . 'vendor/scrivo/highlight.php/styles/' . ($options['color_theme']?$options['color_theme']:"default") . '.css',
		array(),
		'1.0.0'
	);
	wp_enqueue_style(
		'shamp-default',
		plugin_dir_url( __FILE__ ) . 'style.css',
		array(),
		'1.0.0'
	);
}

/**
 * Add actions to the plugin entry at the Plugins list page.
 *
 * @param array $actions Actions.
 * @return array Actions.
 */
function shamp_plugin_action_links( $actions ) {	
	$mylinks = array(
      '<a href="' . admin_url( 'options-general.php?page=shamp_plugin' ) . '">' . __( 'Settings', 'shamp' ) . '</a>',
	);
   $actions = array_merge( $actions, $mylinks );
   return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__),  __NAMESPACE__ . '\shamp_plugin_action_links' );
