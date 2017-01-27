<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

function zan_dc_options_framework_location_override() {
	//return array( ZANDC_CORE . 'options.php' );
}

/**
 * Simple check for validating a URL, it must start with http:// or https://.
 * and pass FILTER_VALIDATE_URL validation.
 *
 * @param  string $url
 *
 * @return bool
 */
function zan_dc_is_valid_url( $url ) {

	// Must start with http:// or https://
	if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		return false;
	}

	// Must pass validation
	if ( !filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	return true;
}

/*
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * This code allows the theme to work without errors if the Options Framework plugin has been disabled.
 */

if ( !function_exists( 'zan_dc_get_option' ) ) {
	function zan_dc_get_option( $name, $default = false ) {

		global $zandc;

		$option_val = isset( $zandc[$name] ) ? $zandc[$name] : $default;

		return $option_val;


		//		$optionsframework_settings = get_option( 'optionsframework' );
		//
		//		// Gets the unique option id
		//		$option_name = $optionsframework_settings['id'];
		//
		//		if ( get_option( $option_name ) ) {
		//			$options = get_option( $option_name );
		//		}
		//
		//		if ( isset( $options[$name] ) ) {
		//			return $options[$name];
		//		}
		//		else {
		//			return $default;
		//		}
	}
}

if ( !function_exists( 'zan_dc_get_option_prev_version' ) ) {
	function zan_dc_get_option_prev_version( $name, $default = false ) {

		$optionsframework_settings = get_option( 'optionsframework' );

		// Gets the unique option id
		$option_name = $optionsframework_settings['id'];

		if ( get_option( $option_name ) ) {
			$options = get_option( $option_name );
		}

		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}
		else {
			return $default;
		}
	}
}


//add_filter( 'options_framework_location', 'zan_dc_options_framework_location_override' );

function zan_dc_optionscheck_options_menu_params( $menu ) {

	$menu['mode'] = 'menu';
	$menu['page_title'] = esc_attr__( 'Zan Domain Checker', 'zandc' );
	$menu['menu_title'] = esc_attr__( 'Zan Domain Checker', 'zandc' );
	$menu['menu_slug'] = 'zan-domain-checker';
	$menu['icon_url'] = 'dashicons-admin-site';

	return $menu;
}

//add_filter( 'optionsframework_menu', 'zan_dc_optionscheck_options_menu_params' );

/**
 * @param null   $ip
 * @param string $purpose
 * @param bool   $deep_detect
 *
 * @return array|null|string
 *
 * How to use 1:
 * echo zandc_ip_info("Visitor", "Country"); // India
 * echo zandc_ip_info("Visitor", "Country Code"); // IN
 * echo zandc_ip_info("Visitor", "State"); // Andhra Pradesh
 * echo zandc_ip_info("Visitor", "City"); // Proddatur
 * echo zandc_ip_info("Visitor", "Address"); // Proddatur, Andhra Pradesh, India
 *
 * print_r(zandc_ip_info("Visitor", "Location")); // Array ( [city] => Proddatur [state] => Andhra Pradesh [country] =>
 * India
 * [country_code] => IN [continent] => Asia [continent_code] => AS )
 *
 * How to use 2:
 * echo zandc_ip_info("173.252.110.27", "Country"); // United States
 * echo zandc_ip_info("173.252.110.27", "Country Code"); // US
 * echo zandc_ip_info("173.252.110.27", "State"); // California
 * echo zandc_ip_info("173.252.110.27", "City"); // Menlo Park
 * echo zandc_ip_info("173.252.110.27", "Address"); // Menlo Park, California, United States
 *
 * print_r(zandc_ip_info("173.252.110.27", "Location")); // Array ( [city] => Menlo Park [state] => California
 * [country] => United States [country_code] => US [continent] => North America [continent_code] => NA )
 *
 */
function zandc_ip_info( $ip = null, $purpose = "location", $deep_detect = true ) {
	$output = null;
	if ( filter_var( $ip, FILTER_VALIDATE_IP ) === false ) {
		$ip = $_SERVER["REMOTE_ADDR"];
		if ( $deep_detect ) {
			if ( filter_var( @$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) )
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if ( filter_var( @$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ) )
				$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
	}
	$purpose = str_replace( array( "name", "\n", "\t", " ", "-", "_" ), null, strtolower( trim( $purpose ) ) );
	$support = array( "country", "countrycode", "state", "region", "city", "location", "address" );
	$continents = array(
		"AF" => "Africa",
		"AN" => "Antarctica",
		"AS" => "Asia",
		"EU" => "Europe",
		"OC" => "Australia (Oceania)",
		"NA" => "North America",
		"SA" => "South America"
	);
	if ( filter_var( $ip, FILTER_VALIDATE_IP ) && in_array( $purpose, $support ) ) {
		$ipdat = @json_decode( file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip ) );
		if ( @strlen( trim( $ipdat->geoplugin_countryCode ) ) == 2 ) {
			switch ( $purpose ) {
				case "location":
					$output = array(
						"city"           => @$ipdat->geoplugin_city,
						"state"          => @$ipdat->geoplugin_regionName,
						"country"        => @$ipdat->geoplugin_countryName,
						"country_code"   => @$ipdat->geoplugin_countryCode,
						"continent"      => @$continents[strtoupper( $ipdat->geoplugin_continentCode )],
						"continent_code" => @$ipdat->geoplugin_continentCode
					);
					break;
				case "address":
					$address = array( $ipdat->geoplugin_countryName );
					if ( @strlen( $ipdat->geoplugin_regionName ) >= 1 )
						$address[] = $ipdat->geoplugin_regionName;
					if ( @strlen( $ipdat->geoplugin_city ) >= 1 )
						$address[] = $ipdat->geoplugin_city;
					$output = implode( ", ", array_reverse( $address ) );
					break;
				case "city":
					$output = @$ipdat->geoplugin_city;
					break;
				case "state":
					$output = @$ipdat->geoplugin_regionName;
					break;
				case "region":
					$output = @$ipdat->geoplugin_regionName;
					break;
				case "country":
					$output = @$ipdat->geoplugin_countryName;
					break;
				case "countrycode":
					$output = @$ipdat->geoplugin_countryCode;
					break;
			}
		}
	}

	return $output;
}

function zan_dc_clean_str( $string = '' ) {

	$string = str_replace( ' ', '-', trim( $string ) ); // Replaces all spaces with hyphens.

	return preg_replace( '/[^a-zA-Z0-9_\.-]/s', '', $string ); // Removes special chars.
}

function zan_dc_is_valid_domain_name( $domain_name ) {
	return ( preg_match( "/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name ) //valid chars check
	         && preg_match( "/^.{1,253}$/", $domain_name ) //overall length check
	         && preg_match( "/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name ) ); //length of each label
}

function zan_dc_domain_check_result() {
	global $zandc;

	$response = array(
		'html'    => '',
		'message' => '',
		'domain'  => '', // Final valid domain
		'err'     => ''
	);

	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
	$domain_name = isset( $_POST['domain_name'] ) ? $_POST['domain_name'] : '';
	$tld_ext = isset( $_POST['tld_ext'] ) ? $_POST['tld_ext'] : '';
	$is_no_div = isset( $_POST['is_no_div'] ) ? $_POST['is_no_div'] == 'yes' : false;
	$recaptcha_response = isset( $_POST['recaptcha_response'] ) ? $_POST['recaptcha_response'] : '';

	// Verify nonce security
	/*
	if ( !wp_verify_nonce( $nonce, 'domain-check-availability' ) ) {
		$response['message'] = '<p class="zan-dc-err-message zan-dc-security-err">' . esc_html__( 'Security check error!', 'zandc' ) . '</p>';
		$response['err'] = 'nonce';
		wp_send_json( $response );
	}
	*/

	$domain_name = zan_dc_clean_str( $domain_name );
	$response['domain'] = $domain_name;

	if ( !zan_dc_is_valid_domain_name( $domain_name ) ) {
		$response['message'] = '<p class="zan-dc-err-message zan-dc-invalid-err">' . esc_html__( 'Invalid domain name!', 'zandc' ) . '</p>';
		$response['err'] = 'nonce';
		wp_send_json( $response );
	}

	// Vefify reCaptcha
	$enable_recaptcha = zan_dc_get_option( 'zandc_enable_recaptcha', 'no' );
	$recaptcha_site_key = zan_dc_get_option( 'zandc_recaptcha_site_key', 'no' );
	$recaptcha_secret_key = zan_dc_get_option( 'zandc_recaptcha_secret_key', 'no' );
	$is_enable_recaptcha = $enable_recaptcha == 'yes' && trim( $recaptcha_site_key ) != '' && trim( $recaptcha_secret_key ) != '';
	if ( $is_enable_recaptcha ) {
		if ( !class_exists( 'ReCaptcha' ) ) {
			require_once ZANDC_DIR_PATH . 'classes/recaptcha/autoload.php';
		}

		$recaptcha = new \ReCaptcha\ReCaptcha( $recaptcha_secret_key );
		// If file_get_contents() is locked down on your PHP installation to disallow
		// its use with URLs, then you can use the alternative request method instead.
		// This makes use of fsockopen() instead.
		// $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\SocketPost());

		// Make the call to verify the response and also pass the user's IP address
		$resp = $recaptcha->verify( $recaptcha_response, $_SERVER['REMOTE_ADDR'] );

		if ( $resp->isSuccess() ) {
			// Do nothing
		}
		else {
			// reCaptcha checking fail.
			$response['message'] = '<p class="zan-dc-err-message zan-dc-recaptcha-err">' . esc_html__( 'Please verify the captcha!', 'zandc' ) . '</p>';
			$response['err'] = 'recaptcha';
			wp_send_json( $response );
		}

	}

	// Prepare domain
	$domain_name = str_replace( 'www.', '', $domain_name );
	if ( zan_dc_is_valid_url( $domain_name ) ) {
		$parse = parse_url( $domain_name );
		$domain_name = $parse['host'];
	}

	if ( trim( $domain_name ) == '' ) {
		$response['message'] = '<p class="zan-dc-err-message zan-dc-invalid-domain-err">' . esc_html__( 'Invalid domain!', 'zandc' ) . '</p>';
		$response['err'] = 'domain_empty';
		wp_send_json( $response );
	}

	$response['domain'] = $domain_name;
	$domain_name .= trim( $tld_ext ) != '' ? '.' . trim( $tld_ext ) : '';

	// Get domain extension
	$domain_ext = '';
	$domain_name_args = array_filter( explode( '.', $domain_name ) );
	if ( count( $domain_name_args ) > 1 && trim( $domain_ext ) == '' ) {
		$domain_ext = isset( $domain_name_args[count( $domain_name_args ) - 1] ) ? $domain_name_args[count( $domain_name_args ) - 1] : '';
	}

	$list_of_domains_to_check = array();

	// Check all available extensions or only one extension
	$exts_need_check = array();
	if ( trim( $domain_ext ) == '' ) {
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
				$list_of_domains_to_check[] = $domain_name . '.' . $ext;
				$exts_need_check[] = $ext;
			}
		}

	}
	else {
		// Check only current extension
		$list_of_domains_to_check[] = $domain_name;
		$exts_need_check[] = trim( $domain_ext );
	}

	if ( !empty( $list_of_domains_to_check ) ) {

		$avai_result_message = zan_dc_get_option( 'zandc_avai_result_message', esc_html__( 'Congratulations! {domain} is available.', 'zandc' ) );
		$not_avai_result_message = zan_dc_get_option( 'zandc_not_avai_result_message', esc_html__( 'Sorry! {domain} is already taken.', 'zandc' ) );
		$not_supported_tld_ext = zan_dc_get_option( 'zandc_not_supported_tld_ext', esc_html__( 'Sorry, currently there is WHOIS server for this TLD extension: {ext}', 'zandc' ) );
		$max_num_of_exts = max( 1, intval( zan_dc_get_option( 'zandc_max_num_of_exts', 5 ) ) );
		$show_whois_in = zan_dc_get_option( 'zandc_show_whois_in', 'popup' );
		$integrate_with = zan_dc_get_option( 'zandc_integrate_with', 'disable' );
		$tld_exts_integrated_with_wc_products = zan_dc_get_option( 'zandc_tld_exts_integrated_with_wc_products', '' );

		$result_item_tag = $is_no_div ? 'span' : 'div';

		$integrate_link_html = '';
		$integrate_text = esc_html__( 'Order', 'zandc' );
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
				}
			}
		}

		$whmsc_integration_link_ok = false; // This variable to check $integrate_link is not null
		if ( $integrate_with == 'whmcs' ) {
			$integrate_text = zan_dc_get_option( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) );
			$integrate_link = zan_dc_get_option( 'zandc_integration_link', '' );
			if ( trim( $integrate_link ) != '' ) {
				$whmsc_integration_link_ok = true;
				$integrate_link .= '/cart.php';
				$integrate_link = add_query_arg(
					array(
						'a'      => 'add',
						'domain' => 'register'
					),
					$integrate_link
				);
				$response['html'] .= '<form method="post" name="whmcs" id="whmcs" class="zan-dc-whmsc-integration-form hidden" target="_blank" action="' . esc_url( $integrate_link ) . '">
				<input type="hidden" name="domains[]" class="zan-dc-whmcs-domain-name-hidden" value="{domain}" >
				<input type="hidden" name="domainsregperiod[{domain}]" class="zan-dc-domainsregperiod-hidden" value="1">
				</form>';
			}
		}

		$custom_integration_link_ok = false;
		if ( $integrate_with == 'link' ) {
			$integrate_text = zan_dc_get_option( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) );
			$integrate_link = zan_dc_get_option( 'zandc_integration_link', '' );
			if ( trim( $integrate_link ) != '' ) {
				$custom_integration_link_ok = true;
				$link_target = isset( $zandc['zandc_integration_link_open_new_tab'] ) ? $zandc['zandc_integration_link_open_new_tab'] != 1 ? '_self' : '_blank' : '_blank';
				$integrate_link_html = '<a href="' . esc_url( $integrate_link ) . '" target="' . $link_target . '" class="zan-dc-integrate-link zan-dc-integrate-custom-link zan-dc-btn hover-overlay-light-to-dark-ltr">' . sanitize_text_field( $integrate_text ) . '</a>';
			}
		}

		if ( !class_exists( 'DomainAvailability' ) ) {
			require_once ZANDC_DIR_PATH . 'classes/DomainAvailability.php';
		}
		$Domains = new DomainAvailability();

		$whois_btn_text = isset( $zandc['zandc_whois_btn_text'] ) ? esc_html( $zandc['zandc_whois_btn_text'] ) : esc_html__( 'Whois', 'zandc' );

		$i = 0;
		foreach ( $list_of_domains_to_check as $domain ) {

			$available = json_decode( $Domains->is_available( $domain ) );

			// Domain is invalid
			if ( $available->status == 0 ) {

				$whois_link_html = '';
				switch ( $show_whois_in ) {
					case 'popup':
						$whois_link_html .= '<a href="#" data-domain="' . esc_attr( $domain ) . '" class="zan-dc-whois-link zan-dc-btn zan-dc-whois-popup hover-overlay-light-to-dark-ltr">' . $whois_btn_text . '</a >';
						break;
					case 'custom_page':
						$whois_page_id = max( 0, intval( zan_dc_get_option( 'zandc_whois_page', 0 ) ) );
						if ( $whois_page_id > 0 ) {
							$whois_link = add_query_arg( 'domain', esc_attr( $domain ), get_permalink( $whois_page_id ) );
							$whois_link_html .= '<a href="' . esc_url( $whois_link ) . '" data-domain="' . esc_attr( $domain ) . '" class="zan-dc-whois-link zan-dc-btn zan-dc-whois-page-link hover-overlay-light-to-dark-ltr">' . $whois_btn_text . '</a >';
						}
						break;
				}

				$message = str_replace( '{domain}', '<span>' . $domain . '</span>', $not_avai_result_message );
				$response['html'] .= '<' . $result_item_tag . ' class="result-item is-registered">' . balanceTags( $message ) . $whois_link_html . '</' . $result_item_tag . '>';
			}

			// Domain is valid
			elseif ( $available->status == 1 ) {

				if ( $integrate_with == 'woocommerce' && class_exists( 'WooCommerce' ) ) {
					if ( isset( $wc_integrate_link_args[$exts_need_check[$i]] ) ) {
						$integrate_product_id = $wc_integrate_link_args[$exts_need_check[$i]];
						$shop_page_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );

						$integrate_link = add_query_arg(
							array(
								'domain'      => esc_attr( $domain ),
								'add-to-cart' => $integrate_product_id
							),
							$shop_page_url
						);

						$integrate_link_html = '<a class="zan-dc-integrate-link zan-dc-integrate-wc zan-dc-btn hover-overlay-light-to-dark-ltr" href="' . esc_url( $integrate_link ) . '">' . sanitize_text_field( $integrate_text ) . '</a>';
					}
					else {
						$integrate_link_html = '';
					}
				}

				if ( $integrate_with == 'whmcs' && $whmsc_integration_link_ok ) {
					$integrate_link_html = '<a href="#" data-domain="' . esc_attr( $domain ) . '" class="zan-dc-integrate-link zan-dc-integrate-whmcs zan-dc-btn hover-overlay-light-to-dark-ltr">' . sanitize_text_field( $integrate_text ) . '</a>';
				}

				if ( $integrate_with == 'link' && $custom_integration_link_ok ) {
					$integrate_link_html = str_replace( '{domain}', $domain, $integrate_link_html );
				}

				$message = str_replace( '{domain}', '<span>' . $domain . '</span>', $avai_result_message );
				$response['html'] .= '<' . $result_item_tag . ' class="result-item not-registered">' . balanceTags( $message ) . $integrate_link_html . '</' . $result_item_tag . '>';
			}

			// No whois server for this TLD domain
			elseif ( $available->status == 2 ) {
				$message = str_replace( '{ext}', '<span>' . $exts_need_check[$i] . '</span>', $not_supported_tld_ext );
				$response['html'] .= '<' . $result_item_tag . ' class="result-item no-whois-server">' . balanceTags( $message ) . '</' . $result_item_tag . '>';
			}
			$i++;

			// Break if maximum number of TLD ext checking is reached
			if ( $i >= $max_num_of_exts ) {
				break;
			}
		}
	}

	wp_send_json( $response );

	die();
}

add_action( 'wp_ajax_zan_dc_domain_check_result', 'zan_dc_domain_check_result' );
add_action( 'wp_ajax_nopriv_zan_dc_domain_check_result', 'zan_dc_domain_check_result' );

function zan_dc_check_whois_via_ajax() {

	$response = array(
		'html'    => '',
		'message' => '',
		'domain'  => '', // Final valid domain
		'err'     => ''
	);

	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
	$domain_name = isset( $_POST['domain_name'] ) ? $_POST['domain_name'] : '';

	// Verify nonce security
	/*
	if ( !wp_verify_nonce( $nonce, 'domain-check-availability' ) ) {
		$response['message'] = '<p class="zan-dc-err-message zan-dc-security-err">' . esc_html__( 'Security check error!', 'zandc' ) . '</p>';
		$response['err'] = 'nonce';
		wp_send_json( $response );
	}
	*/

	if ( trim( $domain_name ) != '' ) {
		if ( !class_exists( 'Whois' ) ) {
			require_once ZANDC_DIR_PATH . 'classes/whoisClass.php';
		}

		$whois = new Whois;

		if ( function_exists( 'idn_to_ascii' ) ) {
			$domain_name = idn_to_ascii( $domain_name );
		}
		if ( function_exists( 'idn_to_utf8' ) ) {
			$domain_name = idn_to_utf8( $domain_name );
		}

		$whois_title = zan_dc_get_option( 'zandc_whois_title', esc_html__( 'Whois record for {domain}', 'zandc' ) );
		$title = str_replace( '{domain}', $domain_name, $whois_title );
		if ( trim( $title ) != '' ) {
			$response['html'] .= '<h3 class="zan-dc-whois-record-title">' . sanitize_text_field( $title ) . '</h3>';
		}

		$response['html'] .= '<pre class="zan-dc-pre zan-dc-whois-pre">' . $whois->whoislookup( $domain_name ) . '</pre>';

		$response['html'] = '<div class="zan-dc-whois zan-dc-pupup-content">' . $response['html'] . '</div>';

	}

	wp_send_json( $response );

	die();
}

add_action( 'wp_ajax_zan_dc_check_whois_via_ajax', 'zan_dc_check_whois_via_ajax' );
add_action( 'wp_ajax_nopriv_zan_dc_check_whois_via_ajax', 'zan_dc_check_whois_via_ajax' );

// WooCommerce functions ==========================================================
function zan_dc_set_cart_domain_name_on_add_to_cart(
	$cart_item_key, $product_id = null, $quantity = 1, $variation_id = null, $variation = null, $cart_item_data = null
) {
	if ( isset( $_GET['domain'] ) ) {
		WC()->session->set( $cart_item_key . '_zandc_domain', $_GET['domain'] );
	}
}

add_action( 'woocommerce_add_to_cart', 'zan_dc_set_cart_domain_name_on_add_to_cart' );

function zan_dc_render_cart_item_meta( $title = null, $cart_item = null, $cart_item_key = null ) {

	if ( $cart_item_key && is_cart() ) {

		if ( WC()->session->get( $cart_item_key . '_zandc_domain' ) ) {
			$title = $title . '<dl class="zan-dc-dl">
				 <dt class="">' . esc_html__( 'Domain: ', 'zandc' ) . '</dt>
				 <dd class=""><p>' . WC()->session->get( $cart_item_key . '_zandc_domain' ) . '</p></dd>			
			  </dl>';
		}
	}

	return $title;

}

add_filter( 'woocommerce_cart_item_name', 'zan_dc_render_cart_item_meta', 1, 3 );

function zan_dc_render_meta_on_checkout_order_review_item( $quantity = null, $cart_item = null, $cart_item_key = null
) {
	if ( $cart_item_key ) {
		if ( WC()->session->get( $cart_item_key . '_zandc_domain' ) ) {
			return $quantity . '<dl class="zan-dc-dl">
				 <dt class="">' . esc_html__( 'Domain: ', 'zandc' ) . '</dt>
				 <dd class=""><p>' . WC()->session->get( $cart_item_key . '_zandc_domain' ) . '</p></dd>			
			  </dl>';
		}
	}
}

add_filter( 'woocommerce_checkout_cart_item_quantity', 'zan_dc_render_meta_on_checkout_order_review_item', 1, 3 );

function zan_dc_add_order_item_meta( $item_id, $values = null, $cart_item_key ) {
	if ( WC()->session->get( $cart_item_key . '_zandc_domain' ) ) {
		wc_add_order_item_meta( $item_id, "Domain", WC()->session->get( $cart_item_key . '_zandc_domain' ) );
	}
}

add_action( 'woocommerce_add_order_item_meta', 'zan_dc_add_order_item_meta', 1, 3 );

function zan_dc_order_item_name( $item_name, $item = null, $is_visible = false ) {

	if ( isset( $item['item_meta']['Domain'][0] ) ) {
		$item_name = $item_name .= ' : ' . sanitize_text_field( $item['item_meta']['Domain'][0] );
	}

	return $item_name;
}

add_action( 'woocommerce_order_item_name', 'zan_dc_order_item_name', 1, 2 );

function zan_dc_order_item_count( $item_count_text, $the_order = null ) {

	$domain_name = '';
	if ( sizeof( $the_order->get_items() ) > 0 ) {
		foreach ( $the_order->get_items() as $item ) {
			if ( isset( $item['item_meta']['Domain'][0] ) ) {
				$domain_name = $item['item_meta']['Domain'][0];
			}
		}
	}

	if ( trim( $domain_name ) != '' ) {
		$item_count_text .= ' (' . sanitize_text_field( $domain_name ) . ')';
	}

	return $item_count_text;
}

add_action( 'woocommerce_admin_order_item_count', 'zan_dc_order_item_count', 1, 2 );

// End WooCommerce functions ==========================================================