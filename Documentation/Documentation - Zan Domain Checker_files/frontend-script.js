jQuery(document).ready(function ($) {

    "use strict";

    new WOW().init();

    function is_ie() {
        var ua   = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
        {
            return true;
        }
        else  // If another browser, return 0
        {
            return false;
        }
    }

    // Remove unwanted html
    $('.luckyshop-remove-inner-content').remove();

    // Click return false on class: .click-return-false
    $(document).on('click', '.click-return-false', function (e) {
        return false;
        e.preventDefault();
    });

    // Hover Dir
    function kt_hover_dir() {
        $('.kt-hover-dir').each(function () {
            $(this).hoverdir();
        });
    }

    kt_hover_dir();

    // Click on the thumb image in single product shortcode
    $(document).on('click', '.img-data', function (e) {

        var $this             = $(this);
        var thisSingleProduct = $this.closest('.lk-single-product-wrap');
        var img               = JSON.parse($this.attr('data-img'));
        var img_full          = JSON.parse($this.attr('data-img-full'));

        thisSingleProduct.find('.product-image a').attr('href', img_full['url']);
        thisSingleProduct.find('.product-image a img').attr('src', img['url']).attr('width', img['width']).attr('height', img['height']).attr('alt', img['alt']);
        thisSingleProduct.find('.img-data').removeClass('selected');
        $this.addClass('selected');

        e.preventDefault();

    });

    // Icon box hover color
    $('.icon-box.title-border-inherit-color-icon-hover').each(function () {
        var $this             = $(this);
        var this_border_color = $this.css('border-color');
        var title_color       = $this.find('.icon-box-content > h4').css('color');
        $this.attr('data-border-color', this_border_color);
        $this.find('.icon-box-content > h4').attr('data-color', title_color);
    });

    $('.icon-box').hover(function (e) {
        var $this    = $(this);
        var iconWrap = $this.find('.icon-wrap');
        if (iconWrap.length) {
            var icon_hover_color    = iconWrap.attr('data-hover-color');
            var icon_hover_bg_color = iconWrap.attr('data-hover-bg-color');
            iconWrap.css({
                'color' : icon_hover_color,
                'background-color' : icon_hover_bg_color
            });

            if ($this.is('.title-border-inherit-color-icon-hover')) {
                $this.css({
                    'border-color' : icon_hover_color
                }).find('.icon-box-content > h4').css({
                    'color' : icon_hover_color
                });
            }
        }

    }, function (e) {
        var $this    = $(this);
        var iconWrap = $this.find('.icon-wrap');
        if (iconWrap.length) {
            var icon_color    = iconWrap.attr('data-color');
            var icon_bg_color = iconWrap.attr('data-bg-color');
            iconWrap.css({
                'color' : icon_color,
                'background-color' : icon_bg_color
            });

            if ($this.is('.title-border-inherit-color-icon-hover')) {
                var this_border_color = $this.attr('data-border-color');
                var title_color       = $this.find('.icon-box-content > h4').attr('data-color');
                $this.css({
                    'border-color' : this_border_color
                }).find('.icon-box-content > h4').css({
                    'color' : title_color
                });
            }
        }
    });

    // Socials list hover color
    $('.socials-list li a').hover(function (e) {
        var $this               = $(this);
        var icon_hover_color    = $this.attr('data-hover-color');
        var icon_hover_bg_color = $this.attr('data-hover-bg-color');
        $this.css({
            'color' : icon_hover_color,
            'background-color' : icon_hover_bg_color
        });

    }, function (e) {
        var $this         = $(this);
        var icon_color    = $this.attr('data-color');
        var icon_bg_color = $this.attr('data-bg-color');
        $this.css({
            'color' : icon_color,
            'background-color' : icon_bg_color
        });
    });

    // Floor elevator
    function kt_numbering_floor() {
        var i = 1;
        $('.floor-elevator.has-elevator').each(function () {
            $(this).addClass('floor-elevator-num-' + i).attr('data-floor-num', i);
            i++;
        });
    }

    kt_numbering_floor();

    $(document).on('click', '.btn-elevator', function (e) {
        var $this          = $(this);
        var elevator_speed = 300;

        // Auto
        if ($this.is('.btn-elevator-auto')) {
            var thisFloor         = $this.closest('.floor-elevator');
            var this_floor_number = parseInt(thisFloor.attr('data-floor-num'));
            if ($this.is('.btn-elevator-next')) {
                var next_floor_number = this_floor_number + 1;
                if ($('.floor-elevator.has-elevator[data-floor-num="' + next_floor_number + '"]').length) {
                    $('html, body').animate({
                        scrollTop : $('.floor-elevator.has-elevator[data-floor-num="' + next_floor_number + '"]').offset().top - 50
                    }, elevator_speed);
                }
                else {
                    // If not found next floor, go to current floor
                    $('html, body').animate({
                        scrollTop : thisFloor.offset().top - 50
                    }, elevator_speed);
                }
            }
            else {
                var prev_floor_number = this_floor_number - 1;
                if ($('.floor-elevator.has-elevator[data-floor-num="' + prev_floor_number + '"]').length) {
                    $('html, body').animate({
                        scrollTop : $('.floor-elevator.has-elevator[data-floor-num="' + prev_floor_number + '"]').offset().top - 50
                    }, elevator_speed);
                }
                else {
                    // If not found previous floor, go to current floor
                    $('html, body').animate({
                        scrollTop : thisFloor.offset().top - 50
                    }, elevator_speed);
                }
            }
        }
        else {

        }

        e.preventDefault();
    });

    // Inner link of the elevator (floor)
    $(document).on('click', '.document-content-section a', function (e) {
        var $this     = $(this);
        var elevator_speed = 300;
        var this_href = $this.attr('href');
        if (typeof this_href == 'undefined' || typeof this_href === false) {
            return false;
        }
        if ($(this_href).length) {
            $('html, body').animate({
                scrollTop : $(this_href).offset().top - 90
            }, elevator_speed);
        }

        e.preventDefault();
    });

    // Tabs
    $(document).on('click', '.kt-tabs a[data-toggle="tab"]', function (e) {
        var $this       = $(this);
        var thisNavItem = $this.closest('.tab-nav-item');
        var thisTabWrap = $this.closest('.kt-tabs');
        var tab_link    = $this.attr('href');

        if (thisNavItem.is('.active')) {
            return false;
        }

        thisTabWrap.find('.tab-nav-item').removeClass('active');
        thisNavItem.addClass('active');
        thisTabWrap.find('.tab-panel').removeClass('active');
        thisTabWrap.find(tab_link).addClass('active');
        thisTabWrap.trigger('changed_tab');
        $this.trigger('actived_tab');

        e.preventDefault();

    });

    function kt_products_carousel_tab_effect() {
        // effect first tab
        var sleep = 0;
        $('.kt-tabs-effect.nav-tab>li.active a[data-toggle="tab"]').each(function () {
            var tabElement = $(this);
            setTimeout(function () {
                tabElement.trigger('click');
            }, sleep);
            sleep += 10000
        });
        // effect click
        $(document).on('actived_tab', '.kt-tabs-effect a[data-toggle="tab"]', function () {
            var item = '.products > .product, .item-category';

            var tab_id       = $(this).attr('href');
            var tab_animated = $(this).attr('data-animated');
            tab_animated     = ( tab_animated == undefined ) ? 'fadeInUp' : tab_animated;

            if ($(tab_id).find('.owl-carousel').length > 0) {
                item = '.owl-item.active';
            }
            $(tab_id).find(item).each(function (i) {
                var t     = $(this);
                var style = $(this).attr("style");
                style     = ( style == undefined ) ? '' : style;
                var delay = i * 100;
                t.attr("style", style +
                    ";-webkit-animation-delay:" + delay + "ms;"
                    + "-moz-animation-delay:" + delay + "ms;"
                    + "-o-animation-delay:" + delay + "ms;"
                    + "animation-delay:" + delay + "ms;"
                ).addClass(tab_animated + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    t.removeClass(tab_animated + ' animated');
                    t.attr("style", style);
                });
            });

        });
    }

    kt_products_carousel_tab_effect();

    // Try make border Products Carousel Tabs on equal row
    $('.vc_row-o-equal-height .try-border-equal-row').each(function () {
        var $this = $(this);
        $this.closest('.vc_column-inner').css({
            'border' : '1px solid #f0f0f0'
        });
    });
    $('.vc_row-o-equal-height .try-border-left-equal-row').each(function () {
        var $this = $(this);
        $this.closest('.vc_column-inner').css({
            'border-left' : '1px solid #f0f0f0'
        });
    });
    $('.vc_row-o-equal-height .try-border-right-equal-row').each(function () {
        var $this = $(this);
        $this.closest('.vc_column-inner').css({
            'border-right' : '1px solid #f0f0f0'
        });
    });
    $('.vc_row-o-equal-height .try-border-top-equal-row').each(function () {
        var $this = $(this);
        $this.closest('.vc_column-inner').css({
            'border-top' : '1px solid #f0f0f0'
        });
    });
    $('.vc_row-o-equal-height .try-border-bottom-equal-row').each(function () {
        var $this = $(this);
        $this.closest('.vc_column-inner').css({
            'border-bottom' : '1px solid #f0f0f0'
        });
    });

    // Popup form (search popup)
    $(document).on('click', '.kt-show-popup', function (e) {
        var $this    = $(this);
        var popup_id = $this.attr('href'); // Include "#"

        if ($(popup_id).length) {
            $(popup_id).css({
                'display' : 'block'
            }).animate({
                'opacity' : 1
            }, 500, function () {
                // Animation complete.
                if ($(popup_id).is('.search-popup')) {
                    $(popup_id).find('input[type="text"]').focus();
                }
            });
        }

        e.preventDefault();
    });

    // Close popup
    $(document).on('click', '.kt-popup .popup-close, .kt-popup .kt-overlay', function (e) {
        var $this     = $(this);
        var thisPopup = $this.closest('.kt-popup');

        thisPopup.animate({
            'opacity' : 0
        }, 200, function () {
            // Animation complete.
        }).css({
            'display' : 'none'
        });

        e.preventDefault();
    });

    // Documentation menu item click
    $(document).on('click', '.menu-documentation-container .menu > li a', function (e) {
        var $this     = $(this);
        var anchor_id = $this.closest('li').attr('id');

        if (typeof anchor_id == 'undefined' || typeof anchor_id === false) {
            return false;
        }

        if ($('#anchor-' + anchor_id).length) {
            $('html, body').animate({
                scrollTop : $('#anchor-' + anchor_id).offset().top - 110
            }, 600);
        }

        e.preventDefault();
    });


    // DISABLE CLICK CLASS
    $(document).on('click', '.disable-click', function (e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();
        return false;
    });

    $('.disable-click').each(function () {
        $(this).removeClass('zoom');
    });

    //NICESCROLL
    //$('.toggle-header > div.ts-search-head, .toggle-header > div.ts-main-menu, .toggle-header > div.ts-shoppingcart-header').niceScroll({
    //    cursorborder:"1px solid #6b6b6b",
    //    cursorcolor:'#6b6b6b',
    //    horizrailenabled:false
    //});
    //$('.header-style-1 .toggle-header > div.ts-show-account').niceScroll({
    //    cursorborder:"",
    //    cursorcolor:'#cccccc',
    //    horizrailenabled:false
    //});
    //$('.header-style-2 .toggle-header > div.ts-show-account').niceScroll({
    //    cursorborder:"",
    //    cursorcolor:'#6b6b6b',
    //    horizrailenabled:false
    //});

    // FOCUS TOGGLE DROPDOWN AREA ON HEADER WHEN HOVER
    // To make sure scrollable every time mouse hover
    //$(document).on('mouseenter', '.toggle-header .showing', function(){
//        $(this).focus(); 
//    });


    // Submit Mailchimp via ajax
    $(document).on('submit', 'form[name="news_letter"]', function (e) {

        var $this    = $(this);
        var thisWrap = $this.closest('.newsletter-form-wrap');

        if ($this.hasClass('processing')) {
            return false;
        }

        var api_key         = $this.find('input[name="api_key"]').val();
        var list_id         = $this.find('input[name="list_id"]').val();
        var success_message = $this.find('input[name="success_message"]').val();
        var email           = $this.find('input[name="email"]').val();

        var data = {
            action : 'luckyshop_core_submit_mailchimp_via_ajax',
            api_key : api_key,
            list_id : list_id,
            success_message : success_message,
            email : email
        }

        $this.addClass('processing');
        thisWrap.find('.return-message').remove();

        $.post(ajaxurl, data, function (response) {

            if ($.trim(response['success']) == 'yes') {
                $this.after('<p class="return-message bg-success">' + response['message'] + '</p>');
                $this.find('input[name="email"]').val('');
            }
            else {
                $this.after('<p class="return-message bg-danger">' + response['message'] + '</p>');
            }

            $this.removeClass('processing');

        });

        e.preventDefault();
        return false;

    });

    // Count down (coming soon mode)
    // Coming Soon Countdown
    $('.luckyshop-countdown-wrap').each(function () {
        var $this             = $(this);
        var countdown_to_date = $this.attr('data-countdown');

        if (typeof countdown_to_date == 'undefined' || typeof countdown_to_date == false) {
            var cd_class = $this.attr('class');
            if ($.trim(cd_class) != '') {
                cd_class = cd_class.split('luckyshop-cms-date_');
                if (typeof cd_class[1] != 'undefined' && typeof cd_class[1] != false) {
                    countdown_to_date = cd_class[1];
                }
            }
        }

        if (typeof countdown_to_date != 'undefined' && typeof countdown_to_date != false) {
            if ($this.hasClass('countdown-admin-menu')) { // For admin login
                $this.find('a').countdown(countdown_to_date, function (event) {
                    $this.find('a').html(
                        event.strftime(luckyshop['html']['countdown_admin_menu'])
                    );
                });
            }
            else {
                $this.countdown(countdown_to_date, function (event) {
                    $this.html(
                        event.strftime(luckyshop['html']['countdown'])
                    );
                });
            }
        }

    });

    // Equal Columns in vc row
    function luckyshop_set_equal_columns() {
        $('.vc_row.equal-columns').each(function () {

            var $this = $(this);
            if ($this.find('> .wpb_column').length > 1) {

                $this.find('> .wpb_column').css({
                    'height' : ''
                });

                // Set delay 50ms to fix firefox
                setTimeout(function () {
                    // Caculate max height
                    var max_col_h = 0;
                    $this.find('> .wpb_column').each(function () {
                        var this_col_h = $(this).height();
                        if (this_col_h > max_col_h) {
                            max_col_h = this_col_h;
                        }
                    });

                    // Set all column in row the same height
                    $this.find('> .wpb_column').height(max_col_h);
                }, 50);
            }

        });

    }

    //luckyshop_set_equal_columns();

    // ALIGN
    function luckyshop_owl_align_nav_by_elem($owl_slider) {
        if ($owl_slider.is('.nav-align-mid-by-elem')) {
            var align_selector = $owl_slider.attr('data-align-elem');
            var align_elem_h   = $owl_slider.find(align_selector).height();
            var nav_h          = $owl_slider.find('.owl-next').height();
            var pos_top        = (align_elem_h - nav_h) / 2;
            $owl_slider.find('.owl-prev, .owl-next').css({
                'top' : pos_top + 'px',
                'transform' : 'translateY(0)',
                '-ms-transform' : 'translateY(0)',
                '-o-transform' : 'translateY(0)',
                '-webkit-transform' : 'translateY(0)'
            });
        }
    }

    // Need add carousel to a list (full width)
    function luckyshop_add_carousel_to_list() {
        $('.lk-add-carousel-to-ul').each(function () {
            var $this              = $(this);
            var ul_target_selector = $this.attr('data-ul-target');
            if (typeof ul_target_selector == 'undefined' || typeof ul_target_class === false) {
                ul_target_selector = 'ul';
            }
            if ($this.find(ul_target_selector).length) {
                var total_item = $this.find(ul_target_selector + ' > li').length;
                if (total_item >= 2) {
                    $this.find(ul_target_selector).addClass('luckyshop-owl-carousel nav-center nav-style-1').attr('data-number', 4).attr('data-margin', 10).attr('data-navControl', 'yes');
                }
            }
            else {
                //console.log(ul_target_selector);
            }
        });
    }

    luckyshop_add_carousel_to_list();


    // LUCKYSHOP OWL CAROUSEL
    function luckyshop_init_owl_carousel() {

        // Remove unwanted html
        $('.luckyshop-remove-inner-content').remove();

        $('.luckyshop-owl-carousel').each(function () {

            var $this            = $(this),
                $loop            = $this.attr('data-loop') == "yes",
                $numberItem      = parseInt($this.attr('data-number')),
                $Nav             = $this.attr('data-navControl') == "yes",
                $Dots            = $this.attr('data-Dots') == "yes",
                $autoplay        = $this.attr('data-autoPlay') == "yes",
                $autoplayTimeout = parseInt($this.attr('data-autoPlayTimeout')),
                $marginItem      = parseInt($this.attr('data-margin')),
                $rtl             = $this.attr('data-rtl') == "yes",
                $responsiveClass = $this.attr('data-responsiveClass') == 'yes',
                $resNumber; // Responsive Settings

            $numberItem      = (isNaN($numberItem)) ? 1 : $numberItem;
            $autoplayTimeout = (isNaN($autoplayTimeout)) ? 4000 : $autoplayTimeout;
            $marginItem      = (isNaN($marginItem)) ? 0 : $marginItem;

            switch ($numberItem) {

                case 1 :
                    $resNumber = {
                        0 : {
                            items : 1
                        }
                    }
                    break;

                case 2 :
                    $resNumber = {
                        0 : {
                            items : 1
                        },
                        480 : {
                            items : 1
                        },
                        768 : {
                            items : 2
                        },
                        992 : {
                            items : $numberItem
                        }
                    }
                    break;

                case 3 :
                case 4 :
                    $resNumber = {
                        0 : {
                            items : 1
                        },
                        480 : {
                            items : 1
                        },
                        768 : {
                            items : 2
                        },
                        992 : {
                            items : 3
                        },
                        1200 : {
                            items : $numberItem
                        }
                    }
                    break;

                default : // $numberItem > 4
                    $resNumber = {
                        0 : {
                            items : 1
                        },
                        480 : {
                            items : 2
                        },
                        768 : {
                            items : 3
                        },
                        992 : {
                            items : 4
                        },
                        1200 : {
                            items : $numberItem
                        }
                    }
                    break;
            } // Endswitch

            if ($this.is('.luckyshop-product-thumbs-carousel')) {
                $resNumber = {
                    0 : {
                        items : 3
                    },
                    480 : {
                        items : parseInt($this.attr('data-total-items')) > 3 ? 4 : parseInt($this.attr('data-total-items')),
                    },
                    768 : {
                        items : 3
                    },
                    992 : {
                        items : 3
                    },
                    1200 : {
                        items : 3
                    }
                }
            }

            if (!$this.is('.carousel-initiated')) {
                $this.owlCarousel({
                    loop : $loop,
                    nav : $Nav,
                    navText : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                    dots : $Dots,
                    autoplay : $autoplay,
                    autoplayTimeout : $autoplayTimeout,
                    margin : $marginItem,
                    //responsiveBaseElement: $responsiveBaseElement,
                    responsiveClass : $responsiveClass,
                    rtl : $rtl,
                    responsive : $resNumber,
                    autoplayHoverPause : true,
                    //center: true,
                    onRefreshed : function () {
                        var total_active = $this.find('.owl-item.active').length;
                        var i            = 0;

                        $this.find('.owl-item').removeClass('active-first active-last');
                        $this.find('.owl-item.active').each(function () {
                            i++;
                            if (i == 1) {
                                $(this).addClass('active-first');
                            }
                            if (i == total_active) {
                                $(this).addClass('active-last');
                            }
                        });

                        // If is Products Carousel 3 (shortcode), make all product in items equal each others
                        if ($this.is('.products-carousel-3')) {
                            $this.find('.products > li').css({
                                'height' : 'auto'
                            });
                            var product_h = 0;
                            $this.find('.products > li').each(function () {
                                product_h = $(this).innerHeight() > product_h ? $(this).innerHeight() : product_h;
                            });
                            $this.find('.products > li').css({
                                'height' : product_h + 'px'
                            });
                        }
                    },
                    onTranslated : function () {
                        var total_active = $this.find('.owl-item.active').length;
                        var i            = 0;

                        $this.find('.owl-item').removeClass('active-first active-last');
                        $this.find('.owl-item.active').each(function () {
                            i++;
                            if (i == 1) {
                                $(this).addClass('active-first');
                            }
                            if (i == total_active) {
                                $(this).addClass('active-last');
                            }
                        });
                    },
                    onInitialized : function () {
                        // If is Products Carousel 3 (shortcode), make all product in items equal each others
                        if ($this.is('.products-carousel-3')) {
                            $this.find('.products > li').css({
                                'height' : 'auto'
                            });
                            var product_h = 0;
                            $this.find('.products > li').each(function () {
                                product_h = $(this).innerHeight() > product_h ? $(this).innerHeight() : product_h;
                            });
                            $this.find('.products > li').css({
                                'height' : product_h + 'px'
                            });
                        }
                    },
                    onResized : function () {
                        luckyshop_owl_align_nav_by_elem($this);
                        //luckyshop_set_equal_columns();
                    }
                });
                $this.addClass('carousel-initiated');
            }
            else {
                if ($this.is('.products-carousel-3')) {
                    $this.find('.products > li').css({
                        'height' : 'auto'
                    });
                    var product_h = 0;
                    $this.find('.products > li').each(function () {
                        product_h = $(this).innerHeight() > product_h ? $(this).innerHeight() : product_h;
                    });
                    $this.find('.products > li').css({
                        'height' : product_h + 'px'
                    });
                }
            }

        });
    }

    luckyshop_init_owl_carousel();

    // Google maps 
    $('.kt-gmaps').each(function () {
        var $this             = $(this),
            $id               = $this.attr('id'),
            $zoom             = parseInt($this.attr('data-zoom')),
            $latitude         = $this.attr('data-latitude'),
            $longitude        = $this.attr('data-longitude'),
            $address          = $this.attr('data-address'),
            $info_title       = $this.attr('data-info_title'),
            $phone            = $this.attr('data-phone'),
            $email            = $this.attr('data-email'),
            $website          = $this.attr('data-website'),
            $map_type         = $this.attr('data-map-type'),
            $pin_icon         = $this.attr('data-pin-icon'),
            $pan_control      = $this.attr('data-pan-control') === "true" ? true : false,
            $map_type_control = $this.attr('data-map-type-control') === "true" ? true : false,
            $scale_control    = $this.attr('data-scale-control') === "true" ? true : false,
            $draggable        = $this.attr('data-draggable') === "true" ? true : false,
            $zoom_control     = $this.attr('data-zoom-control') === "true" ? true : false,
            $modify_coloring  = $this.attr('data-modify-coloring') === "true" ? true : false,
            $saturation       = $this.attr('data-saturation'),
            $hue              = $this.attr('data-hue'),
            $lightness        = $this.attr('data-lightness'),
            $styles;


        if ($modify_coloring == true) {
            var $styles = [{
                stylers : [{
                    hue : $hue
                }, {
                    saturation : $saturation
                }, {
                    lightness : $lightness
                }, {
                    featureType : "landscape.man_made",
                    stylers : [{
                        visibility : "on"
                    }]
                }]
            }];
        }

        var map;

        function initialize() {
            var bounds     = new google.maps.LatLngBounds();
            var mapOptions = {
                zoom : $zoom,
                panControl : $pan_control,
                zoomControl : $zoom_control,
                mapTypeControl : $map_type_control,
                scaleControl : $scale_control,
                draggable : $draggable,
                scrollwheel : false,
                mapTypeId : google.maps.MapTypeId[$map_type],
                styles : $styles
            };

            map = new google.maps.Map(document.getElementById($id), mapOptions);
            map.setTilt(45);

            // Multiple Markers

            var markers           = [];
            var infoWindowContent = [];

            if ($latitude != '' && $longitude != '') {
                markers[0] = [$address, $latitude, $longitude];

                var info_show_html = '';
                if ($.trim($info_title) != '') {
                    info_show_html += '<h4 class="map_info_title">' + $info_title + '</h4>';
                }

                if ($.trim($address) != '') {
                    info_show_html += '<p>' + $address + '</p>';
                }

                if ($.trim($phone) != '') {
                    info_show_html += '<i class="fa fa-phone"></i>' + $phone + '<br />';
                }

                if ($.trim($email) != '') {
                    info_show_html += '<i class="fa fa-envelope"></i><a href="mailto:' + $email + '">' + $email + '</a><br />';
                }

                if ($.trim($website) != '') {
                    info_show_html += '<i class="fa fa-globe"></i><a href="' + $website + '" target="_blank">' + $website + '</a><br />';
                }

                if ($.trim(info_show_html) != '') {
                    info_show_html = '<div class="map_info_box">' + info_show_html + '</div>';
                }

                infoWindowContent[0] = [info_show_html];
            }

            var infoWindow = new google.maps.InfoWindow(), marker, i;

            for (i = 0; i < markers.length; i++) {
                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                bounds.extend(position);
                marker = new google.maps.Marker({
                    position : position,
                    map : map,
                    title : markers[i][0],
                    icon : $pin_icon
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        if (infoWindowContent[i][0].length > 1) {
                            infoWindow.setContent(infoWindowContent[i][0]);
                        }

                        infoWindow.open(map, marker);
                    }
                })(marker, i));

                map.fitBounds(bounds);

            }

            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
                this.setZoom($zoom);
                google.maps.event.removeListener(boundsListener);
            });
        }

        initialize();

        function googleMapsResize() {
            initialize();
        }

        var temporaryTabsContainer = jQuery('.mk-tabs');
        if (temporaryTabsContainer.length) {
            temporaryTabsContainer.each(function () {
                google.maps.event.addDomListener($(this)[0], "click", googleMapsResize);
            });

        }

        var fullHeight = $this.hasClass('full-height-map');

        function fullHeightMap() {
            if (fullHeight) {
                var $window_height = jQuery(window).outerHeight(), wp_admin_height = 0, header_height = 0;
                if (jQuery.exists('#mk-header .mk-header-holder')) {
                    header_height = parseInt(jQuery('#mk-header').attr('data-sticky-height'));
                }

                if (jQuery.exists("#wpadminbar")) {
                    var wp_admin_height = jQuery("#wpadminbar").outerHeight();
                }

                $window_height = $window_height - wp_admin_height - header_height;
                $this.css('height', $window_height);
            }
        }

        fullHeightMap();
        jQuery(window).on('debouncedresize', fullHeightMap);
    });

    function luckyshop_instagram_square() {
        $('ul.instargram').each(function () {
            var thisWidth = $(this).find('> li:first-child a').width();
            $(this).find('> li a').css({
                'height' : thisWidth + 'px'
            });
        });
    }

    luckyshop_instagram_square();

    // Window resize
    $(window).on('debouncedresize', function () {
        //luckyshop_set_equal_columns();
        luckyshop_instagram_square();
        //luckyshop_init_owl_carousel();
    });

    // Window loaded
    $(window).load(function () {
        //luckyshop_set_equal_columns();
        luckyshop_instagram_square();
        luckyshop_init_owl_carousel();
        //$(window).trigger('resize');

        // if ($('.luckyshop-owl-carousel.nav-align-mid-by-elem').length) {
        //     $('.luckyshop-owl-carousel.nav-align-mid-by-elem').each(function () {
        //         var $this = $(this);
        //         luckyshop_owl_align_nav_by_elem($this);
        //     });
        // }

    });

    // Ajax completed
    // $(document).ajaxComplete(function (event, xhr, settings) {
    //     luckyshop_init_owl_carousel();
    // });

    // For customizer mode only
    //if ( $('#base-color-hidden').length ) {
    //    var base_color = $('#base-color-hidden').val();
    //    var css_url_old = luckyshop['custom_style_via_ajax_url'];
    //    var css_url_new = luckyshop_insert_param( css_url_old, 'base_color', base_color );
    //
    //    $('#luckyshopcore-customize-style-css').attr('href', css_url_new);
    //}

    // Insert param to URL
    //function luckyshop_insert_param(uri, key, value) {
    //    key = encodeURIComponent(key); value = encodeURIComponent(value);
    //
    //    var kvp = uri.split('&');
    //
    //    var i=kvp.length; var x; while(i--)
    //    {
    //        x = kvp[i].split('=');
    //
    //        if (x[0]==key)
    //        {
    //            x[1] = value;
    //            kvp[i] = x.join('=');
    //            break;
    //        }
    //    }
    //
    //    if(i<0) {kvp[kvp.length] = [key,value].join('=');}
    //
    //    // Return URL with a param inserted
    //    return kvp.join('&');
    //}
    //Slide feature
    //$('.ts-slide-features').each(function(){
    //
    //    $(this).owlCarousel({
    //        loop:true,
    //        nav: false,
    //        dots:true,
    //        margin: 0,
    //        autoplay:true,
    //        responsiveClass: false,
    //        responsive:{
    //            0:{
    //                items:1
    //            },
    //            480:{
    //                items:1
    //            },
    //            768:{
    //                items:1
    //            },
    //            992:{
    //                items:1
    //            }
    //        }
    //    })
    //});
});  
   