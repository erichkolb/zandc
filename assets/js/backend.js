jQuery(document).ready(function ($) {

    "use strict";

    // Posttype metas dependency
    var zanDcOptionsDepConfig = {
        1 : {
            'selector' : '#section-zandc_search_btn_text', // Selectors dependency on "dep"
            'dep' : '#zandc_show_search_btn',
            'compare' : '=',
            'value' : 'yes',
            'indent' : true
        },
        2 : {
            'selector' : '#section-zandc_whois_page', // Selectors dependency on "dep"
            'dep' : '#zandc_show_whois_in',
            'compare' : '=',
            'value' : 'custom_page',
            'indent' : true
        },
        3 : {
            'selector' : '#section-zandc_whois_title', // Selectors dependency on "dep"
            'dep' : '#zandc_show_whois_in',
            'compare' : '<>',
            'value' : 'disable',
            'indent' : true
        },
        4 : {
            'selector' : '#section-zandc_tld_exts_integrated_with_wc_products, #section-zandc_wc_integration_btn_text', // Selectors dependency on "dep"
            'dep' : '#zandc_integrate_with',
            'compare' : '=',
            'value' : 'woocommerce',
            'indent' : true
        },
        5 : {
            'selector' : '#section-zandc_integration_link, #section-zandc_integration_link_text', // Selectors dependency on "dep"
            'dep' : '#zandc_integrate_with',
            'compare' : 'in',
            'value' : 'whmcs,link',
            'indent' : true
        },
        6 : {
            'selector' : '#section-zandc_recaptcha_site_key, #section-zandc_recaptcha_secret_key', // Selectors dependency on "dep"
            'dep' : '#zandc_enable_recaptcha',
            'compare' : '=',
            'value' : 'yes',
            'indent' : true
        },
        7 : {
            'selector' : '#section-zandc_try_faster_checking, #section-zandc_try_country_detection', // Selectors dependency on "dep"
            'dep' : '#zandc_enable_instant_domain_search',
            'compare' : '=',
            'value' : 'yes',
            'indent' : true
        }

    };

    function zan_dc_page_update_display_dep_metas() {

        $.each(zanDcOptionsDepConfig, function (i, val) {

            // For repeatable field
            if ($(val['selector']).is('.cmb-repeatable-group')) {
                $(val['selector']).closest('.cmb-repeat-group-wrap').addClass((val['selector'] + '_wrap').replace('#', '').replace('.', ''));
                val['selector'] = (val['selector'] + '_wrap').replace('#', '.');
            }

            var compare = val['compare'] == '' ? '=' : val['compare'];
            var indent  = false;
            var indent2 = false;
            var indent3 = false;
            if (val.hasOwnProperty('indent')) {
                indent = val['indent'];
            }
            if (val.hasOwnProperty('indent2')) {
                indent2 = val['indent2'];
            }
            if (val.hasOwnProperty('indent3')) {
                indent3 = val['indent3'];
            }

            if (indent) {
                $(val['selector']).addClass('zan-dep zan-dep-lv-1').css({'padding-left' : '7%'});
            }
            if (indent2) {
                $(val['selector']).addClass('zan-dep zan-dep-lv-2').css({'padding-left' : '12%'});
            }
            if (indent3) {
                $(val['selector']).addClass('zan-dep zan-dep-lv-3').css({'padding-left' : '17%'});
            }

            switch (compare) {

                case '=':
                    if ($(val['dep']).val() != val['value']) {
                        $(val['selector']).css({'display' : 'none'});
                    }
                    else {
                        $(val['selector']).css({'display' : ''});
                    }
                    break;

                case 'checked':
                    if ($(val['dep']).is(':checked') && $(val['dep']).val() == val['value']) {
                        $(val['selector']).css({'display' : ''});
                    }
                    else {
                        $(val['selector']).css({'display' : 'none'});
                    }
                    break;

                case '!=':
                case '<>':
                    if ($(val['dep']).val() == val['value']) {
                        $(val['selector']).css({'display' : 'none'});
                    }
                    else {
                        $(val['selector']).css({'display' : ''});
                    }
                    break;

                case 'in':

                    if (val['value'].indexOf($(val['dep']).val() + ',') >= 0 || val['value'].indexOf(', ' + $(val['dep']).val()) >= 0 || val['value'].indexOf(',' + $(val['dep']).val()) >= 0) {
                        $(val['selector']).css({'display' : ''});
                    }
                    else {
                        $(val['selector']).css({'display' : 'none'});
                    }
                    break;
            }

            // Check dependency on parrent
            if ($(val['dep']).closest('.rwmb-field, *[class^="cmb-type-"], *[class*=" cmb-type-"]').is(':hidden')) {
                $(val['selector']).css({'display' : 'none'});
            }
            else {
            }
        });

    }

    function zan_dc_init_dep_metas() {
        zan_dc_page_update_display_dep_metas();
        $.each(zanDcOptionsDepConfig, function (i, val) {
            $(document).on('change', val['dep'], function () {
                zan_dc_page_update_display_dep_metas();
            });
        });
    }

    zan_dc_init_dep_metas();

});