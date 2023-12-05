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
                $('#pleb_rulesets').append(response);
                showHideNoRulesetNotice();

                //$('#pleb_rulesets').find('.pleb_ruleset:last').find('.pleb_edit_ruleset_button').trigger('click');
            }
        });
    });

    $(document.body).on('click', '#pleb_ruleset_add_default_button', function(e){
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
                action: 'pleb_ruleset_default_template',
                field_key: field_key
            },
            success: function(response){

                $('#pleb_ruleset_default_wrapper').html(response);

                showHideNoRulesetNotice();
            }
        });

        $button.hide();
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
                $ruleset.find('.pleb_no_ruleset_rule_notice').hide();
                $ruleset.find('.ruleset_rules').show();
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
        $button.find('.button_dynamic_action').toggle();
    });

    function showHideNoRulesetNotice()
    {
        if( $('#pleb_rulesets .pleb_ruleset').length==0 && $('#pleb_ruleset_default_wrapper .pleb_ruleset').length==0 ){
            $('#pleb_no_ruleset_notice').show();
        }else{
            $('#pleb_no_ruleset_notice').hide();
        }
    }

    $(document.body).on('click', '.pleb_ruleset_delete[data-ruleset_id][data-confirm]', function(e){
        e.preventDefault();
        const $button = $(this);
        const $ruleset = $button.parents('.pleb_ruleset');

        if( confirm( $button.attr('data-confirm') ) ){
            $ruleset.slideUp(300, function(){
                $ruleset.remove();

                showHideNoRulesetNotice();
            });
        }

    });

    $(document.body).on('click', '.pleb_ruleset_default_delete[data-confirm]', function(e){
        e.preventDefault();
        const $button = $(this);
        const $ruleset = $button.parents('.pleb_ruleset');

        if( confirm( $button.attr('data-confirm') ) ){
            $ruleset.slideUp(300, function(){
                $ruleset.remove();

                $('#pleb_ruleset_add_default_button').show();

                showHideNoRulesetNotice();
            });
        }

    });

    $(document.body).on('click', '.pleb_rule_delete[data-rule_id][data-confirm]', function(e){
        e.preventDefault();
        const $button = $(this);
        const $rule = $button.parents('.pleb_rule');
        const $ruleset = $rule.parents('.pleb_ruleset');

        if( confirm( $button.attr('data-confirm') ) ){
            $rule.slideUp(300, function(){
                $rule.remove();

                if( $ruleset.find('.pleb_rule').length==0 ){
                    $ruleset.find('.pleb_no_ruleset_rule_notice').show();
                    $ruleset.find('.ruleset_rules').hide();
                }else{
                    $ruleset.find('.ruleset_rules').show();
                }
            });
        }

    });

    $(document.body).on('change', 'select.rule_condition_id', function(e){
        const $select = $(this);
        const $rule = $select.parents('.pleb_rule');
        const rule_id = $rule.attr('data-rule_id');
        const field_key = $rule.attr('data-field_key');
        const condition_comparator = $rule.find('[name$="][condition_comparator]"]').val();
        const condition_value = $rule.find('[name$="][condition_value]"]').val();
        const $rules = $rule.parents('.ruleset_rules');

        $rule.addClass('loading');
        $rules.css('opacity', 0.5);

        $.ajax({
            url: pleb.ajax_url,
            method: 'post',
            data: {
                action: 'pleb_rule_template',
                rule_id: rule_id,
                field_key: field_key,
                condition_id: $select.val(),
                condition_comparator: condition_comparator,
                condition_value: condition_value
            },
            success: function(response){

                $rule.replaceWith(response);

                // class dejà absente 
                //$rule.removeClass('loading');
                $rules.css('opacity', 1);
            }
        });



    });

});