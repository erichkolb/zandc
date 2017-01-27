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
            recaptcha_response : recaptcha_response
        };

        $.post(zandc['ajaxurl'], data, function (response) {

            if (!(thisWrap.find('.zan-dc-results-wrap .zan-dc-results').length)) {
                thisWrap.append('<div class="zan-dc-results-wrap"><div class="zan-dc-results"></div></div>');
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

            console.log(response);

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

});