<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

function zan_dc_whois( $atts ) {

	$whois_title = zan_dc_get_option( 'zandc_whois_title', esc_html__( 'Whois record for {domain}', 'zandc' ) );

	extract(
		shortcode_atts(
			array(
				'title' => $whois_title, // The {domain} will be replaced with domain name
				'pre'   => 'yes',
			), $atts
		)
	);

	$html = '';

	$domain = isset( $_GET['domain'] ) ? $_GET['domain'] : '';
	if ( trim( $domain ) == '' ) {
		$domain = isset( $_POST['domain'] ) ? $_POST['domain'] : '';
	}

	if ( trim( $domain ) != '' ) {
		if ( !class_exists( 'Whois' ) ) {
			require_once ZANDC_DIR_PATH . 'classes/whoisClass.php';
		}

		$whois = new Whois;

		if ( function_exists( 'idn_to_ascii' ) ) {
			$domain = idn_to_ascii( $domain );
		}
		if ( function_exists( 'idn_to_utf8' ) ) {
			$domain = idn_to_utf8( $domain );
		}

		$title = str_replace( '{domain}', $domain, $title );
		if ( trim( $title ) != '' ) {
			$html .= '<h3 class="zan-dc-whois-record-title">' . sanitize_text_field( $title ) . '</h3>';
		}

		if ( $pre == 'yes' ) {
			$html .= '<pre class="zan-dc-pre zan-dc-whois-pre">' . $whois->whoislookup( $domain ) . '</pre>';
		}
		else {
			$html .= '<div class="zan-dc-whois">' . $whois->whoislookup( $domain ) . '</div>';
		}

		$html = '<div class="zan-dc-whois-wrap">' . $html . '</div>';

	}

	return $html;
}

add_shortcode( 'zwhois', 'zan_dc_whois' );
add_shortcode( 'zdcwhois', 'zan_dc_whois' );
add_shortcode( 'idcwhois', 'zan_dc_whois' );