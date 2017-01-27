<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zanDomainChecker' );
function zanDomainChecker() {

	$anim_effects_in = array(
		esc_html__( '--- No Animation ---', 'zandc' ) => '',
		esc_html__( 'bounce', 'zandc' )               => 'bounce',
		esc_html__( 'flash', 'zandc' )                => 'flash',
		esc_html__( 'pulse', 'zandc' )                => 'pulse',
		esc_html__( 'rubberBand', 'zandc' )           => 'rubberBand',
		esc_html__( 'shake', 'zandc' )                => 'shake',
		esc_html__( 'swing', 'zandc' )                => 'swing',
		esc_html__( 'tada', 'zandc' )                 => 'tada',
		esc_html__( 'wobble', 'zandc' )               => 'wobble',
		esc_html__( 'jello', 'zandc' )                => 'jello',
		esc_html__( 'bounceIn', 'zandc' )             => 'bounceIn',
		esc_html__( 'bounceInDown', 'zandc' )         => 'bounceInDown',
		esc_html__( 'bounceInLeft', 'zandc' )         => 'bounceInLeft',
		esc_html__( 'bounceInRight', 'zandc' )        => 'bounceInRight',
		esc_html__( 'bounceInUp', 'zandc' )           => 'bounceInUp',
		esc_html__( 'fadeIn', 'zandc' )               => 'fadeIn',
		esc_html__( 'fadeInDown', 'zandc' )           => 'fadeInDown',
		esc_html__( 'fadeInDownBig', 'zandc' )        => 'fadeInDownBig',
		esc_html__( 'fadeInLeft', 'zandc' )           => 'fadeInLeft',
		esc_html__( 'fadeInLeftBig', 'zandc' )        => 'fadeInLeftBig',
		esc_html__( 'fadeInRight', 'zandc' )          => 'fadeInRight',
		esc_html__( 'fadeInRightBig', 'zandc' )       => 'fadeInRightBig',
		esc_html__( 'fadeInUp', 'zandc' )             => 'fadeInUp',
		esc_html__( 'fadeInUpBig', 'zandc' )          => 'fadeInUpBig',
		esc_html__( 'flip', 'zandc' )                 => 'flip',
		esc_html__( 'flipInX', 'zandc' )              => 'flipInX',
		esc_html__( 'flipInY', 'zandc' )              => 'flipInY',
		esc_html__( 'lightSpeedIn', 'zandc' )         => 'lightSpeedIn',
		esc_html__( 'rotateIn', 'zandc' )             => 'rotateIn',
		esc_html__( 'rotateInDownLeft', 'zandc' )     => 'rotateInDownLeft',
		esc_html__( 'rotateInDownRight', 'zandc' )    => 'rotateInDownRight',
		esc_html__( 'rotateInUpLeft', 'zandc' )       => 'rotateInUpLeft',
		esc_html__( 'rotateInUpRight', 'zandc' )      => 'rotateInUpRight',
		esc_html__( 'slideInUp', 'zandc' )            => 'slideInUp',
		esc_html__( 'slideInDown', 'zandc' )          => 'slideInDown',
		esc_html__( 'slideInLeft', 'zandc' )          => 'slideInLeft',
		esc_html__( 'slideInRight', 'zandc' )         => 'slideInRight',
		esc_html__( 'zoomIn', 'zandc' )               => 'zoomIn',
		esc_html__( 'zoomInDown', 'zandc' )           => 'zoomInDown',
		esc_html__( 'zoomInLeft', 'zandc' )           => 'zoomInLeft',
		esc_html__( 'zoomInRight', 'zandc' )          => 'zoomInRight',
		esc_html__( 'zoomInUp', 'zandc' )             => 'zoomInUp',
		esc_html__( 'rollIn', 'zandc' )               => 'rollIn',
	);

	vc_map(
		array(
			'name'     => esc_html__( 'Instant Domain Checker', 'zandc' ),
			'base'     => 'zandomainchecker', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Instant Domain Checker', 'zandc' ),
			'params'   => array(
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Placeholder Text', 'zandc' ),
					'param_name' => 'placeholder_text',
					'std'        => esc_html__( 'Search domain', 'zandc' ),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Show Search Button', 'zandc' ),
					'param_name' => 'show_search_btn',
					'value'      => array(
						esc_html__( 'Yes', 'zandc' ) => 'yes',
						esc_html__( 'No', 'zandc' )  => 'no'
					),
					'std'        => 'yes'
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Search Button Text', 'zandc' ),
					'param_name' => 'search_btn_text',
					'std'        => esc_html__( 'Search', 'zandc' ),
					'dependency' => array(
						'element' => 'show_search_btn',
						'value'   => 'yes',
					),
				),
				/*
				array(
					'type'        => 'textarea',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Top Level Domain Extensions', 'zandc' ),
					'param_name'  => 'tld_exts',
					'std'         => '',
					'description' => esc_html__( 'List of supported TLD extensions, each extension is separated by a vertical stripe. Ex: com|org|net|us|jp|vn. Empty list means all extensions are allowed.', 'zandc' ),
				),
				*/
				array(
					'type'       => 'dropdown',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'CSS Animation', 'zandc' ),
					'param_name' => 'css_animation',
					'value'      => $anim_effects_in,
					'std'        => 'fadeInUp',
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Animation Delay', 'zandc' ),
					'param_name'  => 'animation_delay',
					'std'         => '0.4',
					'description' => esc_html__( 'Delay unit is second.', 'zandc' ),
					'dependency'  => array(
						'element'   => 'css_animation',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Style', 'zandc' ),
					'param_name' => 'style',
					'value'      => array(
						esc_html__( 'Style 1', 'zandc' ) => 'style_1',
						esc_html__( 'Style 2', 'zandc' ) => 'style_2',
						esc_html__( 'Style 3', 'zandc' ) => 'style_3',
						esc_html__( 'Style 4', 'zandc' ) => 'style_4',
					),
					'std'        => 'style_1',
					'group'      => esc_html__( 'Style And Color', 'zandc' )
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Input Color', 'zandc' ),
					'param_name' => 'input_color',
					'std'        => '#616161',
					'group'      => esc_html__( 'Style And Color', 'zandc' )
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Input Background Color', 'zandc' ),
					'param_name' => 'input_bg_color',
					'std'        => '#ececec',
					'group'      => esc_html__( 'Style And Color', 'zandc' )
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Button Text Color', 'zandc' ),
					'param_name' => 'btn_text_color',
					'std'        => '#ffffff',
					'group'      => esc_html__( 'Style And Color', 'zandc' )
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Button Background Color', 'zandc' ),
					'param_name' => 'btn_bg_color',
					'std'        => '#71d9a0',
					'group'      => esc_html__( 'Style And Color', 'zandc' )
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'Css', 'zandc' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'zandc' ),
				)
			)
		)
	);
}

function zan_dc_form( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zandomainchecker', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'placeholder_text' => '',
				'show_search_btn'  => 'yes',
				'search_btn_text'  => '',
				'tld_exts'         => '',
				'css_animation'    => '',
				'animation_delay'  => '0.4',   //In second
				'style'            => 'style_1',
				'input_color'      => '',
				'input_bg_color'   => '',
				'btn_text_color'   => '',
				'btn_bg_color'     => '',
				'css'              => '',
			), $atts
		)
	);

	$css_class = 'zan-dc-wrap zan-dc-wc-shortcode-wrap wow ' . $css_animation . ' zan-dc-' . $style;
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = 0;
	}
	$animation_delay = $animation_delay . 's';

	$html = '';
	$btn_html = '';
	$recaptcha_html = '';

	$dc_nonce = wp_create_nonce( 'domain-check-availability' );
	$form_id = uniqid( 'zan_dc_form_' );
	$action_url = ZANDC_BASE_URL . 'core/shortcodes/domainchecker.php';

	$input_style = '';
	$btn_style = '';
	if ( trim( $input_color ) != '' ) {
		$input_style .= 'color: ' . esc_attr( $input_color ) . ';';
	}
	if ( trim( $input_bg_color ) != '' ) {
		$input_style .= 'background-color: ' . esc_attr( $input_bg_color ) . ';';
	}

	if ( trim( $input_style ) != '' ) {
		$input_style = 'style="' . $input_style . '"';
	}

	if ( trim( $btn_text_color ) != '' ) {
		$btn_style .= 'color: ' . esc_attr( $btn_text_color ) . ';';
	}
	if ( trim( $btn_bg_color ) != '' ) {
		$btn_style .= 'background-color: ' . esc_attr( $btn_bg_color ) . ';';
	}

	if ( trim( $btn_style ) != '' ) {
		$btn_style = 'style="' . $btn_style . '"';
	}

	if ( trim( $show_search_btn ) == 'yes' ) {
		$css_class .= ' has-submit-btn';
		$btn_html .= '<button type="submit" name="zan-dc-submit" class="zan-dc-submit-btn hover-effect-crossing" ' . $btn_style . '><span class="zan-dc-btn-icon"></span>' . esc_attr( $search_btn_text ) . '</button>';
	}

	$enable_recaptcha = zan_dc_get_option( 'zandc_enable_recaptcha', 'no' );
	$recaptcha_site_key = zan_dc_get_option( 'zandc_recaptcha_site_key', 'no' );
	$recaptcha_secret_key = zan_dc_get_option( 'zandc_recaptcha_secret_key', 'no' );
	$enable_instant_domain_search = zan_dc_get_option( 'zandc_enable_instant_domain_search', 'no' );
	$try_faster_checking = zan_dc_get_option( 'zandc_try_faster_checking', 'no' );
	$try_country_detection = zan_dc_get_option( 'zandc_try_country_detection', 'yes' );
	$remove_form_wrap_on_instant_search = zan_dc_get_option( 'zandc_remove_form_wrap', false );

	$form_tag = 'form';

	if ( $enable_instant_domain_search == 'yes' && $remove_form_wrap_on_instant_search ) {
		$form_tag = 'div';
	}

	$wrap_attrs = '';

	if ( $enable_recaptcha == 'yes' && trim( $recaptcha_site_key ) != '' && trim( $recaptcha_secret_key ) != '' ) {
		$css_class .= ' has-recaptcha';
		$recaptcha_html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $recaptcha_site_key ) . '"></div>';
		$enable_instant_domain_search = 'no'; // Not support instant search when reCAPTCHA is enabled
		$try_faster_checking = 'no';
		$try_country_detection = 'no';
	}

	if ( $enable_instant_domain_search == 'yes' ) {
		$css_class .= ' is-instant-search';
	}

	if ( $try_faster_checking == 'yes' ) {
		$css_class .= ' try-faster-checking';
	}

	if ( $try_country_detection == 'yes' ) {
		$css_class .= ' try-country_detection';
		$country_code = zandc_ip_info( 'Visitor', 'Country Code' );
		$wrap_attrs .= ' data-country-code="' . esc_attr( strtolower( $country_code ) ) . '"';
	}


	if ( $style != 'style_4' ) {
		$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '" ' . $wrap_attrs . '>
					<div class="zan-dc-inner">
						<' . $form_tag . ' method="post" action="' . esc_url( $action_url ) . '" class="zan-dc-form" id="' . esc_attr( $form_id ) . '" name="zan_dc_form" autocomplete="off">
							<div class="zan-dc-input-wrap">
								<input type="hidden" name="zan_dc_nonce" value="' . esc_attr( $dc_nonce ) . '" />
								<input type="text" class="zan-dc-input" name="zandomainchecker" placeholder="' . esc_attr( $placeholder_text ) . '" ' . $input_style . ' />
								' . $btn_html . '
							</div><!-- /.zan-dc-input-wrap -->
							' . $recaptcha_html . '
						</' . $form_tag . '>
					</div>
				</div>';
	}
	else {
		// For style_4: Display TLD exts dropdown with pricing (if nntegrate with WooCoomerce)

		$tld_exts_dropdown_html = '';

		$all_available_exts = zan_dc_get_option( 'zandc_tld_exts', '' ); // List of supported TLD exts. If empty (supported all), don't show dropdown
		$tld_exts_integrated_with_wc_products = zan_dc_get_option( 'zandc_tld_exts_integrated_with_wc_products', '' );

		$exts_dropdown_args = array();
		$all_available_exts_args = array();

		if ( trim( $all_available_exts ) != '' ) {
			$all_available_exts = explode( '|', $all_available_exts );

			foreach ( $all_available_exts as $ext ) {
				$ext = trim( str_replace( '.', '', $ext ) );
				if ( $ext != '' ) {
					$all_available_exts_args[] = $ext;
				}
			}
		}

		if ( trim( $tld_exts_integrated_with_wc_products ) != '' ) {
			$tld_exts_integrated_with_wc_products = array_filter( explode( '|', $tld_exts_integrated_with_wc_products ) );
			$currency_symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
			if ( !empty( $tld_exts_integrated_with_wc_products ) ) {
				foreach ( $tld_exts_integrated_with_wc_products as $tld_ext_integrated_with_wc_product ) {
					$tld_ext_integrated_with_wc_product = array_filter( explode( '-', $tld_ext_integrated_with_wc_product ) );
					$product_id = 0;
					$domain_ext = '';
					if ( isset( $tld_ext_integrated_with_wc_product[0] ) ) {

						if ( is_numeric( $tld_ext_integrated_with_wc_product[0] ) ) {
							$product_id = intval( $tld_ext_integrated_with_wc_product[0] );
							$domain_ext = isset( $tld_ext_integrated_with_wc_product[1] ) ? esc_attr( $tld_ext_integrated_with_wc_product[1] ) : '';
						}
						else {
							$domain_ext = esc_attr( $tld_ext_integrated_with_wc_product[0] );
							if ( isset( $tld_ext_integrated_with_wc_product[1] ) ) {
								$product_id = is_numeric( $tld_ext_integrated_with_wc_product[1] ) ? intval( $tld_ext_integrated_with_wc_product[1] ) : 0;
							}
						}
					}
					if ( trim( $domain_ext ) != '' && in_array( trim( $domain_ext ), $all_available_exts ) ) {
						if ( $product_id > 0 && class_exists( 'WooCommerce' ) ) {
							$product = new WC_Product( $product_id );
							$price = $product->price;
							$exts_dropdown_args[$domain_ext] = $currency_symbol . $price;
						}
						else {
							$exts_dropdown_args[$domain_ext] = '';
						}
					}
				}
			}
		}

		if ( !empty( $exts_dropdown_args ) ) {
			$tld_exts_dropdown_html .= '<select name="zan_dc_tld_ext" class="zan-dc-tld-exts-select zan-dc-select" style="color: ' . esc_attr( $input_color ) . ';">';
			foreach ( $exts_dropdown_args as $domain_ext => $price ) {
				$tld_exts_dropdown_html .= '<option data-price="' . esc_attr( $price ) . '" value="' . esc_attr( $domain_ext ) . '">.' . esc_attr( $domain_ext ) . '</option>';
			}
			$tld_exts_dropdown_html .= '</select>';
		}

		$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '" ' . $wrap_attrs . '>
					<div class="zan-dc-inner">
						<' . $form_tag . ' method="post" action="' . esc_url( $action_url ) . '" class="zan-dc-form" id="' . esc_attr( $form_id ) . '" name="zan_dc_form" autocomplete="off">
							<div class="zan-dc-input-wrap">
								<input type="hidden" name="zan_dc_nonce" value="' . esc_attr( $dc_nonce ) . '" />
								<div class="part-input">
									<input type="text" class="zan-dc-input" name="zandomainchecker" placeholder="' . esc_attr( $placeholder_text ) . '" ' . $input_style . ' />
									<div class="dropdown-wrap">
										' . $tld_exts_dropdown_html . '
									</div><!-- /.dropdown-wrap -->
								</div><!-- /.part-input -->
								<div class="part-btn">
									' . $btn_html . '
								</div><!-- /.part-btn -->
							</div><!-- /.zan-dc-input-wrap -->
							' . $recaptcha_html . '
						</' . $form_tag . '>
					</div>
				</div>';
	}

	return $html;

}

add_shortcode( 'zandomainchecker', 'zan_dc_form' );


/**
 * Domain checker form using global settings
 */
function zan_dc_form_global( $atts ) {

	extract(
		shortcode_atts(
			array(
				'nodiv' => 'no',
			), $atts
		)
	);

	$tld_exts = zan_dc_get_option( 'zandc_tld_exts', '' );
	$max_num_of_exts = max( 1, intval( zan_dc_get_option( 'zandc_max_num_of_exts', 5 ) ) );
	$search_input_placeholder = zan_dc_get_option( 'zandc_search_input_placeholder', esc_html__( 'Search domain', 'zandc' ) );
	$show_search_btn = zan_dc_get_option( 'zandc_show_search_btn', 'yes' );
	$search_btn_text = zan_dc_get_option( 'zandc_search_btn_text', esc_html__( 'Search', 'zandc' ) );
	$show_whois_in = zan_dc_get_option( 'zandc_show_whois_in', 'popup' );
	$whois_page = zan_dc_get_option( 'zandc_whois_page', '' );
	$avai_result_message = zan_dc_get_option( 'zandc_avai_result_message', esc_html__( 'Congratulations! {domain} is available.', 'zandc' ) );
	$not_avai_result_message = zan_dc_get_option( 'zandc_not_avai_result_message', esc_html__( 'Sorry! {domain} is already taken.', 'zandc' ) );
	$not_supported_tld_ext = zan_dc_get_option( 'zandc_not_supported_tld_ext', esc_html__( 'Sorry, currently there is WHOIS server for this TLD extension: {ext}', 'zandc' ) );
	$integrate_with = zan_dc_get_option( 'zandc_integrate_with', 'disable' );
	$tld_exts_integrated_with_wc_products = zan_dc_get_option( 'zandc_tld_exts_integrated_with_wc_products', '' );
	$wc_integration_btn_text = zan_dc_get_option( 'zandc_wc_integration_btn_text', esc_html__( 'Add To Cart', 'zandc' ) );
	$integration_link = zan_dc_get_option( 'zandc_integration_link', '' );
	$integration_link_text = zan_dc_get_option( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) );
	$enable_recaptcha = zan_dc_get_option( 'zandc_enable_recaptcha', 'no' );
	$recaptcha_site_key = zan_dc_get_option( 'zandc_recaptcha_site_key', '' );
	$recaptcha_secret_key = zan_dc_get_option( 'zandc_recaptcha_secret_key', '' );
	$enable_instant_domain_search = zan_dc_get_option( 'zandc_enable_instant_domain_search', 'no' );
	$try_faster_checking = zan_dc_get_option( 'zandc_try_faster_checking', 'no' );
	$try_country_detection = zan_dc_get_option( 'zandc_try_country_detection', 'yes' );
	$remove_form_wrap_on_instant_search = zan_dc_get_option( 'zandc_remove_form_wrap', false );


	$dc_nonce = wp_create_nonce( 'domain-check-availability' );
	$form_id = uniqid( 'zan_dc_form_' );
	$action_url = ZANDC_BASE_URL . 'core/shortcodes/domainchecker.php';

	$html = '';
	$btn_html = '';
	$recaptcha_html = '';

	$wrap_class = 'zan-dc-wrap ';
	$wrap_attrs = '';

	if ( $enable_recaptcha == 'yes' && trim( $recaptcha_site_key ) != '' && trim( $recaptcha_secret_key ) != '' ) {
		$wrap_class .= ' has-recaptcha';
		$recaptcha_html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $recaptcha_site_key ) . '"></div>';
		$enable_instant_domain_search = 'no'; // Not support instant search when reCAPTCHA is enabled
		$try_faster_checking = 'no';
		$try_country_detection = 'no';
	}

	if ( trim( $show_search_btn ) == 'yes' ) {
		$wrap_class .= ' has-submit-btn';
		$btn_html .= '<button type="submit" name="zan-dc-submit" class="zan-dc-submit-btn hover-effect-crossing"><span class="zan-dc-btn-icon"></span>' . esc_attr( $search_btn_text ) . '</button>';
	}

	if ( $enable_instant_domain_search == 'yes' ) {
		$wrap_class .= ' is-instant-search';
	}

	if ( $try_faster_checking == 'yes' ) {
		$wrap_class .= ' try-faster-checking';
	}

	if ( $try_country_detection == 'yes' ) {
		$wrap_class .= ' try-country_detection';
		$country_code = zandc_ip_info( 'Visitor', 'Country Code' );
		$wrap_attrs .= ' data-country-code="' . esc_attr( strtolower( $country_code ) ) . '"';
	}

	$div = ( $nodiv == 'no' ) ? 'div' : 'span';

	$html = '<' . $div . ' data-nodiv="' . esc_attr( $nodiv ) . '" class="' . esc_attr( $wrap_class ) . '" ' . $wrap_attrs . '>
					<' . $div . ' class="zan-dc-inner">
						<form method="post" action="' . esc_url( $action_url ) . '" class="zan-dc-form" id="' . esc_attr( $form_id ) . '" name="zan_dc_form" autocomplete="off">
							<' . $div . ' class="zan-dc-input-wrap">
								<input type="hidden" name="zan_dc_nonce" value="' . esc_attr( $dc_nonce ) . '" />
								<input type="text" class="zan-dc-input" name="zandomainchecker" placeholder="' . esc_attr( $search_input_placeholder ) . '" />
								' . $btn_html . '
							</' . $div . '><!-- /.zan-dc-input-wrap -->
							' . $recaptcha_html . '
						</form>
					</' . $div . '>
				</' . $div . '><!-- /.zan-dc-wrap -->';

	if ( $enable_instant_domain_search == 'yes' && $remove_form_wrap_on_instant_search ) {
		$html = '<' . $div . ' data-nodiv="' . esc_attr( $nodiv ) . '" class="' . esc_attr( $wrap_class ) . '" ' . $wrap_attrs . '>
					<' . $div . ' class="zan-dc-inner">
						<' . $div . ' method="post" class="zan-dc-form" id="' . esc_attr( $form_id ) . '" name="zan_dc_form" autocomplete="off">
							<' . $div . ' class="zan-dc-input-wrap">
								<input type="hidden" name="zan_dc_nonce" value="' . esc_attr( $dc_nonce ) . '" />
								<input type="text" class="zan-dc-input" name="zandomainchecker" placeholder="' . esc_attr( $search_input_placeholder ) . '" />
								' . $btn_html . '
							</' . $div . '><!-- /.zan-dc-input-wrap -->
							' . $recaptcha_html . '
						</' . $div . '>
					</' . $div . '>
				</' . $div . '><!-- /.zan-dc-wrap -->';
	}

	return $html;


}

add_shortcode( 'zandc', 'zan_dc_form_global' );
add_shortcode( 'idc', 'zan_dc_form_global' );