jQuery(function ( $ ) {

    //console.log(pleb);

    const shipping_method_fields_prefix = pleb.shipping_method.plugin_id + pleb.shipping_method.method_id + '_';

    function plebRulesetsShippingMethodFielChanged(el){
        const elName = $(el).attr('id').replace(shipping_method_fields_prefix, '');
        const elValue = $(el).val();
        plebRulesetsShippingMethodShowHideField(elName, elValue);
    }

    function plebRulesetsShippingMethodFielTr(elName){
        return $('#'+shipping_method_fields_prefix + elName).closest('tr');
    }

    function plebRulesetsShippingMethodShowHideField(elName, elValue){
        console.log(elName+' : '+elValue);
        
        if(elName=='tax_status'){
            var $tr = plebRulesetsShippingMethodFielTr('prices_include_tax');
            if ( 'none' === elValue || '' === elValue ) {
                $tr.hide();
            } else {
                $tr.show();
            }
        }

        if(elName=='rulesets_matching_mode'){
            var $tr = plebRulesetsShippingMethodFielTr('replace_method_title');
            if ( 'many_grouped' === elValue ) {
                $tr.hide();
            } else {
                $tr.show();
            }
        }

    }

    $(document.body).on('change', '[id^='+shipping_method_fields_prefix+']', function () {
        plebRulesetsShippingMethodFielChanged(this);
    });

    $('[id^='+shipping_method_fields_prefix+']').trigger('change');
    // $(document.body).on('wc_backbone_modal_loaded', function ( evt, target ) {
    //     if ( 'wc-modal-shipping-method-settings' === target ) {
    //         plebRulesetsShippingMethodShowHideTaxIncludeField($('#wc-backbone-modal-dialog #' + shipping_method_fields_prefix + 'tax_status', evt.currentTarget));
    //     }
    // });


    $('#pleb_rulesets').sortable({
        opacity: 0.7,
        revert: false,
        cursor: 'move',
        handle: '.hndle',
        items: '> .pleb_ruleset',
        placeholder: "pleb_ruleset_placeholder",
        start: function(e, ui){
            ui.placeholder.outerHeight(ui.item.outerHeight());
        },
        update: function (event, ui) {
            $('#pleb_rulesets > .pleb_ruleset').each(function (index, elem) {
                $(elem).find('input[name^="' + shipping_method_fields_prefix + 'rulesets"][name$="[order]"]').val(index + 1);
            });
        }
    });

    $(document.body).on('click', '#pleb_ruleset_add_button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const field_key = $button.attr('data-field_key');
        if (!field_key) {
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
            success: function (response) {
                $('#pleb_rulesets').append(response);
                showHideNoRulesetNotice();

                //$('#pleb_rulesets').find('.pleb_ruleset:last').find('.pleb_edit_ruleset_button').trigger('click');
            }
        });
    });

    $(document.body).on('click', '#pleb_ruleset_add_default_button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const field_key = $button.attr('data-field_key');
        if (!field_key) {
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
            success: function (response) {

                $('#pleb_ruleset_default_wrapper').html(response);

                showHideNoRulesetNotice();
            }
        });

        $button.hide();
    });

    $(document.body).on('click', '.pleb_ruleset_add_rule_button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const field_key = $button.attr('data-field_key');
        const $ruleset = $button.parents('.pleb_ruleset');
        if (!field_key) {
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
            success: function (response) {
                $ruleset.find('.pleb_no_ruleset_rule_notice').hide();
                $ruleset.find('.ruleset_rules').show();
                $ruleset.find('.ruleset_rules').append(response);

                //$('#pleb_rulesets').find('.pleb_ruleset:last').find('.pleb_edit_ruleset_button').trigger('click');
            }
        });
    });

    $(document.body).on('click', '.pleb_edit_ruleset_button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const $wrapper = $button.parents('.pleb_title_input_wrapper');

        $wrapper.find('h2').toggle();
        $wrapper.find('.pleb_input_wrapper').toggle();
        $button.find('.button_dynamic_action').toggle();

        const titleVal = $wrapper.find('h2 > span').text().trim();
        const inputVal = $wrapper.find('.pleb_input_wrapper input').val();
        if($wrapper.find('h2').is(':visible')){
            $wrapper.find('h2 > span').text(inputVal);
        }else{
            $wrapper.find('.pleb_input_wrapper input').val(titleVal);
        }
    });

    $(document.body).on('click', '.pleb_duplicate_ruleset_button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const ruleset_id = $button.attr('data-ruleset_id');
        const $ruleset = $button.parents('.pleb_ruleset');

        $('<div id="duplicate_loading" class="notice notice-info inline text-center notice-alt" style="margin-top:0;margin-bottom:15px;"><p><span class="spinner is-active" style="float:none;margin:0;"></span> '+pleb.translations.loading+'</p></div>').insertAfter($ruleset);

        $.ajax({
            url: pleb.ajax_url,
            method: 'post',
            data: {
                action: 'pleb_ruleset_generate_id'
            },
            success: function (new_ruleset_id) {
                const rulesetHtml = $ruleset[0].outerHTML;
                const newRulesetHtml = rulesetHtml.replaceAll(ruleset_id, new_ruleset_id);
                //alert('replace '+ruleset_id+' by '+new_ruleset_id);
                $('#duplicate_loading').replaceWith(newRulesetHtml);
            }
        });

        

    });

    function showHideNoRulesetNotice()
    {
        if ( $('#pleb_rulesets .pleb_ruleset').length == 0 && $('#pleb_ruleset_default_wrapper .pleb_ruleset').length == 0 ) {
            $('#pleb_no_ruleset_notice').show();
        } else {
            $('#pleb_no_ruleset_notice').hide();
        }

        console.log($('#pleb_ruleset_default_wrapper .pleb_ruleset').length);
        if ( $('#pleb_ruleset_default_wrapper .pleb_ruleset').length == 0 ) {
            $('#pleb_no_ruleset_default_notice').show();
        } else {
            $('#pleb_no_ruleset_default_notice').hide();
        }
    }

    $(document.body).on('click', '.pleb_ruleset_delete[data-ruleset_id][data-confirm]', function (e) {
        e.preventDefault();
        const $button = $(this);
        const $ruleset = $button.parents('.pleb_ruleset');

        if ( confirm($button.attr('data-confirm')) ) {
            $ruleset.slideUp(300, function () {
                $ruleset.remove();

                showHideNoRulesetNotice();
            });
        }

    });

    $(document.body).on('click', '.pleb_ruleset_default_delete[data-confirm]', function (e) {
        e.preventDefault();
        const $button = $(this);
        const $ruleset = $button.parents('.pleb_ruleset');

        if ( confirm($button.attr('data-confirm')) ) {
            $ruleset.slideUp(300, function () {
                $ruleset.remove();

                $('#pleb_ruleset_add_default_button').show();

                showHideNoRulesetNotice();
            });
        }

    });

    $(document.body).on('click', '.pleb_rule_delete[data-rule_id][data-confirm]', function (e) {
        e.preventDefault();
        const $button = $(this);
        const $rule = $button.parents('.pleb_rule');
        const $ruleset = $rule.parents('.pleb_ruleset');

        if ( confirm($button.attr('data-confirm')) ) {
            $rule.slideUp(300, function () {
                $rule.remove();

                if ( $ruleset.find('.pleb_rule').length == 0 ) {
                    $ruleset.find('.pleb_no_ruleset_rule_notice').show();
                    $ruleset.find('.ruleset_rules').hide();
                } else {
                    $ruleset.find('.ruleset_rules').show();
                }
            });
        }

    });

    $(document.body).on('change', 'select.rule_condition_id', function (e) {
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
            success: function (response) {

                $rule.replaceWith(response);

                // class dejà absente
                //$rule.removeClass('loading');
                $rules.css('opacity', 1);
            }
        });



    });


    $(document).on('click', '.pleb_nav_tabs a.nav-tab', function(e){
        e.preventDefault();

        const $link = $(this);
        const $navTabs = $link.parents('.pleb_nav_tabs');
        const $otherLinks = $navTabs.find('a.nav-tab').not($link);
        const $tabsContents = $navTabs.find('.tab_content');

        $link.addClass('nav-tab-active');
        $otherLinks.removeClass('nav-tab-active');
        $tabsContents.hide();
        $navTabs.find('.tab_content'+$link.attr('href')).show();

    });


});