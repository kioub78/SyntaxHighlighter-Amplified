/**
 * BLOCK: SyntaxHighlighter Amplified (shamp/code)
 */

const { settings, languages } = window.shampData;

let definition = {
	title: wp.i18n.__( 'SyntaxHighlighter Code', 'shamp' ),
	description: wp.i18n.__( 'Adds syntax highlighting to source code.', 'shamp' ),
	icon: 'editor-code',
	category: 'formatting',
	keywords: [
		wp.i18n.__( 'Source', 'shamp' ),
		wp.i18n.__( 'Program', 'shamp' ),
		wp.i18n.__( 'Develop', 'shamp' ),
	],
	attributes: {
		content: {
			type: 'string',
			source: 'text',
			selector: 'pre',
		},
		language: {
			type: 'string',
			default: settings.language.default,
		},
		title: {
			type: 'string',
			default: settings.title.default,
		},
		gutter: {
			type: 'boolean',
			default: settings.gutter.default,
		},
		firstline: {
			type: 'string',
			default: settings.firstline.default,
		},
		padlinenumbers: {
			type: 'string',
			default: settings.padlinenumbers.default,
		},
		highlight: {
			type: 'string',
		},
		altlines: {
			type: 'boolean',
			default: settings.altlines.default,
		},
		wraplines: {
			type: 'boolean',
			default: settings.wraplines.default,
		},
		autolinks: {
			type: 'boolean',
			default: settings.autolinks.default,
		},
		smarttabs: {
			type: 'boolean',
			default: settings.smarttabs.default,
		},
		tabsize: {
			type: 'int',
			default: settings.tabsize.default,
		},
		classname: {
			type: 'string',
			default: settings.classname.default,
		},
	},
	supports: {
		html: false,
		align: true,
	},
	edit: function ( { attributes, setAttributes, className } ) {
		const {
			content,
		} = attributes;

		const tabSize = settings.tabSize;

		return wp.element.createElement( 
			wp.element.Fragment, 
			{},
			blockSettingsElements( { attributes, setAttributes } ),
			wp.element.createElement( 
				"div", 
				{
					className:className + ' wp-block-code',
				},
				wp.element.createElement( 
					wp.editor.PlainText, 
					{
						className:"wp-block-shamp__textarea",
						value:content,
						style: { tabSize, MozTabSize: '' + tabSize },
						onChange: function( nextContent  ) {
							setAttributes( { content: nextContent } );
						},
						placeholder: wp.i18n.__( 'Tip: you can choose a code language from the block settings.', 'shamp' ),
						arialabel: wp.i18n.__( 'SyntaxHighlighter Amplified Code', 'shamp' )
					} 
				)
			)
		);
	},
	save: function ( { attributes } ) {
		return wp.element.createElement( 
			"pre", 
			{},
			escape( attributes.content )
		);
	}
};

function blockSettingsElements( { attributes, setAttributes } ) {
	const blockSettings = [];
	const toolbar = [];

	const {
		language,
		title,
		gutter,
		firstline,
		padlinenumbers,
		highlight,
		altlines,
		wraplines,
		autolinks,
		smarttabs,
		tabsize,
		classname,
	} = attributes;

	// Language
	if ( settings.language.supported ) {
		const options = [];
		for ( const lang in languages ) {
			options.push( {
				name: languages[ lang ],
				key: lang,
			} );
		}

		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "code-language"
				},
				wp.element.createElement(
					wp.components.CustomSelectControl,
					{
						label: wp.i18n.__( 'Code Language', 'shamp' ),
						value: options.find( ( option ) => option.key === language ),
						options: options,
						className: "shamp-settings-dropdown",
						onChange: ( nextLanguage ) => setAttributes( { language: nextLanguage.selectedItem.key } )
					}
				)
			)
		)
		
		toolbar.push( languageToolbar( { attributes, setAttributes, options } ) );
	}
	
	// Title
	if ( settings.title.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "title"
				},
				wp.element.createElement(
					wp.components.TextControl,
					{
						label: wp.i18n.__( 'Title', 'shamp' ),
						type: "string",
						value: title,
						onChange: ( nextTitle ) => setAttributes( { title: nextTitle } )
					}
				)
			)
		)
	}
	
	// Line numbers
	if ( settings.gutter.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "show-line-numbers"
				},
				wp.element.createElement(
					wp.components.ToggleControl,
					{
						label: wp.i18n.__( 'Show Line Numbers', 'shamp' ),
						checked: gutter,
						onChange: ( nextGutter ) => setAttributes( { gutter: nextGutter } )
					}
				)
			)
		)
	}

	// First line number
	if ( gutter && settings.firstline.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "first-line-number"
				},
				wp.element.createElement(
					wp.components.TextControl,
					{
						label: wp.i18n.__( 'First Line Number', 'shamp' ),
						type: "number",
						value: firstline,
						onChange: ( nextFirstline ) => setAttributes( { firstline: nextFirstline } ),
						min: "1",
						max: "100000"
					}
				)
			)
		)
	}

	// Number Padding
	if ( gutter && settings.padlinenumbers.supported ) {
		const options = [
			{ key: 'false', name: wp.i18n.__( 'Off', 'shamp' ) },
			{ key: 'true', name: wp.i18n.__( 'Automatic', 'shamp' ) },
			{ key: '1', name: '1' },
			{ key: '2', name: '2' },
			{ key: '3', name: '3' },
			{ key: '4', name: '4' },
			{ key: '5', name: '5' }
		];
		
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "number-padding"
				},
				wp.element.createElement(
					wp.components.CustomSelectControl,
					{
						label: wp.i18n.__( 'Number Padding', 'shamp' ),
						value: options.find( ( option ) => option.key === padlinenumbers ),
						options: options,
						className: "shamp-settings-dropdown",
						onChange: ( nextPadlinenumbers ) => setAttributes( { padlinenumbers: nextPadlinenumbers.selectedItem.key } )
					}
				)
			)
		)
	}
	
	// Highlight line(s)
	if ( settings.highlight.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "highlight-lines"
				},
				wp.element.createElement(
					wp.components.TextControl,
					{
						label: wp.i18n.__( 'Highlight Lines', 'shamp' ),
						value: highlight,
						help: wp.i18n.__( 'A comma-separated list of line numbers to highlight. Can also be a range. Example: 1,5,10-20', 'shamp' ),
						onChange: ( nextHighlight ) => setAttributes( { highlight: nextHighlight } )
					}
				)
			)
		)
	}

	// Alternate lines
	if ( settings.altlines.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "alternate-lines"
				},
				wp.element.createElement(
					wp.components.ToggleControl,
					{
						label: wp.i18n.__( 'Alternate Lines', 'shamp' ),
						checked: altlines,
						onChange: ( nextAltlines ) => setAttributes( { altlines: nextAltlines } )
					}
				)
			)
		)
	}
	
	// Wrap long lines
	if ( settings.wraplines.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "wrap-long-lines"
				},
				wp.element.createElement(
					wp.components.ToggleControl,
					{
						label: wp.i18n.__( 'Wrap Long Lines', 'shamp' ),
						checked: wraplines,
						onChange: ( nextWraplines ) => setAttributes( { wraplines: nextWraplines } )
					}
				)
			)
		)
	}
	
	// Make URLs clickable
	if ( settings.autolinks.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "make-urls-clickable"
				},
				wp.element.createElement(
					wp.components.ToggleControl,
					{
						label: wp.i18n.__( 'Make URLs Clickable', 'shamp' ),
						checked: autolinks,
						onChange: ( nextAutolinks ) => setAttributes( { autolinks: nextAutolinks } )
					}
				)
			)
		)
	}
	
	// Smart Tabs
	if ( settings.smarttabs.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "smart-tabs"
				},
				wp.element.createElement(
					wp.components.ToggleControl,
					{
						label: wp.i18n.__( 'Smart Tabs', 'shamp' ),
						checked: smarttabs,
						onChange: ( nextSmarttabs ) => setAttributes( { smarttabs: nextSmarttabs } )
					}
				)
			)
		)
	}
	
	// Tab Size
	if ( smarttabs && settings.tabsize.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "tab-size"
				},
				wp.element.createElement(
					wp.components.TextControl,
					{
						label: wp.i18n.__( 'Tab Size', 'shamp' ),
						type: "number",
						value: tabsize,
						onChange: ( nextTabsize ) => setAttributes( { tabsize: nextTabsize } ),
						min: "1",
						max: "100"
					}
				)
			)
		)
	}
	
	// CSS Classes
	if ( settings.classname.supported ) {
		blockSettings.push(
			wp.element.createElement( 
				wp.components.PanelRow,
				{
					key: "classname"
				},
				wp.element.createElement(
					wp.components.TextControl,
					{
						label: wp.i18n.__( 'Additional CSS Class(es)', 'shamp' ),
						type: "string",
						value: classname,
						onChange: ( nextClassname ) => setAttributes( { classname: nextClassname } )
					}
				)
			)
		)
	}
	
	return wp.element.createElement(
		wp.element.Fragment,
		{},
		wp.element.createElement(
			wp.editor.BlockControls,
			{},
			wp.element.createElement(
				wp.components.ToolbarGroup,
				{},
				toolbar
			)
		),
		wp.element.createElement(
			wp.editor.InspectorControls,
			{
				key: "shampInspectorControls"
			},
			wp.element.createElement(
				wp.components.PanelBody,
				{
					title: "Settings"
				},
				blockSettings
			)
		)
    );

}

function languageToolbar ( { attributes, setAttributes, options } ) {
	const { language } = attributes;
	
	return wp.element.createElement( 
		ToolbarDropdown,
		{
			key: "code-language",
			options: options,
			optionsLabel: wp.i18n.__( 'Code Language', 'shamp' ),
			value: language,
			onChange: ( value ) => setAttributes( { language: value } )
		}
    );

}

function ToolbarDropdown ( { options, optionsLabel, icon, value, onChange, ...props } ) {
	const selectedOption = options.find( function( option ) { return value === option.key } );
	
	return wp.element.createElement(
		wp.components.Dropdown,
		{
			className: "shamp-toolbar-dropdown",
			popoverProps: {
				isAlternate: true,
				position: 'bottom right left',
				focusOnMount: true,
				className: 'shamp-toolbar-dropdown__popover'
			},
			renderToggle: ( { isOpen, onToggle } ) => {
				return wp.element.createElement(
					wp.components.Button,
					{
						onClick: onToggle,
						icon: icon,
						ariaexpanded: isOpen,
						ariahaspopup: "true",
						children: selectedOption ? selectedOption.name : ''
					}
				)
			},
			renderContent: ( { onClose } ) => {
				return wp.element.createElement(
					wp.components.NavigableMenu,
					{
						role: "menu",
						stopNavigationEvents: true
					},
					wp.element.createElement(
						wp.components.MenuGroup,
						{
							label: optionsLabel
						},
						options.map( ( option ) => {
							const isSelected = option.key === selectedOption.key;
							return wp.element.createElement(
								wp.components.MenuItem,
								{
									key: option.key,
									role: "menuitemradio",
									isSelected: isSelected,
									className: 'shamp-toolbar-dropdown__option' + (isSelected ? 'is-selected' : ''),
									onClick: () => {
										onChange( option.key );
										onClose();
									},
									children: option.name
								}
							)
						} )
					)
				)
			},
			...props
		}
    )
	
}

/**
 * Escapes ampersands, shortcodes, and links.
 *
 * @param {string} content The content of a code block.
 * @return {string} The given content with some characters escaped.
 */
function escape( content ) {
	return lodash.flow(
		wp.escapeHtml.escapeEditableHTML,
		escapeProtocolInIsolatedUrls
	)( content || '' );
}

/**
 * Converts the first two forward slashes of any isolated URL into their HTML
 * counterparts (i.e. // => &#47;&#47;). For instance, https://youtube.com/watch?x
 * becomes https:&#47;&#47;youtube.com/watch?x.
 *
 * An isolated URL is a URL that sits in its own line, surrounded only by spacing
 * characters.
 *
 * See https://github.com/WordPress/wordpress-develop/blob/5.1.1/src/wp-includes/class-wp-embed.php#L403
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with its ampersands converted into
 *                  their HTML entity counterpart (i.e. & => &amp;)
 */
function escapeProtocolInIsolatedUrls( content ) {
	return content.replace(
		/^(\s*https?:)\/\/([^\s<>"]+\s*)$/m,
		'$1&#47;&#47;$2'
	);
}

wp.blocks.registerBlockType( 'shamp/code', definition );