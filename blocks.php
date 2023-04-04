<?php
/**
 * Editor Blocks for SyntaxHighlighter Amplified
 *
 * @package SyntaxHighlighterAmplified
 *
 */

namespace SyntaxHighlighterAmplified;

$options = get_option( 'shamp_plugin_options' );

// Editor Blocks
if (
	function_exists( 'parse_blocks' ) // WordPress 5.0+
	|| function_exists( 'the_gutenberg_project' ) // Gutenberg plugin for older WordPress
) {
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );
	register_block_type(
		'shamp/code',
		array(
			'render_callback' => __NAMESPACE__ . '\render_block',
		)
	);
}

// Enqueue block assets for the Editor
function enqueue_block_editor_assets() {
	wp_enqueue_script(
		'shamp-blocks',
		plugin_dir_url( __FILE__ ) . 'blocks.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		'1.0.0'
	);

	wp_enqueue_style(
		'shamp-blocks-css', // Handle.
		plugin_dir_url( __FILE__ ) . 'editor.css',
		[],
		'1.0.0'
	);
	
	// WordPress 5.0+ only, no Gutenberg plugin support
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'shamp-blocks', 'shamp' );
	}
	
	global $languages;
	global $options;
	
	natcasesort( $languages );

	$settings = (object) array(
		'language' => (object) array(
			'supported' => true,
			'default' => 'plain'
		),
		'title' => (object) array(
			'supported' => true,
			'default' => $options['title'],
		),
		'gutter' => (object) array(
			'supported' => true,
			'default' => (bool) $options['line_numbers'],
		),
		'firstline' => (object) array(
			'supported' => true,
			'default' => ($options['first_line']?$options['first_line']:1),
		),
		'padlinenumbers' => (object) array(
			'supported' => true,
			'default' => $options['number_padding'],
		),
		'highlight' => (object) array(
			'supported' => true,
			'default' => '',
		),
		'altlines' => (object) array(
			'supported' => true,
			'default' => (bool) $options['alt_lines'],
		),
		'wraplines' => (object) array(
			'supported' => true,
			'default' => (bool) $options['wrap_lines'],
		),
		'autolinks' => (object) array(
			'supported' => true,
			'default' => (bool) $options['clickable_urls'],
		),
		'smarttabs' => (object) array(
			'supported' => true,
			'default' => (bool) $options['smart_tabs'],
		),
		'tabsize' => (object) array(
			'supported' => true,
			'default' => (int) ($options['tab_size']?$options['tab_size']:4),
		),
		'classname' => (object) array(
			'supported' => true,
			'default' => $options['css_classes'],
		),
	);

	wp_add_inline_script(
		'shamp-blocks',
		sprintf( '
				var shampData = {
					languages: %s,
					settings: %s,
				};',
				json_encode( array_unique( $languages ) ),
				json_encode( $settings )
			   ),
		'before'
	);
}

/**
 * Renders the content of the Gutenberg block on the front end
 * using the shortcode callback. This ensures one source of truth
 * and allows for forward compatibility.
 *
 * @param array $attributes The block's attributes.
 * @param string $content The block's content.
 *
 * @return string The rendered content.
 */
function render_block( $attributes, $content ) {
	
	$lang = $attributes['language']?$attributes['language']:'plain';
	
	$classnames = ! empty( $attributes['className'] ) ? [ esc_attr( $attributes['className'] ) ] : [];
	if ( ! empty( $attributes['align'] ) ) {
		$classnames[] = 'align' . esc_attr( $attributes['align'] );
	}

	$code = preg_replace( '#<pre [^>]+>([^<]+)?</pre>#', '$1', $content );

	// Undo escaping done by WordPress
	$code = htmlspecialchars_decode( $code );
	$code = preg_replace( '/^(\s*https?:)&#0?47;&#0?47;([^\s<>"]+\s*)$/m', '$1//$2', $code );

	// Render code
	$code = shortcode_callback( $attributes, $code, $lang );

	return '<div class="wp-block-shamp-code ' . esc_attr( join( ' ', $classnames ) ) . '">' . $code . '</div>';
}