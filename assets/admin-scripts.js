jQuery( function( $ ) {
    function plebRulesShippingMethodShowHideTaxIncludeField( el ) {
        var form = $( el ).closest( 'form' );
        var taxIncludeField = $( '#woocommerce_pleb_rules_method_prices_include_tax', form ).closest( 'tr' );
        if ( 'none' === $( el ).val() || '' === $( el ).val() ) {
            taxIncludeField.hide();

        } else {
            taxIncludeField.show();
        }
    }

    $( document.body ).on( 'change', '#woocommerce_pleb_rules_method_tax_status', function() {
        plebRulesShippingMethodShowHideTaxIncludeField( this );
    });

    $( '#woocommerce_pleb_rules_method_tax_status' ).trigger( 'change' );
    $( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
        if ( 'wc-modal-shipping-method-settings' === target ) {
            plebRulesShippingMethodShowHideTaxIncludeField( $( '#wc-backbone-modal-dialog #woocommerce_pleb_rules_method_tax_status', evt.currentTarget ) );
        }
    } );


    $('#pleb_rulesets').sortable({
        opacity: 0.7,
        revert: false,
        cursor: 'move',
        handle: '.hndle',
        items: '> .postbox',
        update: function(event, ui){
            $('#pleb_rulesets > .postbox').each(function(index, elem){
                $(elem).find('input[name^="woocommerce_pleb_rules_method_rulesets"][name$="[order]"]').val(index+1);
            });
        }
    });
});