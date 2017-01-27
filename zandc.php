<?php
/*
 * Plugin Name: Instant Domain Checker
 * Plugin URI: http://zandc.zanthemes.net/
 * Description: Just start typing, domain will be checked immediately when you type. Supported WooCommerce integration, WHMCS integration, custom link integration. Supported unlimited TLD domains extensions, ccTLD by country detection. Plugin also supported ajax domain checking, Google reCAPTCHA to protect you from abuse and spam. Compatible with WPML and much more...
 * Author: Le Manh Linh
 * Version: 1.7
 * Author URI: http://zandc.zanthemes.net/
 * Text Domain: zandc
 * Domain Path: languages
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

define( 'ZANDC_VERSION', '1.7' );
define( 'ZANDC_BASE_URL', trailingslashit( plugins_url( 'zandc' ) ) );
define( 'ZANDC_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZANDC_LIBS', ZANDC_DIR_PATH . '/libs/' );
define( 'ZANDC_LIBS_URL', ZANDC_BASE_URL . '/libs/' );
define( 'ZANDC_CORE', ZANDC_DIR_PATH . '/core/' );
define( 'ZANDC_CSS_URL', ZANDC_BASE_URL . 'assets/css/' );
define( 'ZANDC_JS_URL', ZANDC_BASE_URL . 'assets/js/' );
define( 'ZANDC_IMG_URL', ZANDC_BASE_URL . 'assets/images/' );


/*
 * Load Options Framework
 */
if ( !class_exists( 'Options_Framework' ) ) {
	// require_once ZANDC_DIR_PATH . 'options-framework/options-framework.php';
}

/**
 * Load Redux Framework
 */
if ( !class_exists( 'ReduxFramework' ) && file_exists( ZANDC_DIR_PATH . 'reduxframework/ReduxCore/framework.php' ) ) {
	require_once( ZANDC_DIR_PATH . 'reduxframework/ReduxCore/framework.php' );
}

/**
 * Load plugin textdomain
 */
if ( !function_exists( 'zan_dc_load_textdomain' ) ) {
	function zan_dc_load_textdomain() {
		load_plugin_textdomain( 'zandc', false, ZANDC_DIR_PATH . 'languages' );
	}

	add_action( 'plugins_loaded', 'zan_dc_load_textdomain' );
}


if ( !function_exists( 'zdc_load_options' ) ) {
	function zdc_load_options() {
		require_once ZANDC_CORE . 'zandc-options.php';
	}
}
add_action( 'plugins_loaded', 'zdc_load_options' );

function zan_dc_core_enqueue_script() {
	global $zandc;

	$enable_recaptcha = zan_dc_get_option( 'zandc_enable_recaptcha', 'no' );

	wp_register_script( 'jquery.jsonp', ZANDC_JS_URL . 'jquery.jsonp.js', array( 'jquery' ), ZANDC_VERSION, true );
	wp_enqueue_script( 'jquery.jsonp' );

	wp_register_script( 'jquery.typewatch', ZANDC_JS_URL . 'jquery.typewatch.js', array( 'jquery' ), ZANDC_VERSION, true );
	wp_enqueue_script( 'jquery.typewatch' );

	wp_register_script( 'zandc-frontend', ZANDC_JS_URL . 'frontend.js', array( 'jquery' ), ZANDC_VERSION, true );
	wp_enqueue_script( 'zandc-frontend' );

	if ( $enable_recaptcha == 'yes' ) {
		//wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', false, false, true );
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?fallback=true', false, false, true );
	}

	$zandc_localize = array(
		'ajaxurl'                      => esc_url( admin_url( 'admin-ajax.php' ) ),
		'enable_instant_domain_search' => 'no',
		'instant_domain_search_url'    => 'https://instantdomainsearch.com/all/',
		'yahoo_url'                    => 'http://query.yahooapis.com/v1/public/yql'
	);

	// Using SSL Protocol?
	$zandc_localize['using_ssl'] = 'no';
	if ( isset( $zandc['zandc_using_ssl'] ) ) {
		$zandc_localize['using_ssl'] = $zandc['zandc_using_ssl'] == 1 ? 'yes' : 'no';
		$zandc_localize['yahoo_url'] = $zandc['zandc_using_ssl'] == 1 ? 'https://query.yahooapis.com/v1/public/yql' : 'http://query.yahooapis.com/v1/public/yql';
	}

	$whois_btn_text = isset( $zandc['zandc_whois_btn_text'] ) ? esc_html( $zandc['zandc_whois_btn_text'] ) : esc_html__( 'Whois', 'zandc' );
	$zandc_localize['whois_btn_text'] = $whois_btn_text;

	$zandc_localize['show_transfer_btn'] = isset( $zandc['zandc_show_transfer_btn'] ) ? trim( $zandc['zandc_show_transfer_btn'] ) : 'no';
	//$zandc_localize['transfer_exts'] = isset( $zandc['zandc_transfer_exts'] ) ? $zandc['zandc_transfer_exts'] : '';
	$transfer_text = isset( $zandc['zandc_transfer_btn_text'] ) ? esc_html( $zandc['zandc_transfer_btn_text'] ) : esc_html__( 'Transfer', 'zandc' );
	$zandc_localize['transfer_link_html'] = '';
	if ( isset( $zandc['zandc_transfer_link'] ) ) {
		if ( trim( $zandc['zandc_transfer_link'] ) != '' ) {
			$zandc_localize['transfer_link_html'] = '<a href="' . esc_attr( $zandc['zandc_transfer_link'] ) . '" target="_blank" data-domain="{domain}" class="zan-dc-transfer-link zan-dc-btn hover-overlay-light-to-dark-ltr">' . $transfer_text . '</a >';
		}
		else {
			$zandc_localize['show_transfer_btn'] = 'no';
		}
	}

	$check_popular_only = isset( $zandc['zandc_check_popular_only'] ) ? $zandc['zandc_check_popular_only'] == 1 : false;
	$zandc_localize['check_popular_only'] = $check_popular_only ? 'yes' : 'no';

	$list_of_domains_to_check = array();

	// Check all available extensions or only one extension
	$exts_need_check = array();
	// Check all available extensions
	$all_available_exts = zan_dc_get_option( 'zandc_tld_exts' );

	if ( $all_available_exts != '' ) {
		$all_available_exts = explode( '|', $all_available_exts );
	}
	else {
		// Only check .com, .net, .org, .co
		$all_available_exts = array( 'com', 'net', 'org', 'co' );
	}

	foreach ( $all_available_exts as $ext ) {
		$ext = trim( str_replace( '.', '', $ext ) );
		if ( $ext != '' ) {
			$exts_need_check[] = $ext;
		}
	}

	$zandc_localize['all_available_exts'] = $all_available_exts;
	$max_num_of_exts = max( 1, intval( zan_dc_get_option( 'zandc_max_num_of_exts', 5 ) ) );
	$zandc_localize['max_num_of_exts'] = $max_num_of_exts;

	$all_avail_sld_exts = zan_dc_get_option( 'zandc_sld_exts' );

	if ( trim( $all_avail_sld_exts ) != '' ) {
		$all_avail_sld_exts = explode( '|', $all_avail_sld_exts );
	}
	else {
		$all_avail_sld_exts = array();
	}
	$zandc_localize['all_avail_sld_exts'] = $all_avail_sld_exts;

	$zandc_sld_whois_servers = zan_dc_get_option( 'zandc_sld_whois_servers' );
	$zandc_sld_whois_servers = zan_dc_whois_servers_parse_array( $zandc_sld_whois_servers );
	$zandc_localize['sld_whois_servers'] = $zandc_sld_whois_servers;

	$show_sld_results_before_tld_results = zan_dc_get_option( 'zandc_show_sld_results_before_tld_results' ) == 1 ? 'yes' : 'no';
	$zandc_localize['show_sld_results_before_tld_results'] = $show_sld_results_before_tld_results;

	$show_whois_in = zan_dc_get_option( 'zandc_show_whois_in', 'popup' );
	switch ( $show_whois_in ) {
		case 'popup':
			$zandc_localize['whois_link_html'] = '<a href="#" data-domain="{domain}" class="zan-dc-whois-link zan-dc-btn zan-dc-whois-popup hover-overlay-light-to-dark-ltr">' . $whois_btn_text . '</a >';
			break;
		case 'custom_page':
			$whois_page_id = max( 0, intval( zan_dc_get_option( 'zandc_whois_page', 0 ) ) );
			if ( $whois_page_id > 0 ) {
				$whois_link = add_query_arg( 'domain', '{domain}', get_permalink( $whois_page_id ) );
				$zandc_localize['whois_link_html'] = '<a href="' . esc_url( $whois_link ) . '" data-domain="" class="zan-dc-whois-link zan-dc-btn zan-dc-whois-page-link hover-overlay-light-to-dark-ltr">' . $whois_btn_text . '</a >';
			}
			break;
		default:
			$zandc_localize['whois_link_html'] = '';
			break;
	}

	$integrate_with = zan_dc_get_option( 'zandc_integrate_with', 'disable' );
	$tld_exts_integrated_with_wc_products = zan_dc_get_option( 'zandc_tld_exts_integrated_with_wc_products', '' );
	$zandc_localize['integrate_with'] = $integrate_with;
	$zandc_localize['integrate_link_html'] = ''; // For "whmcs" and "link" (and custom link)
	$integration_link_target = '_blank';
	if ( isset( $zandc['zandc_integration_link_open_new_tab'] ) ) {
		if ( $zandc['zandc_integration_link_open_new_tab'] != 1 ) {
			$integration_link_target = '_self';
		}
	}
	if ( $integrate_with == 'whmcs' ) {
		$integrate_text = zan_dc_get_option( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) );
		$integrate_link = zan_dc_get_option( 'zandc_integration_link', '' );
		if ( trim( $integrate_link ) != '' ) {
			$integrate_link .= '/cart.php';
			$integrate_link = add_query_arg(
				array(
					'a'      => 'add',
					'domain' => 'register'
				),
				$integrate_link
			);
			$zandc_localize['whmcs_form'] = '<form method="post" name="whmcs" id="whmcs" class="zan-dc-whmsc-integration-form hidden" target="_blank" action="' . esc_url( $integrate_link ) . '">
				<input type="hidden" name="domains[]" class="zan-dc-whmcs-domain-name-hidden" value="{domain}" >
				<input type="hidden" name="domainsregperiod[{domain}]" class="zan-dc-domainsregperiod-hidden" value="1">
				</form>';
			$integrate_link_html = '<a href="#" data-domain="{domain}" class="zan-dc-integrate-link zan-dc-integrate-whmcs zan-dc-btn hover-overlay-light-to-dark-ltr">' . sanitize_text_field( $integrate_text ) . '</a>';
			$zandc_localize['integrate_link_html'] = $integrate_link_html;
		}
	}

	if ( $integrate_with == 'link' ) {
		$integrate_text = zan_dc_get_option( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) );
		$integrate_link = zan_dc_get_option( 'zandc_integration_link', '' );
		if ( trim( $integrate_link ) != '' ) {
			$integrate_link_html = '<a href="' . esc_attr( $integrate_link ) . '" target="' . esc_attr( $integration_link_target ) . '" class="zan-dc-integrate-link zan-dc-integrate-custom-link zan-dc-btn hover-overlay-light-to-dark-ltr">' . sanitize_text_field( $integrate_text ) . '</a>';
			$zandc_localize['integrate_link_html'] = $integrate_link_html;
		}
	}

	$wc_integrate_link_args = array();
	if ( $integrate_with == 'woocommerce' && class_exists( 'WooCommerce' ) ) {

		if ( trim( $tld_exts_integrated_with_wc_products ) != '' ) {
			$tld_exts_integrated_with_wc_products = array_filter( explode( '|', $tld_exts_integrated_with_wc_products ) );
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
					if ( $product_id > 0 && trim( $domain_ext ) != '' ) {
						$wc_integrate_link_args[$domain_ext] = $product_id;
					}
				}
				$integrate_text = zan_dc_get_option( 'zandc_wc_integration_btn_text', esc_html__( 'Add To Cart', 'zandc' ) );

				foreach ( $exts_need_check as $ext_need_check ) {
					if ( isset( $wc_integrate_link_args[$ext_need_check] ) ) {
						$integrate_product_id = $wc_integrate_link_args[$ext_need_check];
						$shop_page_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );

						$integrate_link = add_query_arg(
							array(
								'domain'      => '{domain}',
								'add-to-cart' => $integrate_product_id
							),
							$shop_page_url
						);

						$wc_integrate_link_args[$ext_need_check] = '<a class="zan-dc-integrate-link zan-dc-integrate-wc zan-dc-btn hover-overlay-light-to-dark-ltr" href="' . esc_attr( $integrate_link ) . '">' . sanitize_text_field( $integrate_text ) . '</a>';
					}
					else {
						// Do nothing
						// $integrate_link_html = '';
					}
				}

			}
		}
	}
	$zandc_localize['wc_integrate_link_args'] = $wc_integrate_link_args;

	$avai_result_message = zan_dc_get_option( 'zandc_avai_result_message', esc_html__( 'Congratulations! {domain} is available.', 'zandc' ) );
	$not_avai_result_message = zan_dc_get_option( 'zandc_not_avai_result_message', esc_html__( 'Sorry! {domain} is already taken.', 'zandc' ) );
	$zandc_localize['avai_result_message'] = $avai_result_message;
	$zandc_localize['not_avai_result_message'] = $not_avai_result_message;

	$enable_instant_domain_search = zan_dc_get_option( 'zandc_enable_instant_domain_search', 'no' );
	$zandc_localize['enable_instant_domain_search'] = $enable_instant_domain_search;

	wp_localize_script( 'zandc-frontend', 'zandc', $zandc_localize );

	wp_register_style( 'zandc-frontend-style', ZANDC_CSS_URL . 'frontend-style.css', false, ZANDC_VERSION, 'all' );
	wp_enqueue_style( 'zandc-frontend-style' );

	$custom_css = zan_dc_get_custom_css();
	if ( trim( $custom_css ) != '' ) {
		wp_add_inline_style( 'zandc-frontend-style', $custom_css );
	}

}

function zan_dc_whois_servers_parse_array( $str = '' ) {
	global $zandc;
	$ret = array();
	if ( trim( $str ) == '' ) {
		return $ret;
	}

	$str_args = preg_split( '/\r\n|[\r\n]/', $str );

	$whois_btn_text = isset( $zandc['zandc_whois_btn_text'] ) ? esc_html( $zandc['zandc_whois_btn_text'] ) : esc_html__( 'Whois', 'zandc' );

	if ( !empty( $str_args ) ) {
		foreach ( $str_args as $str_arg ) {
			$str_arg = explode( '|', $str_arg );
			if ( isset( $str_arg[0] ) && isset( $str_arg[1] ) ) {
				$ret[$str_arg[1]] = $str_arg[0];
				$ret[$str_arg[1]] = '<a href="' . esc_attr( $str_arg[0] ) . '" target="_blank" data-domain="" class="zan-dc-whois-link zan-dc-btn zan-dc-whois-sld-link hover-overlay-light-to-dark-ltr">' . $whois_btn_text . '</a >';
			}
		}
	}

	return $ret;
}

add_action( 'wp_enqueue_scripts', 'zan_dc_core_enqueue_script' );

function zan_dc_get_custom_css() {
	global $zandc;

	$custom_css = isset( $zandc['custom_css_code'] ) ? $zandc['custom_css_code'] : '';

	return $custom_css;
}

function zan_dc_core_enqueue_admin_script() {

	$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';

	wp_register_script( 'zandc-backend', ZANDC_JS_URL . 'backend.js', array( 'jquery' ), ZANDC_VERSION, true );
	wp_enqueue_script( 'zandc-backend' );

	if ( $page == 'zandc_options' ) {
		wp_register_style( 'zandc-redux', ZANDC_CSS_URL . 'redux.css', array(), ZANDC_VERSION, 'all' );
		wp_enqueue_style( 'zandc-redux' );
	}

	wp_register_style( 'zandc-backend-style', ZANDC_CSS_URL . 'backend-style.css', false, ZANDC_VERSION, 'all' );
	wp_enqueue_style( 'zandc-backend-style' );
}

add_action( 'admin_enqueue_scripts', 'zan_dc_core_enqueue_admin_script' );

/*
 * Load whois servers
 */
require_once ZANDC_CORE . 'whois-servers.php';

/*
 * Load Instant Domain Checker functions
 */
require_once ZANDC_CORE . 'functions.php';

/*
 * Load shortcodes
 */
require_once ZANDC_CORE . 'shortcodes/domainchecker.php';
require_once ZANDC_CORE . 'shortcodes/whois.php';

