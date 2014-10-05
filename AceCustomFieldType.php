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
     * @remark\t^t  $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        //'type'        => 'textarea',
        'attributes'    =>  array(
            'cols'        => 60,
            'rows'        => 4,
        ),
        //
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
        return array(
        );
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

//      add_action( 'admin_footer', array( $this, '_replyToAddLinkModalQueryPlugin' ) );
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "jQuery( document ).ready( function(){
            // Hook up ACE editor to all textareas with data-ace_language attribute, from: http://stackoverflow.com/a/19513428/1434155
            jQuery('textarea[data-ace_language]').each(function () {
                var oTextarea = jQuery(this);

                var sMode = oTextarea.data('ace_language');
                var sTheme = oTextarea.data('ace_theme');
                var bGutter = ( undefined !== oTextarea.data('ace_gutter') ) ? oTextarea.data('ace_gutter') : 1;
                var bReadonly = ( undefined !== oTextarea.data('ace_readonly') ) ? oTextarea.data('ace_readonly') : 0;
                var sFontsize = ( undefined !== oTextarea.data('ace_fontsize') ) ? oTextarea.data('ace_fontsize') : 12;

                var oEditDiv = jQuery('<div>', {
                    position: 'absolute',
                    width: oTextarea.width(),
                    height: oTextarea.height(),
                    'class': oTextarea.attr('class')
                }).insertBefore(oTextarea);

                oTextarea.css('display', 'none');

                var oEditor = ace.edit(oEditDiv[0]);
                oEditor.renderer.setShowGutter(bGutter);
                oEditor.setFontSize(sFontsize);
                oEditor.setReadOnly(bReadonly);

                oEditor.getSession().setValue(oTextarea.val());
                oEditor.getSession().setMode('ace/mode/' + sMode);
                oEditor.setTheme('ace/theme/' + sTheme);

                // copy back to textarea on form submit...
                oTextarea.closest('form').submit(function () {
                    oTextarea.val(oEditor.getSession().getValue());
                })
            });


//             jQuery().registerAPFCallback( {

//               /**
//                * The repeatable field callback.
//                *
//                * When a repeat event occurs and a field is copied, this method will be triggered.
//                *
//                * @param  object  oCopied     the copied node object.
//                * @param  string  sFieldType  the field type slug
//                * @param  string  sFieldTagID the field container tag ID
//                * @param  integer iCallType   the caller type. 1 : repeatable sections. 0 : repeatable fields.
//                */
//               added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {

//                   /* If it is not this field type, do nothing. */
//                   if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) {
//                       return;
//                   }

//                   /* If the input tag is not found, do nothing  */
// //                  var oAceEditor = oCopied.find( 'textarea[data-ace_language]' );
//                   var oAceEditor = oCopied.find( '.ace_editor' );
//                   if ( oAceEditor.length <= 0 ) {
//                     alert('hmpf');
//                       return;
//                   }

//                 // Find the wrapper element
//                 var oWrapper = oAceEditor.closest( 'label' ).children( 'div' );
                
//                 // Not sure why but it needs to be cloned again (the framework repeater script clones it internally though)
//                 var oClone = oAceEditor.clone();    
//                 jQuery( oWrapper ).replaceWith( oClone );

//               }

//             });

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

        $aInputAttributes =  array_merge($aInputAttributes, $aField['attributes']);

        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
                        : ""
                    )
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