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
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'    =>  array(
            'cols'        => 60,
            'rows'        => 4,
        ),
        'options'   => array(
            'language'              => 'css',
            'theme'                 => 'chrome',
            'gutter'                => false,
            'readonly'              => false,
            'fontsize'              => 12,
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
            array( 'src'    => dirname( __FILE__ ) . '/ace-builds/src-min-noconflict/ace.js', 'dependencies'    => array( 'jquery' ) ),
        );
    }

    protected function getEnqueuingStyles() {
        return array();
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "jQuery( document ).ready( function(){
            // Add Ace editor to textarea  from: http://stackoverflow.com/a/19513428/1434155
            var addAceEditor = function(oTextArea) {

                var sMode     = oTextArea.data('ace_language');
                var sTheme    = oTextArea.data('ace_theme');
                var bGutter   = oTextArea.data('ace_gutter');
                var bReadonly = oTextArea.data('ace_readonly');
                var iFontsize = oTextArea.data('ace_fontsize');

                var oEditDiv = jQuery('<div>', {
                    position: 'absolute',
                    width: oTextArea.width(),
                    height: oTextArea.height(),
                    'class': oTextArea.attr('class')
                }).insertBefore(oTextArea);

                oTextArea.css('display', 'none');

                var oEditor = ace.edit(oEditDiv[0]);
                oEditor.renderer.setShowGutter(bGutter);
                oEditor.setFontSize(iFontsize);
                oEditor.setReadOnly(bReadonly);

                oEditor.getSession().setValue(oTextArea.val());
                oEditor.getSession().setMode('ace/mode/' + sMode);
                oEditor.setTheme('ace/theme/' + sTheme);

                // copy back to textarea on form submit...
                oTextArea.closest('form').submit(function () {
                    oTextArea.val(oEditor.getSession().getValue());
                })
            }

            // Add Ace editor to all textareas
            jQuery('textarea[data-ace_language]').each(function () {
                addAceEditor(jQuery(this));
            });

            jQuery().registerAPFCallback( {
                /**
                * The repeatable field callback.
                *
                * When a repeat event occurs and a field is copied, this method will be triggered.
                *
                * @param  object  oCopied     the copied node object.
                * @param  string  sFieldType  the field type slug
                * @param  string  sFieldTagID the field container tag ID
                * @param  integer iCallType   the caller type. 1 : repeatable sections. 0 : repeatable fields.
                */
                added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {
                    if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) return;

                    oCopied.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function( iIndex ) {

                        var oTextArea = jQuery( this ).find( 'textarea[data-ace_language]' );
                        if ( oTextArea.length <= 0 ) return true;

                        if (0 === iIndex) { // the newly added field
                            jQuery( this ).find( '.ace_editor').first().remove();
                            oTextArea.val( '' );    // only delete the value of the directly copied one
                            oTextArea.empty();      // the above use of val( '' ) does not erase the value completely.
                            addAceEditor(oTextArea);
                        }
                    });
                    return false;
                }
            });

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
            if (false === $value) $value = 0;
            $aInputAttributes['data-ace_' . $key] = $value;
        }
        unset( $aField['attributes']['value'] );
        $aInputAttributes =  array_merge($aInputAttributes, $aField['attributes']);

        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . "<textarea " . $this->generateAttributes( $aInputAttributes  ) . " >" 
                            . $aField['value']
                    . "</textarea>"
                    . $aField['after_input']
                . "</label>"
                . "<div class='repeatable-field-buttons'></div>"
            . "</div>"
            . $aField['after_label']
            ;
    }

}
endif;