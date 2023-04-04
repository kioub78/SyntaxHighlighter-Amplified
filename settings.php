<?php
/**
 * Settings page for SyntaxHighlighter Amplified
 *
 * @package SyntaxHighlighterAmplified
 *
 */

namespace SyntaxHighlighterAmplified;


function shamp_add_settings_page() {
    add_options_page( 'SyntaxHighlighter Amplified Settings', 'SyntaxHighlighter Amplified', 'manage_options', 'shamp_plugin', __NAMESPACE__ . '\shamp_render_plugin_settings_page' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\shamp_add_settings_page' );

function shamp_render_plugin_settings_page() {
    ?>
    <h2><?php esc_html_e( 'SyntaxHighlighter Amplified Settings', 'shamp' ); ?></h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'shamp_plugin_options' );
        do_settings_sections( 'shamp_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_html_e( 'Save Changes', 'shamp' ); ?>" />
    </form>
    <?php
}

function shamp_register_settings() {
    register_setting( 'shamp_plugin_options', 'shamp_plugin_options' );
    add_settings_section( 'general_options', __( 'General Options', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_general_section_text', 'shamp_plugin' );
	add_settings_section( 'line_number_options', __( 'Line Number Options', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_line_number_section_text', 'shamp_plugin' );
	add_settings_section( 'alignment_options', __( 'Alignment Options', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_alignment_section_text', 'shamp_plugin' );
	add_settings_section( 'preview', __( 'Preview', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_preview_section_text', 'shamp_plugin' );
	add_settings_section( 'shortcode', __( 'Shortcode Parameters', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_shortcode_section_text', 'shamp_plugin' );

	add_settings_field( 'shamp_plugin_setting_color_theme', __( 'Color Theme', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_color_theme', 'shamp_plugin', 'general_options' );
	add_settings_field( 'shamp_plugin_setting_alt_lines', __( 'Alternate Lines', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_alt_lines', 'shamp_plugin', 'general_options' );
	add_settings_field( 'shamp_plugin_setting_wrap_lines', __( 'Wrap Lines', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_wrap_lines', 'shamp_plugin', 'general_options' );
	add_settings_field( 'shamp_plugin_setting_clickable_urls', __( 'Make URLs Clickable', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_clickable_urls', 'shamp_plugin', 'general_options' );
	add_settings_field( 'shamp_plugin_setting_css_classes', __( 'Additional CSS Class(es)', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_css_classes', 'shamp_plugin', 'general_options' );
	add_settings_field( 'shamp_plugin_setting_title', __( 'Title', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_title', 'shamp_plugin', 'general_options' );
	
	add_settings_field( 'shamp_plugin_setting_line_numbers', __( 'Display Line Numbers', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_line_numbers', 'shamp_plugin', 'line_number_options' );
	add_settings_field( 'shamp_plugin_setting_first_line', __( 'Starting Line Number', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_first_line', 'shamp_plugin', 'line_number_options' );
	add_settings_field( 'shamp_plugin_setting_number_padding', __( 'Line Number Padding', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_number_padding', 'shamp_plugin', 'line_number_options' );
	
	add_settings_field( 'shamp_plugin_setting_smart_tabs', __( 'Use Tabs for Alignment', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_smart_tabs', 'shamp_plugin', 'alignment_options' );
	add_settings_field( 'shamp_plugin_setting_tab_size', __( 'Tab Size', 'shamp' ), __NAMESPACE__ . '\shamp_plugin_setting_tab_size', 'shamp_plugin', 'alignment_options' );
}
add_action( 'admin_init', __NAMESPACE__ . '\shamp_register_settings' );

function shamp_plugin_general_section_text() {
    echo '<p>' . esc_html_x( 'Here you can set the general options of how code blocks are displaied.', 'shamp' ) . '</p>';
}

function shamp_plugin_line_number_section_text() {
    echo '<p>' . esc_html_x( 'Here you can set options regarding line numbers.', 'shamp' ) . '</p>';
}

function shamp_plugin_alignment_section_text() {
    echo '<p>' . esc_html_x( 'Here you can set the code alignment related options.', 'shamp' ) . '</p>';
}

function shamp_plugin_preview_section_text() {
    echo '<p>' . esc_html_x( 'Click "Save Changes" to update this preview.', 'shamp' ) . '</p>';
    $democode = '[php highlight="12"] 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>PHP Code Example</title>
	</head>
	<body>
		<h1>' . __( 'PHP Code Example', 'shamp' ) . '</h1>
		
		<p><?php echo \'' . __( 'Hello World!', 'shamp' ) . '\'; ?></p>
				
		<p>' . __( 'This line is highlighted.', 'shamp' ) . '</p>
				
		<div class="foobar">
'. __( '		This	is	an
		example	of	smart
		tabs.', 'shamp' ) . '
		</div>
				
		<p><a href="http://wordpress.org/">' . __( 'WordPress', 'shamp' ) . '</a></p>
	</body>
</html>[/php]';
	enqueue_styles();
	echo sanitize(priority_shortcode($democode));
}

function shamp_plugin_shortcode_section_text() {
	$shortcodes = implode(",", listSupportedShortcodes());
	$shortcodes = str_replace(',', ", ", $shortcodes);
	?>
	<p><?php printf( esc_html_x( 'These are the parameters you can pass to the shortcode and what they do. For the booleans (i.e. on/off), pass %1$s/%2$s or %3$s/%4$s.', 'shamp' ), '<code>true</code>', '<code>1</code>', '<code>false</code>', '<code>0</code>' ); ?></p>
	<ul class="ul-disc">
		<li>
			<?php printf(esc_html_x( '%1$s or %2$s &#8212; The language syntax to highlight with. You can alternately just use that as the tag, such as <code>[php]code[/php]</code>. Available tags: %3$s.', 'shamp' ), '<code>lang</code>', '<code>language</code>', $shortcodes ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle automatic URL linking.', 'shamp' ), '<code>autolinks</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Add additional CSS class(es) to the code box.', 'shamp' ), '<code>classname</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; An interger specifying what number the first line should be (for the line numbering).', 'shamp' ), '<code>firstline</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle the left-side line numbering.', 'shamp' ), '<code>gutter</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%1$s &#8212; A comma-separated list of line numbers to highlight. You can also specify a range. Example: %2$s', 'shamp' ), '<code>highlight</code>', '<code>2,5-10,12</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle alternate line background color.', 'shamp' ), '<code>altlines</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle light mode which disables the gutter and altlines all at once.', 'shamp' ), '<code>light</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%1$s &#8212; Controls line number padding. Valid values are %2$s (no padding), %3$s (automatic padding), or an integer (forced padding).', 'shamp' ), '<code>padlinenumbers</code>', '<code>false</code>', '<code>true</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle the use of tabs for code alignment.', 'shamp' ), '<code>smarttabs</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; An integer specifying how much space a tab occupies.', 'shamp' ), '<code>tabsize</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Toggle line wrapping.', 'shamp' ), '<code>wraplines</code>' ); ?></li>
		<li>
			<?php printf( esc_html_x( '%s &#8212; Sets some text to show up before the code.', 'shamp' ), '<code>title</code>' ); ?></li>
	</ul>
	<p><?php esc_html_e( 'Some example shortcodes:', 'shamp' ); ?></p>
	<ul class="ul-disc">
		<li><code>[php]<?php esc_html_e( 'your code here', 'shamp' ); ?>[/php]</code></li>
		<li><code>[css autolinks="false" classname="myclass" firstline="1" gutter="true" highlight="1-3,6,9" altlines="true" light="false" padlinenumbers="false" smarttabs="true" tabsize="4" wraplines="false" title="example-filename.php"]your code here[/css]</code></li>
		<li><code>[code lang="js"]<?php esc_html_e( 'your code here', 'shamp' ); ?>[/code]</code></li>
		<li><code>[sourcecode language="plain"]<?php esc_html_e( 'your code here', 'shamp' ); ?>[/sourcecode]</code></li>
	</ul>
	<?php
}

function shamp_plugin_setting_line_numbers() {
    $options = get_option( 'shamp_plugin_options' );
	?>
    <input id="shamp_plugin_setting_line_numbers" name="shamp_plugin_options[line_numbers]" type="checkbox" value="1" <?php checked( $options['line_numbers'], 1 ) ?>/>
	<?php
}

function shamp_plugin_setting_color_theme() {
	$themes = array(
			'default'    => 'Default',
			'github'     => 'GitHub', 
			'idea'       => 'Idea',
			'vs'         => 'Visual Studio',
			'grayscale'  => 'Grayscale',
			'midnight'   => 'Midnight',
			'googlecode' => 'Google Code',
	);
    $options = get_option( 'shamp_plugin_options' );
	?>
	<select id='shamp_plugin_setting_color_theme' name='shamp_plugin_options[color_theme]'>
	<?php
	foreach ( $themes as $theme => $name ) {
		echo '<option value="' . esc_attr( $theme ) . '"' . selected( $options['color_theme'], $theme, false ) . '>' . esc_html( $name ) . "&nbsp;</option>\n";
	}?>
	</select>
	<?php
}

function shamp_plugin_setting_alt_lines() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_alt_lines" name="shamp_plugin_options[alt_lines]" type="checkbox" value="1" <?php checked( $options['alt_lines'], 1 ) ?>/>
	<?php
}

function shamp_plugin_setting_wrap_lines() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_wrap_lines" name="shamp_plugin_options[wrap_lines]" type="checkbox" value="1" <?php checked( $options['wrap_lines'], 1 ) ?>/>
	<?php
}

function shamp_plugin_setting_clickable_urls() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_clickable_urls" name="shamp_plugin_options[clickable_urls]" type="checkbox" value="1" <?php checked( $options['clickable_urls'], 1 ) ?>/>
	<?php
}

function shamp_plugin_setting_smart_tabs() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_smart_tabs" name="shamp_plugin_options[smart_tabs]" type="checkbox" value="1" <?php checked( $options['smart_tabs'], 1 ) ?>/>
	<?php
}

function shamp_plugin_setting_tab_size() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_tab_size" name="shamp_plugin_options[tab_size]" type="text" value="<?php echo ($options['tab_size']?$options['tab_size']:4) ?>" class="small-text" />
	<?php
}

function shamp_plugin_setting_first_line() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_first_line" name="shamp_plugin_options[first_line]" type="text" value="<?php echo ($options['first_line']?$options['first_line']:1) ?>" class="small-text" />
	<?php
}

function shamp_plugin_setting_number_padding() {
	$paddings = array(
			'false'	=> __( 'Off', 'shamp' ),
			'true'	=> __( 'Automatic', 'shamp' ), 
			1		=> '1',
			2		=> '2',
			3		=> '3',
			4		=> '4',
			5		=> '5',
	);
    $options = get_option( 'shamp_plugin_options' );
	?>
	<select id='shamp_plugin_setting_number_padding' name='shamp_plugin_options[number_padding]'>
	<?php
	foreach ( $paddings as $padding => $name ) {
		echo '<option value="' . esc_attr( $padding ) . '"' . selected( $options['number_padding'], $padding, false ) . '>' . esc_html( $name ) . "&nbsp;</option>\n";
	}?>
	</select>
	<?php
}

function shamp_plugin_setting_css_classes() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_css_classes" name="shamp_plugin_options[css_classes]" type="text" value="<?php echo $options['css_classes'] ?>" class="regular-text" />
	<?php
}

function shamp_plugin_setting_title() {
    $options = get_option( 'shamp_plugin_options' );
	?>
	<input id="shamp_plugin_setting_title" name="shamp_plugin_options[title]" type="text" value="<?php echo $options['title'] ?>" class="regular-text" />
	<?php
}