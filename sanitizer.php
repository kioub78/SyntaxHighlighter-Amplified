<?php
/**
 * Sanitizer for SyntaxHighlighter Amplified.
 *
 * @package SyntaxHighlighterAmplified
 *
 */

namespace SyntaxHighlighterAmplified;

// Create list of language aliases and map them to their real names.
// The key is the language alias and the value is the specific langauge script name.
$languages = array(
	'as3'           => 'actionscript',
	'actionscript3' => 'actionscript',
	'arduino'       => 'arduino',
	'bash'          => 'bash',
	'shell'         => 'bash',
	'clojure'       => 'clojure',
	'clj'           => 'clojure',
	'cpp'           => 'cpp',
	'c'             => 'cpp',
	'c-sharp'       => 'cs',
	'csharp'        => 'cs',
	'css'           => 'css',
	'delphi'        => 'delphi',
	'pas'           => 'delphi',
	'pascal'        => 'delphi',
	'diff'          => 'diff',
	'patch'         => 'diff',
	'erl'           => 'erlang',
	'erlang'        => 'erlang',
	'fsharp'        => 'fsharp',
	'go'            => 'go',
	'golang'        => 'go',
	'groovy'        => 'groovy',
	'haskell'       => 'haskell',
	'java'          => 'java',
	'jfx'           => 'java',
	'javafx'        => 'java',
	'js'            => 'javascript',
	'jsx'           => 'javascript',
	'jscript'       => 'javascript',
	'javascript'    => 'javascript',
	'latex'         => 'tex', // Not used as a shortcode
	'tex'           => 'tex',
	'matlab'        => 'matlab',
	'matlabkey'     => 'matlab',
	'objc'          => 'objectivec',
	'obj-c'         => 'objectivec',
	'perl'          => 'perl',
	'pl'            => 'perl',
	'php'           => 'php',
	'plain'         => 'plaintext',
	'text'          => 'plaintext',
	'ps'            => 'powershell',
	'powershell'    => 'powershell',
	'py'            => 'python',
	'python'        => 'python',
	'r'             => 'r', // Not used as a shortcode
	'splus'         => 'r',
	'rails'         => 'ruby',
	'rb'            => 'ruby',
	'ror'           => 'ruby',
	'ruby'          => 'ruby',
	'scala'         => 'scala',
	'sql'           => 'sql',
	'swift'         => 'swift',
	'vb'            => 'vbnet',
	'vbnet'         => 'vbnet',
	'xml'           => 'xml',
	'xhtml'         => 'xml',
	'xslt'          => 'xml',
	'html'          => 'xml',
	'yaml'          => 'yaml',
	'yml'           => 'yaml',
);

/**
  * Sanitize CSS styles within the HTML contained in provided content.
  * 
  * @param string $content The HTML content to sanitize.
  * 
  * @return string|\Exception The sanitized content or error.
  */
function sanitize($content) {
	$options = get_option( 'shamp_plugin_options' );
	$regex = "/<pre\s[^>]*class=\"([^\"]*brush\s*:[^\"]*)\"[^>]*>(.*)<\/pre>/siU";
	if( preg_match_all($regex, $content, $matches) ) {
		$blocks = $matches[0];
		$classes = $matches[1];
		$codes = $matches[2];
		foreach($blocks as $idx => $block) {
			// Undo escaping done by WordPress
			$class = htmlspecialchars_decode( $classes[$idx] );
			$class = str_replace( '&#039;', "'", $class );
			$code = htmlspecialchars_decode( $codes[$idx] );
			$code = preg_replace( '/^(\s*https?:)&#0?47;&#0?47;([^\s<>"]+\s*)$/m', '$1//$2', $code );
			
			// Sanitize code block
			$hlblock = process_element( $class, $code, $options );
			$content = str_replace($block, $hlblock, $content);
		}
	}
	return $content;
}

/**
 * Highlight contents of syntaxhighlighter pre elements.
 *
 * @param string $class The value of class attribute of the pre element. This contains directives on how code highlight will be done.
 * @param string $code The body of the pre element representing the code to be highlighted.
 * @param array $options Plugin options used to configure how highlighted code is presented.
 * 
 * @return string|\Exception The sanitized content or error.
 */
function process_element( $class, $code, $options ) {
	$attrs = array();
	foreach ( explode( ';', $class ) as $attr_pair ) {
		$attr_pair = explode( ':', $attr_pair, 2 );
		if ( 2 !== count( $attr_pair ) ) {
			continue;
		}
		$attrs[ trim( $attr_pair[0] ) ] = trim( $attr_pair[1] );
	}

	$links = array();
	$autolinks = ( isset($attrs['autolinks']) ? $attrs['autolinks'] : $options['clickable_urls'] );
	if ( $autolinks && $autolinks !== "false" ) {
		$code = make_clickable($code);
		$regex = "/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU";
		if( preg_match_all($regex, $code, $matches) ) {
			$links = $matches[0];
			foreach($links as $idx => $link) {
				$code = str_replace($link, "shamplink" . $idx, $code);
			}
		}
	}

	$smarttabs = ( isset($attrs['smarttabs']) ? $attrs['smarttabs'] : $options['smart_tabs'] );
	$tabsize = ( ( isset($attrs['tabsize']) && trim($attrs['tabsize']) ) ? trim($attrs['tabsize']) : trim($options['tab_size']) );
	if ( $smarttabs && $smarttabs !== "false" ) {
		$tsize = ( $tabsize ? $tabsize : 4 );
		$code = str_replace("\t", str_repeat(' ', $tsize), $code);
	}

	$result = highlight( $attrs, $code );

	if ( $result instanceof \Exception ) {
		$result = new \WP_Error( 'highlight_error', $result->getMessage() );
	}

	if ( ! is_array( $result ) || ! isset( $result['value'] ) && ! isset( $result['language'] ) ) {
		return;
	}

	if  (  $autolinks && $autolinks !== "false" ) {
		foreach($links as $idx => $link) {
			$result = str_replace("shamplink" . $idx, $link, $result);
		}
	}
	
	$formattedcode = createHTMLTableFromArray( $options, $attrs, \HighlightUtilities\splitCodeIntoArray($result['value']) );
	
	$classname = ( ( isset($attrs['classname']) && str_replace("'", "", trim($attrs['classname'])) ) ? str_replace("'", "", trim($attrs['classname'])) : trim($options['css_classes']) );

	$title = ( ( isset($attrs['title']) && trim($attrs['title']) ) ? trim($attrs['title']) : trim($options['title']) );
	
	// Set the title variable if the title parameter is set (but not for feeds)
	if ( $title && ! is_feed() )
		$title_attr = ' title="' . esc_attr( $title ) . '"';

	$block = "<pre class=\"" . implode( ' ', array( $class, 'hljs', $result['language'], $classname ) ) . "\"" . $title_attr . "><code>" . $formattedcode . "</code></pre>";
	
	return $block;
}

/**
 * Highlight the provided code.
 *
 * @param array  $attrs Attributes.
 * @param string $code  Code.
 *
 * @return array|\Exception Result or error.
 */
function highlight( $attrs, $code ) {
	global $languages;
	try {
		$highlighter = new \Highlight\Highlighter();
		$language = $languages[$attrs['brush']];

		if ( $language ) {
			$r = $highlighter->highlight( $language, $code );
		} else {
			$r = $highlighter->highlightAuto( $code );
		}

		return array(
			'value'    => $r->value,
			'language' => $r->language,
		);
	} catch ( \Exception $e ) {
		return $e;
	}
}

/**
 * Create table of highlighted code, for numbering, line highlitting end such.
 *
 * @param array  $options Plugin options.
 * @param array  $attrs Pre tag attributes.
 * @param array  $lines Highlighted code in lines.
 *
 * @return string HTML table of code.
 */
function createHTMLTableFromArray( $options, $attrs, $lines ) {
	$count = count($lines);
	
	// trim array top
	for ($x = 0; $x < $count; $x++) {
		if ( ! trim($lines[$x]) ) {
			unset($lines[$x]);
		} else {
			break;
		}
	}
	
	// trim array bottom
	for ($x = $count; $x > 0; $x--) {
		if ( ! trim($lines[$x - 1]) ) {
			unset($lines[$x - 1]);
		} else {
			break;
		}
	}
	
	// create table
	$highlights = explode(",", substr($attrs['highlight'], 1, strlen($attrs['highlight']) - 2));

	foreach($highlights as $highlight) {
		if(false !== strpos( $highlight, '-' )) {
			unset($highlights[$highlight]);
			$bounds = explode("-", $highlight);
			for ($i = trim($bounds[0]); $i <= trim($bounds[1]); $i++) {
				array_push($highlights, $i);
			}
		}
	}

	$padlinenumbers = ( ( isset($attrs['padlinenumbers']) && trim($attrs['padlinenumbers']) ) ? trim($attrs['padlinenumbers']) : $options['number_padding'] );
	$idxpadding = ((!$padlinenumbers || $padlinenumbers === "false")?1:(($padlinenumbers === "true")?strlen(count($lines)):$padlinenumbers));

	$firstline = ( ( isset($attrs['firstline']) && trim($attrs['firstline']) ) ? trim($attrs['firstline']) : trim($options['first_line']) );
	$index = ($firstline?$firstline:1);
	
	$title = ( ( isset($attrs['title']) && trim($attrs['title']) ) ? trim($attrs['title']) : trim($options['title']) );
	$result = "<table>" . ( ( $title  && ! is_feed() ) ? "<caption>" . $title . "</caption>" : "" ) . "<tbody>";
	
	$altlines = (isset($attrs['altlines'])?$attrs['altlines']:$options['alt_lines']);
	$gutter = (isset($attrs['gutter'])?$attrs['gutter']:$options['line_numbers']);
	$wraplines = (isset($attrs['wraplines'])?$attrs['wraplines']:$options['wrap_lines']);
	
	if( $attrs['light'] ) {
		$gutter = false;
		$altlines = false;
	}
	
	foreach ($lines as $line) {
		$highlighted = in_array( $index, $highlights );
		$index = sprintf("%0" . $idxpadding . "d", $index);
		$result .= "<tr" . (($altlines && $altlines !== "false")?"":" class=\"nobg\"") . ">" . (($gutter && $gutter !== "false")?"<td id=\"L" . $index . "\" data-line-number=\"" . $index . "\"" . ($highlighted?" class=\"highlight\"":"") . "></td>":"") . "<td id=\"LC" . $index . "\" class=\"blob-code" . ($highlighted?" highlight":"") . (($gutter && $gutter !== "false")?"":" noidx") . "\"><pre" . (($wraplines && $wraplines !== "false")?" class=\"wrap\"":"") . "><code>" . $line . "</code></pre></td></tr>";
		$index++;
	}
	
	$result .= "</tbody></table>";
	
	return $result;
}