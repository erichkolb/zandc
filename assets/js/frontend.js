jQuery(document).ready(function ($) {

    "use strict";

    // Domain checker style 4 with TLD exts dropdown
    $('.zan-dc-style_4 .zan-dc-tld-exts-select').each(function () {
        var $this    = $(this);
        var thisWrap = $this.closest('.dropdown-wrap');
        if (!thisWrap.find('.zan-dc-price-wrap').length) {
            thisWrap.append('<div class="zan-dc-price-wrap"></div>');
        }
        var price = $('option:selected', $this).attr('data-price');

        if ($.trim(price) != '') {
            thisWrap.find('.zan-dc-price-wrap').html('<span class="zan-dc-price">' + price + '</span>');
        }
        else {
            thisWrap.find('.zan-dc-price-wrap').remove();
        }

    });

    $(document).on('change', '.zan-dc-style_4 .zan-dc-tld-exts-select', function (e) {
        var $this    = $(this);
        var thisWrap = $this.closest('.dropdown-wrap');
        if (!thisWrap.find('.zan-dc-price-wrap').length) {
            thisWrap.append('<div class="zan-dc-price-wrap"></div>');
        }
        var price = $('option:selected', $this).attr('data-price');
        if ($.trim(price) != '') {
            thisWrap.find('.zan-dc-price-wrap').html('<span class="zan-dc-price">' + price + '</span>');
        }
        else {
            thisWrap.find('.zan-dc-price-wrap').remove();
        }
    });

    $(document).on('submit', 'form[name="zan_dc_form"]', function (e) {

        var $this               = $(this);
        var thisWrap            = $this.closest('.zan-dc-inner');
        var isNoDiv             = thisWrap.closest('.zan-dc-wrap').attr('data-nodiv') == 'yes';
        var nonce               = $this.find('input[name="zan_dc_nonce"]').val();
        var domain_name         = $this.find('input[name="zandomainchecker"]').val();
        var tld_ext             = '';
        var recaptcha_response  = '';
        var err                 = false;
        var is_recaptcha_enable = false;

        if ($this.find('[name="zan_dc_tld_ext"]').length) {
            tld_ext = $this.find('[name="zan_dc_tld_ext"]').val();
        }

        if ($this.find('[name="g-recaptcha-response"]').length) {
            recaptcha_response  = $this.find('[name="g-recaptcha-response"]').val();
            is_recaptcha_enable = true;
        }

        if ($this.is('.checking')) {
            return false;
        }

        if (is_recaptcha_enable && $.trim(recaptcha_response) == '') {
            $this.find('.g-recaptcha').addClass('zan-dc-err');
            err = true;
        }
        else {
            $this.find('.g-recaptcha').removeClass('zan-dc-err');
        }

        if ($.trim(domain_name) == '') {
            $this.find('input[name="zandomainchecker"]').addClass('zan-dc-err');
            err = true;
        }
        else {
            $this.find('input[name="zandomainchecker"]').removeClass('zan-dc-err');
        }

        if (err) {
            return false;
        }

        $this.addClass('checking');
        thisWrap.find('.zan-dc-results-wrap .zan-dc-results').removeClass('zan-dc-check-err-nonce zan-dc-check-err-recaptcha zan-dc-check-err-domain_empty').html('');

        var data = {
            action : 'zan_dc_domain_check_result', // via ajax
            nonce : nonce,
            domain_name : domain_name,
            tld_ext : tld_ext, // For style_4
            recaptcha_response : recaptcha_response,
            is_no_div : isNoDiv ? 'yes' : 'no'
        };

        $.post(zandc['ajaxurl'], data, function (response) {

            if (!(thisWrap.find('.zan-dc-results-wrap .zan-dc-results').length)) {
                if (isNoDiv) {
                    thisWrap.append('<div class="zan-dc-results-wrap"><div class="zan-dc-results"></div></div>');
                }
                else {
                    thisWrap.append('<span class="zan-dc-results-wrap"><span class="zan-dc-results"></span></span>');
                }
            }

            if ($.trim(response['err']) != '') {
                thisWrap.find('.zan-dc-results-wrap .zan-dc-results').addClass('zan-dc-check-err-' + response['err']);
            }

            thisWrap.find('.zan-dc-results-wrap .zan-dc-results').html(response['html'] + response['message'] + '<a href="#" class="zan-dc-close-result">X</a>');
            $this.find('input[name="zandomainchecker"]').val(response['domain']);
            $this.removeClass('checking');
            if (is_recaptcha_enable) {
                grecaptcha.reset();
            }

        });

        e.preventDefault();
        return false;
    });

    // Whois popup
    $(document).on('click', '.zan-dc-whois-popup', function (e) {

        var $this       = $(this);
        var thisDcInner = $this.closest('.zan-dc-inner');
        var nonce       = thisDcInner.find('[name="zan_dc_nonce"]').val();
        var domain_name = $this.attr('data-domain');

        if (!$('body .zan-dc-whois-pupup-wrap').length) {
            $('body').append('<div class="zan-dc-whois-popup-wrap zan-dc-popup-wrap"><div class="zan-dc-whois-popup-inner zan-dc-popup-inner"></div><a href="#" class="zan-dc-close-popup zan-dc-close-whois">X</a></div>');
        }

        var data = {
            action : 'zan_dc_check_whois_via_ajax', // via ajax
            nonce : nonce,
            domain_name : domain_name
        };

        $.post(zandc['ajaxurl'], data, function (response) {

            $('.zan-dc-whois-popup-wrap .zan-dc-whois-popup-inner').html(response['html']);

        });

        e.preventDefault();
    });

    // Submit integration WHMCS form
    $(document).on('click', '.zan-dc-integrate-whmcs', function (e) {

        var $this       = $(this);
        var domain_name = $this.attr('data-domain');

        if ($.trim(domain_name) == '') {
            return false;
        }

        if ($('.zan-dc-whmsc-integration-form').length) {
            $('.zan-dc-whmsc-integration-form .zan-dc-whmcs-domain-name-hidden').val(domain_name);
            $('.zan-dc-whmsc-integration-form .zan-dc-domainsregperiod-hidden').attr('name', 'domainsregperiod[' + domain_name + ']');
            $('.zan-dc-whmsc-integration-form').submit();
        }

        e.preventDefault();
    });

    // Close checking result
    $(document).on('click', '.zan-dc-close-result', function (e) {

        var $this          = $(this);
        var thisResultWrap = $this.closest('.zan-dc-results-wrap');
        if (thisResultWrap.length) {
            thisResultWrap.remove();
        }

        e.preventDefault();
    });

    // Close popup (remove)
    $(document).on('click', '.zan-dc-close-popup', function (e) {

        var $this = $(this);
        $this.closest('.zan-dc-popup-wrap').remove();

        e.preventDefault();
    });

    // Close popup when click outside the popup content
    $(document).on('click', '.zan-dc-popup-wrap', function (e) {

        var $this = $(this);

        var popupContent = $this.find('.zan-dc-pupup-content');

        if (!popupContent.is(e.target) // if the target of the click isn't the container...
            && popupContent.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $this.remove();
        }

        e.preventDefault();
    });

    var zandcAjaxRequest            = null;
    var zandcAjaxRequestSingleCheck = null; // Instant request check a domain with ext
    var zandcAjaxRequestSldCheck    = null;

    $('.zan-dc-wrap.is-instant-search .zan-dc-input').each(function () {
        var $this = $(this);
        // callback: The callback function
        // wait: The number of milliseconds to wait after the the last key press before firing the callback
        // highlight: Highlights the element when it receives focus
        // allowSubmit: Allows a non-multiline element to be submitted (enter key) regardless of captureLength
        // captureLength: Minimum # of characters necessary to fire the callback
        $this.typeWatch({
            callback : function (value) {
                var thisWrap      = $this.closest('.zan-dc-wrap');
                var isNoDiv       = thisWrap.attr('data-nodiv') == 'yes';
                var thisWrapInner = $this.closest('.zan-dc-inner');
                var search_key    = $this.val().toLocaleLowerCase();

                // Get SLD ext
                // alert('http://subdomain.example.com'.match(/[^./]+\.[^./]+$/)[0]);
                // alert('http://example.com'.match(/[^./]+\.[^./]+$/)[0]);

                if ($.trim(search_key) == '' || $.trim(search_key).length <= 1) {
                    thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').remove();
                    return false;
                }

                if (thisWrapInner.is('.is-instant-searching')) {
                    // Do nothing
                }

                search_key = $.trim(search_key);
                if (search_key.substr(0, 4) == 'www.') {
                    search_key = search_key.replace(/^(www\.)/, "");
                }

                // Don't check anything if no change. "is-trigger-keydown" class for style_4 TLD select change
                if ($this.attr('data-input-val') != search_key || $this.is('.is-trigger-keydown')) {
                    $this.attr('data-input-val', search_key);
                    $this.removeClass('is-trigger-keydown');
                }
                else {
                    return false;
                }

                var instant_ajax_url    = zandc['instant_domain_search_url'];
                var search_key_split    = search_key.split('.');
                var domain_tld          = 'com';
                var whois_link_html     = '';
                var transfer_link_html  = '';
                var integrate_link_html = '';

                if (zandc['all_available_exts'] != 'undefined' && zandc['all_available_exts'] != null && zandc['all_available_exts'].length != 0) {
                    domain_tld = zandc['all_available_exts'][0];
                }

                if (thisWrap.is('.zan-dc-style_4')) {
                    domain_tld = thisWrap.find('.zan-dc-tld-exts-select').val();
                }

                // TLD did not input
                if (typeof search_key_split[1] === 'undefined' || typeof search_key_split[1] === false) {
                    instant_ajax_url += search_key_split[0] + '?&partTld=' + domain_tld;
                    whois_link_html = zandc['whois_link_html'].replace('{domain}', search_key_split[0] + '.' + domain_tld);
                    if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                        transfer_link_html = zandc['transfer_link_html'].replace('{domain}', search_key_split[0] + '.' + domain_tld);
                        transfer_link_html = transfer_link_html.replace('{domain_not_ext}', search_key_split[0]).replace('{ext}', domain_tld);
                    }
                }
                else {
                    instant_ajax_url += search_key_split[0] + '?&partTld=' + search_key_split[1];
                    whois_link_html = zandc['whois_link_html'].replace('{domain}', search_key_split[0] + '.' + search_key_split[1]);
                    if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                        transfer_link_html = zandc['transfer_link_html'].replace('{domain}', search_key_split[0] + '.' + search_key_split[1]);
                        transfer_link_html = transfer_link_html.replace('{domain_not_ext}', search_key_split[0]).replace('{ext}', search_key_split[1]);
                    }
                    domain_tld = search_key_split[1];
                }

                // If is try faster checking on
                if (thisWrap.is('.try-faster-checking') && !thisWrap.is('.zan-dc-style_4')) {
                    // TLD did not input
                    if (typeof search_key_split[1] === 'undefined' || typeof search_key_split[1] === false) {
                        instant_ajax_url = zandc['instant_domain_search_url'] + search_key_split[0] + '?&tldTags=popular';
                        // If county detection is turn on
                        if (thisWrap.is('.try-country_detection')) {
                            var country_code = thisWrap.attr('data-country-code');
                            instant_ajax_url += '&country=' + country_code;
                        }
                    }
                    else {

                    }
                }

                thisWrapInner.addClass('is-instant-searching');

                if (!(thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').length)) {
                    if (isNoDiv) {
                        thisWrapInner.append('<span class="zan-dc-results-wrap"><span class="zan-dc-results"></span></span>');
                    }
                    else {
                        thisWrapInner.append('<div class="zan-dc-results-wrap"><div class="zan-dc-results"></div></div>');
                    }

                }

                thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').attr('data-latest-domain', search_key_split[0] + '.' + domain_tld);

                instant_ajax_url = zandc['yahoo_url'] + '?q=' + encodeURIComponent('select * from html where url="' + instant_ajax_url + '"') + '&format=json&callback=?';

                zandcAjaxRequest = $.ajax({
                    //url : instant_ajax_url + '&callback=zandc_json_callback',
                    url : instant_ajax_url,
                    type : 'GET',
                    cache : true,
                    dataType : 'json', // jsonp, html
                    //jsonpCallback: 'zandc_json_callback',
                    //jsonp: 'callback',
                    // contentType: 'text/plain',
                    // xhrFields: {
                    //     // The 'xhrFields' property sets additional fields on the XMLHttpRequest.
                    //     // This can be used to set the 'withCredentials' property.
                    //     // Set the value to 'true' if you'd like to pass cookies to the server.
                    //     // If this is enabled, your server must respond with the header
                    //     // 'Access-Control-Allow-Credentials: true'.
                    //     withCredentials: false
                    // },
                    //jsonp: 'callback',
                    crossDomain : true,
                    statusCode : {
                        429 : function (response) {
                            console.log('Please try again');
                        }
                    },
                    beforeSend : function () {
                        if (zandcAjaxRequest != null) {
                            // console.log('Abort previous request');
                            zandcAjaxRequest.abort();
                        }
                        if (zandcAjaxRequestSingleCheck != null) {
                            // console.log('Abort previous single check request');
                            zandcAjaxRequestSingleCheck.abort();
                        }
                    },
                    success : function (response) {
                        //var search_results = $(response.responseText).text();
                        var search_results = response['query']['results']['body'];
                        var result_html    = '';

                        if ($.trim(search_results) != '') {
                            if ($.type(search_results) == 'object') {
                                search_results = search_results['content'];
                            }
                            var results_txt_split = search_results.split("\n");

                            var first_search_results_json = JSON.parse(results_txt_split[0]);

                            if (first_search_results_json['label'] == 'undefined' || first_search_results_json['label'] == undefined) {
                                return false;
                            }

                            //console.log(first_search_results_json);
                            var result_class   = 'result-item';
                            var result_message = '';
                            if (first_search_results_json['isRegistered']) {
                                result_class += ' is-registered';
                                result_message = zandc['not_avai_result_message'].replace('{domain}', '<span>' + first_search_results_json['label'] + '.' + first_search_results_json['tld'] + '</span>');
                            }
                            else {
                                result_class += ' not-registered';
                                result_message     = zandc['avai_result_message'].replace('{domain}', '<span>' + first_search_results_json['label'] + '.' + first_search_results_json['tld'] + '</span>');
                                whois_link_html    = '';
                                transfer_link_html = '';
                                if (zandc['integrate_with'] == 'woocommerce') {
                                    if (typeof zandc['wc_integrate_link_args'][first_search_results_json['tld']] != 'undefined' && typeof typeof zandc['wc_integrate_link_args'][first_search_results_json['tld']] != false) {
                                        integrate_link_html = zandc['wc_integrate_link_args'][first_search_results_json['tld']].replace('{domain}', first_search_results_json['label'] + '.' + first_search_results_json['tld']);
                                    }
                                }
                                else {
                                    integrate_link_html = zandc['integrate_link_html'].replace('{domain}', first_search_results_json['label'] + '.' + first_search_results_json['tld']);
                                }
                            }

                            var integration_form_html = ''; // WHMCS integration form
                            if (zandc['integrate_with'] == 'whmcs') {
                                integration_form_html = zandc['whmcs_form'];
                            }

                            result_html += '<p class="' + result_class + '">' + result_message + transfer_link_html + whois_link_html + integrate_link_html + '</p>';
                            result_html += '<a href="#" class="zan-dc-close-result">X</a>';

                            // For nodiv
                            if (thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').is('span')) {
                                result_html = result_html.replace(/<p /g, '<span ').replace(/p>/g, 'span>');
                            }

                            var country_code = '';
                            if (thisWrap.is('.try-faster-checking') && !thisWrap.is('.zan-dc-style_4')) {
                                var tld_args_to_check = zandc['all_available_exts'];
                                country_code          = '';
                                if (thisWrap.is('.try-country_detection')) {
                                    country_code = thisWrap.attr('data-country-code');
                                    tld_args_to_check.push(country_code);
                                }
                                zandc_get_faster_checking_results_html(search_results, search_key_split[0], zandc['all_available_exts'], country_code, thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results'));
                                thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').prepend(integration_form_html);
                            }
                            else {

                                if (first_search_results_json['label'] + '.' + first_search_results_json['tld'] == thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').attr('data-latest-domain')) {
                                    thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').html(integration_form_html + result_html);
                                    // Check for other results
                                    if (zandc['all_available_exts'] != 'undefined' && zandc['all_available_exts'] != null && zandc['all_available_exts'].length > 1 && !thisWrap.is('.zan-dc-style_4')) {
                                        var i;
                                        for (i = 0; i < zandc['all_available_exts'].length; i++) {
                                            if (i >= zandc['max_num_of_exts'] || i >= 5) { // Maximum 5 results for instant search
                                                break;
                                            }
                                            if (zandc['all_available_exts'][i] != first_search_results_json['tld']) {
                                                zandc_instant_check_domain(first_search_results_json['label'] + '.' + zandc['all_available_exts'][i], thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results'));
                                            }
                                        }
                                    }

                                    // If county detection is turn on
                                    if (thisWrap.is('.try-country_detection')) {
                                        country_code = thisWrap.attr('data-country-code');
                                        zandc_instant_check_domain(first_search_results_json['label'] + '.' + country_code, thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results'));
                                    }
                                }
                            }
                        }
                        else {
                            //thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').remove();
                        }

                        thisWrapInner.removeClass('is-instant-searching');

                    }
                });

                $.when(zandcAjaxRequest).done(function () {
                    zandc_check_all_slds(search_key, thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results'));
                });

            },
            wait : 1, // 1000 = 1 second
            highlight : true,
            allowSubmit : false,
            captureLength : 1
        });
    });

    function zandc_get_faster_checking_results_html(search_results, domain, tld_args_to_check, country_detect_code, returnTarget) {
        if ($.type(search_results) == 'object') {
            search_results = search_results['content'];
        }
        var results_txt_split        = search_results.split("\n");
        var results_html             = '';
        var tld_args_need_check      = tld_args_to_check; // Remaining tlds need check
        var tld_args_already_checked = Array();
        var i                        = 0;
        var count_result             = 0;
        var count_results            = results_txt_split.length;
        var is_country_code_checked  = $.trim(country_detect_code) == '';

        $.each(results_txt_split, function () {
            i++;
            if ($.trim(this) != '' && i < count_results && count_result < zandc['max_num_of_exts']) {
                var search_result_json = JSON.parse(this);
                if (search_result_json['label'] == domain && $.inArray(search_result_json['tld'], tld_args_to_check) > -1 && $.inArray(search_result_json['tld'], tld_args_already_checked) == -1) {

                    var result_class        = 'result-item';
                    var result_message      = '';
                    var integrate_link_html = '';
                    var whois_link_html     = '';
                    var transfer_link_html  = '';
                    if (search_result_json['isRegistered']) {
                        result_class += ' is-registered';
                        whois_link_html = zandc['whois_link_html'].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                        if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                            transfer_link_html = zandc['transfer_link_html'].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                            transfer_link_html = transfer_link_html.replace('{domain_not_ext}', search_result_json['label']).replace('{ext}', search_result_json['tld']);
                        }
                        result_message = zandc['not_avai_result_message'].replace('{domain}', '<span>' + search_result_json['label'] + '.' + search_result_json['tld'] + '</span>');
                    }
                    else {
                        result_class += ' not-registered';
                        result_message     = zandc['avai_result_message'].replace('{domain}', '<span>' + search_result_json['label'] + '.' + search_result_json['tld'] + '</span>');
                        whois_link_html    = '';
                        transfer_link_html = '';
                        if (zandc['integrate_with'] == 'woocommerce') {
                            if (typeof zandc['wc_integrate_link_args'][search_result_json['tld']] != 'undefined' && typeof typeof zandc['wc_integrate_link_args'][search_result_json['tld']] != false) {
                                integrate_link_html = zandc['wc_integrate_link_args'][search_result_json['tld']].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                            }
                        }
                        else {
                            integrate_link_html = zandc['integrate_link_html'].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                        }
                    }

                    //results_html += domain + '.' + search_result_json['tld'];
                    results_html += '<p class="' + result_class + '">' + result_message + transfer_link_html + whois_link_html + integrate_link_html + '</p>';

                    tld_args_need_check = zandc_remove_item_of_array(search_result_json['tld'], tld_args_need_check);
                    tld_args_already_checked.push(search_result_json['tld']);

                    if (search_result_json['tld'] == country_detect_code) {
                        is_country_code_checked = true;
                    }
                    count_result++;
                }
                else {
                    return results_html;
                }
            }
        });

        if (returnTarget.is('span')) {
            results_html = results_html.replace(/<p /g, '<span ').replace(/p>/g, 'span>');
        }

        returnTarget.html(results_html);

        if (zandc['check_popular_only'] != 'yes') {
            var max_of_results = zandc['max_num_of_exts'];
            if (is_country_code_checked && $.trim(country_detect_code) != '') {
                max_of_results++;
            }
            else {
                if (typeof country_detect_code != 'undefined' && typeof country_detect_code != false) {
                    if ($.trim(country_detect_code) != '') {
                        zandc_instant_check_domain(domain + '.' + country_detect_code, returnTarget);
                    }
                }
            }

            if (tld_args_need_check.length > 0 && count_result < max_of_results) {
                for (i = 0; i < tld_args_need_check.length; i++) {
                    if (count_result > max_of_results) {
                        break;
                    }
                    zandc_instant_check_domain(domain + '.' + tld_args_need_check[i], returnTarget);
                    count_result++
                }
            }
        }

        if (!returnTarget.find('.zan-dc-close-result').length) {
            returnTarget.append('<a href="#" class="zan-dc-close-result">X</a>');
        }

    }

    function zandc_remove_item_of_array(item_need_remove, args) {
        args = jQuery.grep(args, function (value) {
            return value != item_need_remove;
        });

        return args;
    }

    // Instant search for style_4 when choose new TLD ext
    $(document).on('change', '.zan-dc-wrap.is-instant-search .zan-dc-tld-exts-select', function () {
        var $this    = $(this);
        var thisWrap = $this.closest('.zan-dc-wrap');
        thisWrap.find('.zan-dc-input').addClass('is-trigger-keydown').trigger('keydown'); // Still not work
    });

    // "domain_name" include TLD ext
    function zandc_instant_check_domain(domain_name, returnTarget) {
        var domain_name_split   = domain_name.split('.');
        var instant_ajax_url    = zandc['instant_domain_search_url'];
        var domain_tld          = 'com';
        var whois_link_html     = '';
        var transfer_link_html  = '';
        var integrate_link_html = '';

        if (zandc['all_available_exts'] != 'undefined' && zandc['all_available_exts'] != null && zandc['all_available_exts'].length != 0) {
            domain_tld = zandc['all_available_exts'][0];
        }

        if (typeof domain_name_split[1] === 'undefined' || typeof domain_name_split[1] === false) {
            instant_ajax_url += domain_name_split[0] + '?&partTld=' + domain_tld;
            whois_link_html = zandc['whois_link_html'].replace('{domain}', domain_name_split[0] + '.' + domain_tld);
            if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                transfer_link_html = zandc['transfer_link_html'].replace('{domain}', domain_name_split[0] + '.' + domain_tld);
                transfer_link_html = transfer_link_html.replace('{domain_not_ext}', domain_name_split[0]).replace('{ext}', domain_tld);
            }
        }
        else {
            instant_ajax_url += domain_name_split[0] + '?&partTld=' + domain_name_split[1];
            whois_link_html = zandc['whois_link_html'].replace('{domain}', domain_name_split[0] + '.' + domain_name_split[1]);
            if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                transfer_link_html = zandc['transfer_link_html'].replace('{domain}', domain_name_split[0] + '.' + domain_name_split[1]);
                transfer_link_html = transfer_link_html.replace('{domain_not_ext}', domain_name_split[0]).replace('{ext}', domain_name_split[1]);
            }
            domain_tld = domain_name_split[1];
        }

        instant_ajax_url = zandc['yahoo_url'] + '?q=' + encodeURIComponent('select * from html where url="' + instant_ajax_url + '"') + '&format=json&callback=?';
        var result_html  = '';

        zandcAjaxRequestSingleCheck = $.ajax({
            url : instant_ajax_url,
            type : 'GET',
            cache : true,
            dataType : 'json',
            success : function (response) {

                //var search_results = $(response.responseText).text();
                var search_results = response['query']['results']['body'];

                if ($.trim(search_results) != '') {
                    if ($.type(search_results) == 'object') {
                        search_results = search_results['content'];
                    }
                    var results_txt_split = search_results.split("\n");

                    var first_search_results_json = JSON.parse(results_txt_split[0]);

                    if (first_search_results_json['label'] == 'undefined' || first_search_results_json['label'] == undefined) {
                        return false;
                    }

                    //console.log(first_search_results_json);
                    var result_class   = 'result-item';
                    var result_message = '';
                    if (first_search_results_json['isRegistered']) {
                        result_class += ' is-registered';
                        result_message = zandc['not_avai_result_message'].replace('{domain}', '<span>' + first_search_results_json['label'] + '.' + first_search_results_json['tld'] + '</span>');
                    }
                    else {
                        result_class += ' not-registered';
                        result_message     = zandc['avai_result_message'].replace('{domain}', '<span>' + first_search_results_json['label'] + '.' + first_search_results_json['tld'] + '</span>');
                        whois_link_html    = '';
                        transfer_link_html = '';
                        if (zandc['integrate_with'] == 'woocommerce') {
                            if (typeof zandc['wc_integrate_link_args'][first_search_results_json['tld']] != 'undefined' && typeof typeof zandc['wc_integrate_link_args'][first_search_results_json['tld']] != false) {
                                integrate_link_html = zandc['wc_integrate_link_args'][first_search_results_json['tld']].replace('{domain}', first_search_results_json['label'] + '.' + first_search_results_json['tld']);
                            }
                        }
                        else {
                            integrate_link_html = zandc['integrate_link_html'].replace('{domain}', first_search_results_json['label'] + '.' + first_search_results_json['tld']);
                        }
                    }

                    result_html += '<p class="' + result_class + '">' + result_message + transfer_link_html + whois_link_html + integrate_link_html + '</p>';

                    if (returnTarget.is('span')) {
                        result_html = result_html.replace(/<p /g, '<span ').replace(/p>/g, 'span>');
                    }

                    returnTarget.find('.zan-dc-close-result').remove();
                    returnTarget.append(result_html);
                    returnTarget.append('<a href="#" class="zan-dc-close-result">X</a>');
                }
                else {
                    //thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').remove();
                }

            }
        });
    }

    function zandc_check_all_slds(domain_name, returnTarget) {
        var all_avail_sld_exts         = zandc['all_avail_sld_exts'];
        var sld_whois_servers          = zandc['sld_whois_servers'];
        var domain_name_split          = domain_name.split('.');
        var instant_ajax_url           = zandc['instant_domain_search_url'];
        var instant_ajax_urls_need_run = Array();
        var sld_args_already_checked   = Array();
        var whois_link_html            = '';
        var transfer_link_html         = '';
        var integrate_link_html        = '';

        if (all_avail_sld_exts == 'undefined' || all_avail_sld_exts == null || all_avail_sld_exts.length == 0) {
            return;
        }

        // if SLD is input
        if (typeof domain_name_split[2] != 'undefined' && typeof domain_name_split[2] != false) {
            var sld_ext = domain_name_split[1] + '.' + domain_name_split[2];
            if ($.inArray(sld_ext, all_avail_sld_exts) > -1) {
                instant_ajax_url = zandc['instant_domain_search_url'] + domain_name_split[0] + '?&partTld=' + sld_ext;
                if (sld_whois_servers.hasOwnProperty(sld_ext)) {
                    whois_link_html = sld_whois_servers[sld_ext].replace('{domain}', domain_name_split[0] + '.' + sld_ext);
                    if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                        transfer_link_html = zandc['transfer_link_html'].replace('{domain}', domain_name_split[0] + '.' + sld_ext);
                        transfer_link_html = transfer_link_html.replace('{domain_not_ext}', domain_name_split[0]).replace('{ext}', sld_ext);
                    }
                }
                else {
                    whois_link_html    = '';
                    transfer_link_html = '';
                }
                instant_ajax_urls_need_run.push({
                    'instant_ajax_url' : instant_ajax_url,
                    'whois_link_html' : whois_link_html,
                    'transfer_link_html' : transfer_link_html,
                    'domain' : domain_name_split[0]
                });
            }
        }
        // SLD is not input
        else {
            var k = 0;
            for (k = 0; k < all_avail_sld_exts.length; ++k) {
                var sld_ext_k    = all_avail_sld_exts[k];
                instant_ajax_url = zandc['instant_domain_search_url'] + domain_name_split[0] + '?&partTld=' + sld_ext_k;
                if (sld_whois_servers.hasOwnProperty(sld_ext_k)) {
                    whois_link_html = sld_whois_servers[sld_ext_k].replace('{domain}', domain_name_split[0] + '.' + sld_ext_k);
                    if ($.trim(zandc['show_transfer_btn']) == 'yes') {
                        transfer_link_html = zandc['transfer_link_html'].replace('{domain}', domain_name_split[0] + '.' + sld_ext_k);
                        transfer_link_html = transfer_link_html.replace('{domain_not_ext}', domain_name_split[0]).replace('{ext}', sld_ext_k);
                    }
                }
                else {
                    whois_link_html = '';
                }
                instant_ajax_url = zandc['yahoo_url'] + '?q=' + encodeURIComponent('select * from html where url="' + instant_ajax_url + '"') + '&format=json&callback=?';
                instant_ajax_urls_need_run.push({
                    'instant_ajax_url' : instant_ajax_url,
                    'whois_link_html' : whois_link_html,
                    'transfer_link_html' : transfer_link_html,
                    'domain' : domain_name_split[0]
                });
            }
        }

        if (instant_ajax_urls_need_run) {
            $.each(instant_ajax_urls_need_run, function (index, instant_ajax_url_json) {
                $.when(zandcAjaxRequestSldCheck).done(function () {
                    setTimeout(function () {
                        // console.log('wait 200ms before check next');
                        zandcAjaxRequestSldCheck = $.ajax({
                            url : instant_ajax_url_json.instant_ajax_url,
                            //type : 'GET',
                            cache : true,
                            dataType : 'json',
                            beforeSend : function () {
                                if (zandcAjaxRequest != null) {
                                    //zandcAjaxRequestSldCheck.abort();
                                }
                                if (zandcAjaxRequestSldCheck != null) {
                                    //zandcAjaxRequestSldCheck.abort();
                                }
                            },
                            success : function (response) {

                                var result_html    = '';
                                //var search_results = response;
                                var search_results = response['query']['results']['body'];

                                if ($.trim(search_results) != '') {
                                    if ($.type(search_results) == 'object') {
                                        search_results = search_results['content'];
                                    }
                                    var results_txt_split = search_results.split("\n");
                                    var count_results     = results_txt_split.length;

                                    var first_search_results_json = JSON.parse(results_txt_split[0]);

                                    if (first_search_results_json['tld'] == 'undefined' || first_search_results_json['tld'] == undefined) {
                                        return false;
                                    }

                                    //console.log(first_search_results_json);
                                    var result_class   = 'result-item';
                                    var result_message = '';
                                    var i              = 0;
                                    $.each(results_txt_split, function () {
                                        i++;
                                        if ($.trim(this) != '' && i < count_results) {
                                            var search_result_json = JSON.parse(this);
                                            if (search_result_json['label'] == instant_ajax_url_json.domain && $.inArray(search_result_json['tld'], all_avail_sld_exts) > -1 && $.inArray(search_result_json['tld'], sld_args_already_checked) == -1) {
                                                if (search_result_json['isRegistered']) {
                                                    whois_link_html    = instant_ajax_url_json.whois_link_html;
                                                    transfer_link_html = instant_ajax_url_json.transfer_link_html;
                                                    result_class += ' is-registered';
                                                    result_message     = zandc['not_avai_result_message'].replace('{domain}', '<span>' + search_result_json['label'] + '.' + search_result_json['tld'] + '</span>');
                                                }
                                                else {
                                                    whois_link_html    = '';
                                                    transfer_link_html = '';
                                                    result_class += ' not-registered';
                                                    result_message     = zandc['avai_result_message'].replace('{domain}', '<span>' + search_result_json['label'] + '.' + search_result_json['tld'] + '</span>');
                                                    //whois_link_html = '';
                                                    //whois_link_html = zandc['whois_link_html'].replace('{domain}', search_key_split[0] + '.' + search_key_split[1]);
                                                    if (zandc['integrate_with'] == 'woocommerce') {
                                                        if (typeof zandc['wc_integrate_link_args'][search_result_json['tld']] != 'undefined' && typeof typeof zandc['wc_integrate_link_args'][search_result_json['tld']] != false) {
                                                            integrate_link_html = zandc['wc_integrate_link_args'][search_result_json['tld']].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                                                        }
                                                    }
                                                    else {
                                                        integrate_link_html = zandc['integrate_link_html'].replace('{domain}', search_result_json['label'] + '.' + search_result_json['tld']);
                                                    }
                                                }
                                                sld_args_already_checked.push(search_result_json['tld']);
                                                result_html += '<p class="' + result_class + '">' + result_message + transfer_link_html + whois_link_html + integrate_link_html + '</p>';
                                            }
                                        }
                                    });


                                    if (returnTarget.is('span')) {
                                        result_html = result_html.replace(/<p /g, '<span ').replace(/p>/g, 'span>');
                                    }

                                    if (zandc['show_sld_results_before_tld_results'] == 'yes') {
                                        returnTarget.prepend(result_html);
                                    }
                                    else {
                                        returnTarget.find('.zan-dc-close-result').remove();
                                        returnTarget.append(result_html);
                                        returnTarget.append('<a href="#" class="zan-dc-close-result">X</a>');
                                    }
                                }
                                else {
                                    //thisWrapInner.find('.zan-dc-results-wrap .zan-dc-results').remove();
                                }
                            }
                        });
                    }, 200);
                });
            });
        }
    }


});