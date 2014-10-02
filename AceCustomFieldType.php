<?php
/**
 * AceCustomFieldType, for the  Admin Page Framework by Michael Uno, is written by Per Soderlind - http://soderlind.no
 */
if ( ! class_exists( 'AceCustomFieldType' ) ) :
class AceCustomFieldType extends AdminPageFramework_FieldType {

	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'ace', );

	/**
	 * Defines the default key-values of this field type settings.
	 *
	 * @remark          $_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'type'          => 'textarea',
		'attributes'    =>  array(
			'cols'          => 60,
			'rows'          => 4,
		),
		//
        'options'   => array(
        	'language'      			=> 'css',
        	'theme'         			=> 'chrome',
            // 'soft_wrap'                 => 'off', //40, 80, free
            // 'folding'                   => true,
            // 'highlight_active'          => true,
            // 'show_hidden'               => true,
            // 'show_gutter'               => true,
            // 'show_print_margin'         => true,
            // 'highlight_selected_word'   => true,
            // 'show_hscroll'              => true,
            // 'show_vscroll'              => true,
            // 'animate_scroll'            => true,
            // 'soft_tab'                  => true,
            // 'enable_behaviours'         => true,
        ),
	);


	/**
	 * Loads the field type necessary components.
	 */
	public function setUp() {
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() {
		return array(
			array( 'src'	=> dirname( __FILE__ ) . '/ace-builds/src-min-noconflict/ace.js', 'dependencies'	=> array( 'jquery' ) ),
		);
	}

	protected function getEnqueuingStyles() {
		return array(
		);
	}

	/**
	 * Returns the field type specific JavaScript script.
	 */
	protected function getScripts() {

//		add_action( 'admin_footer', array( $this, '_replyToAddLinkModalQueryPlugin' ) );
		$_aJSArray = json_encode( $this->aFieldTypeSlugs );
		return "jQuery( document ).ready( function(){
			// Hook up ACE editor to all textareas with data-ace_language attribute, from: http://stackoverflow.com/a/19513428/1434155
	        jQuery('textarea[data-ace_language]').each(function () {
	            var oTextarea = jQuery(this);

	            var sMode = oTextarea.data('ace_language');
	            var sTheme = oTextarea.data('ace_theme');
				var bShow_gutter = ( undefined != oTextarea.data('ace_show_gutter') ) ? oTextarea.data('ace_show_gutter') : 'true';

	            var oEditDiv = jQuery('<div>', {
	                position: 'absolute',
	                width: oTextarea.width(),
	                height: oTextarea.height(),
	                'class': oTextarea.attr('class')
	            }).insertBefore(oTextarea);

	            oTextarea.css('display', 'none');

	            var oEditor = ace.edit(oEditDiv[0]);
	            oEditor.renderer.setShowGutter(bShow_gutter);
	            oEditor.getSession().setValue(oTextarea.val());
	            oEditor.getSession().setMode('ace/mode/' + sMode);
	            if (sTheme) oEditor.setTheme('ace/theme/' + sTheme);

	            // copy back to textarea on form submit...
	            oTextarea.closest('form').submit(function () {
	                oTextarea.val(oEditor.getSession().getValue());
	            })
	        });


            // jQuery().registerAPFCallback( {

            //     /**
            //      * The repeatable field callback.
            //      *
            //      * When a repeat event occurs and a field is copied, this method will be triggered.
            //      *
            //      * @param	object	oCopied		the copied node object.
            //      * @param	string	sFieldType	the field type slug
            //      * @param	string	sFieldTagID	the field container tag ID
            //      * @param	integer	iCallType	the caller type. 1 : repeatable sections. 0 : repeatable fields.
            //      */
            //     added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {

            //         /* If it is not this field type, do nothing. */
            //         if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) {
            //             return;
            //         }

            //         /* If the input tag is not found, do nothing  */
            //         var oLinkModalInput = oCopied.find( 'input.link_modal_dialog' );
            //         if ( oLinkModalInput.length <= 0 ) {
            //             return;
            //         }

            //         // Find the 'Select Link' button and update its id (it is copied so the id is still the same as the original one of the clone.)
            //         var oLinkModalSelectButton = oCopied.find( '.select_link' );

            //         // Now attach the event.
            //         oLinkModalSelectButton.link_modal_dialog();

            //     }

            // });

        });";

	}

	/**
	 * Returns the field type specific CSS rules.
	 */
	protected function getStyles() {
		return "/*Ace editor Custom Field Type*/
			.ace_editor {
			        position: relative !important;
			        border: 1px solid lightgray;
			}
		"  . PHP_EOL;
	 }

	/**
	 * Returns the output of the field type.
	 */
	public function getField( $aField ) {

		$aInputAttributes = array();
		foreach ($aField['options'] as $key => $value) {
			$aInputAttributes['data-ace_' . $key] = $value;
		}

		$aInputAttributes =  array_merge($aInputAttributes, $aField['attributes']);

		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<div class='repeatable-field-buttons'></div>"    // the repeatable field buttons will be replaced with this element.
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: ""
					)

					. "<textarea " . $this->generateAttributes( $aInputAttributes /*$aField['attributes']*/ ) . " >" // this method is defined in the base class
                            . $aField['value']
                    . "</textarea>"


					//. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
					//. "<a href='#' id='select_{$aField['input_id']}' class='select_link button button-small' >" . __( 'Select Link', 'admin-page-framework' ) . "</a>"
					. $aField['after_input']
					//. $this->_getExtraInputs( $aField )
				. "</label>"
			. "</div>"
			. $aField['after_label']
			//. $this->_getUploadButtonScript( $aField['input_id'] );
			;
	}


	// protected function _getExtraInputs( $aField ) {

	// 	return '<input ' . $this->generateAttributes(
	// 			array(
	// 				'id'    =>  "{$aField['input_id']}_title",
	// 				'type'  =>  'hidden',
	// 				'name'  =>  "{$aField['_input_name']}[title]",
	// 				'value' =>  isset( $aField['attributes']['value']['title'] ) ? $aField['attributes']['value']['title'] : '',
	// 			)
	// 		) . '/>' . PHP_EOL
	// 		. '<input ' . $this->generateAttributes(
	// 			array(
	// 				'id'    =>  "{$aField['input_id']}_target",
	// 				'type'  =>  'hidden',
	// 				'name'  =>  "{$aField['_input_name']}[target]",
	// 				'value' =>  isset( $aField['attributes']['value']['target'] ) ? $aField['attributes']['value']['target'] : '',
	// 			)
	// 		) . '/>' . PHP_EOL;

	// }


	/**
	 * Returns the field type specific JavaScript script.
	 */
	//  protected function _getUploadButtonScript( $sInputID ) {

	// 	return "<script type='text/javascript' class='admin-page-framework-link-modal-enabler-script'>"
	// 			. "jQuery( document ).ready( function(){
	// 				jQuery( '#select_{$sInputID}' ).link_modal_dialog();
	// 			});"
	// 		. "</script>". PHP_EOL;

	// }

	/**
	 * Prints out the jQuery plugin that adds the link modal dialog.
	 *
	 */
	// public function _replyToAddLinkModalQueryPlugin() {

	// 	$_sScript = "
	// 	(function ( $ ) {

	// 		$.fn.link_modal_dialog = function() {

	// 			this.on( 'click', function( event ) {

	// 				// Find the input id and set the global variable.
	// 				var oInput          = $( this ).siblings( '.link_modal_dialog' );
	// 				sInputID_LinkModal  = oInput.attr( 'id' );

 //                    // for WP v3.8x or below
	// 				wpActiveEditor      = oInput.attr( 'id' );
 //                    tinyMCEPopup        = 'undefined' !== typeof tinyMCEPopup ? tinyMCEPopup : null;

 //                    // Open the modal dialog. Since v3.9, we can directly pass the element id to the parameter.
	// 				wpLink.open( oInput.attr( 'id' ) );

	// 				return false;

	// 			});

 //                this.on( 'wplink-close', function() {
 //                    console.log( 'closed' );
 //                    console.log( arguments );
 //                });

	// 		};

	// 	}( jQuery ));";

	// 	echo "<script type='text/javascript' class='admin-page-framework-linkmodal-jQuery-plugin'>{$_sScript}</script>";

	// }

}
endif;