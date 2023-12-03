jQuery( function( $ ) {
    function plebRulesetsShippingMethodShowHideTaxIncludeField( el ) {
        var form = $( el ).closest( 'form' );
        var taxIncludeField = $( '#woocommerce_pleb_rulesets_method_prices_include_tax', form ).closest( 'tr' );
        if ( 'none' === $( el ).val() || '' === $( el ).val() ) {
            taxIncludeField.hide();
        } else {
            taxIncludeField.show();
        }
    }

    $( document.body ).on( 'change', '#woocommerce_pleb_rulesets_method_tax_status', function() {
        plebRulesetsShippingMethodShowHideTaxIncludeField( this );
    });

    $( '#woocommerce_pleb_rulesets_method_tax_status' ).trigger( 'change' );
    $( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
        if ( 'wc-modal-shipping-method-settings' === target ) {
            plebRulesetsShippingMethodShowHideTaxIncludeField( $( '#wc-backbone-modal-dialog #woocommerce_pleb_rulesets_method_tax_status', evt.currentTarget ) );
        }
    } );


    $('#pleb_rulesets').sortable({
        opacity: 0.7,
        revert: false,
        cursor: 'move',
        handle: '.hndle',
        items: '> .pleb_ruleset',
        update: function(event, ui){
            $('#pleb_rulesets > .pleb_ruleset').each(function(index, elem){
                $(elem).find('input[name^="woocommerce_pleb_rulesets_method_rulesets"][name$="[order]"]').val(index+1);
            });
        }
    });

    $(document.body).on('click', '#pleb_ruleset_add_button', function(e){
        e.preventDefault();
        const $button = $(this);
        const field_key = $button.attr('data-field_key');
        if(!field_key){
            alert("Field key is missing");
            return false;
        }
        $.ajax({
            url: pleb.ajax_url,
            method: 'post',
            data: {
                action: 'pleb_ruleset_template',
                field_key: field_key
            },
            success: function(response){
                $('#pleb_no_ruleset_notice').hide();
                $('#pleb_rulesets').append(response);

                //$('#pleb_rulesets').find('.pleb_ruleset:last').find('.pleb_edit_ruleset_button').trigger('click');
            }
        });
    });

    $(document.body).on('click', '.pleb_ruleset_add_rule_button', function(e){
        e.preventDefault();
        const $button = $(this);
        const field_key = $button.attr('data-field_key');
        const $ruleset = $button.parents('.pleb_ruleset');
        if(!field_key){
            alert("Field key is missing");
            return false;
        }
        $.ajax({
            url: pleb.ajax_url,
            method: 'post',
            data: {
                action: 'pleb_ruleset_rule_template',
                field_key: field_key
            },
            success: function(response){
                $ruleset.find('.ruleset_rules .pleb_no_ruleset_rule_notice').hide();
                $ruleset.find('.ruleset_rules').append(response);

                //$('#pleb_rulesets').find('.pleb_ruleset:last').find('.pleb_edit_ruleset_button').trigger('click');
            }
        });
    });

    $(document.body).on('click', '.pleb_edit_ruleset_button', function(e){
        e.preventDefault();
        const $button = $(this);
        const $wrapper = $button.parents('.pleb_title_input_wrapper');

        $wrapper.find('h2').toggle();
        $wrapper.find('.pleb_input_wrapper').toggle();
    });

});