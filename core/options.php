<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace( "/\W/", "_", strtolower( $themename ) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );

	// echo $themename;
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one'   => __( 'One', 'options_check' ),
		'two'   => __( 'Two', 'options_check' ),
		'three' => __( 'Three', 'options_check' ),
		'four'  => __( 'Four', 'options_check' ),
		'five'  => __( 'Five', 'options_check' )
	);

	// Multicheck Array
	$multicheck_array = array(
		'one'   => __( 'French Toast', 'options_check' ),
		'two'   => __( 'Pancake', 'options_check' ),
		'three' => __( 'Omelette', 'options_check' ),
		'four'  => __( 'Crepe', 'options_check' ),
		'five'  => __( 'Waffle', 'options_check' )
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one'  => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color'      => '',
		'image'      => '',
		'repeat'     => 'repeat',
		'position'   => 'top center',
		'attachment' => 'scroll'
	);

	// Typography Defaults
	$typography_defaults = array(
		'size'  => '15px',
		'face'  => 'georgia',
		'style' => 'bold',
		'color' => '#bada55'
	);

	// Typography Options
	$typography_options = array(
		'sizes'  => array( '6', '12', '14', '16', '20' ),
		'faces'  => array( 'Helvetica Neue' => 'Helvetica Neue', 'Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal', 'bold' => 'Bold' ),
		'color'  => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	if ( isset( $options_categories_obj ) ) {
		foreach ( $options_categories_obj as $category ) {
			$options_categories[$category->cat_ID] = $category->cat_name;
		}
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	if ( isset( $options_tags_obj ) ) {
		foreach ( $options_tags_obj as $tag ) {
			$options_tags[$tag->term_id] = $tag->name;
		}
	}

	// If using image radio buttons, define a directory path
	$imagepath = get_template_directory_uri() . '/images/';

	// Zan Domain Checker Start ============================

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages( 'sort_column=post_parent,menu_order' );
	if ( isset( $options_pages_obj ) ) {
		$options_pages[''] = esc_html__( ' ----- Select a page ----- ', 'zandc' );
		foreach ( $options_pages_obj as $page ) {
			$options_pages[$page->ID] = $page->post_title;
		}
	}
	else {
		$options_pages[''] = esc_html__( ' ----- There is no page to select ----- ', 'zandc' );
	}

	$options = array();

	$options[] = array(
		'name' => esc_html__( 'General Settings', 'zandc' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__( 'Top Level Domain Extensions', 'zandc' ),
		'desc' => esc_html__( 'List of supported TLD extensions, each extension is separated by a vertical stripe. Ex: com|org|net|us|jp. Empty list means all extensions are allowed.', 'zandc' ),
		'id'   => 'zandc_tld_exts',
		'std'  => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__( 'Maximum Results', 'zandc' ),
		'desc' => esc_html__( 'Maximum number of checking results. Ex: 5', 'zandc' ),
		'id'   => 'zandc_max_num_of_exts',
		'std'  => 5,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__( 'Placeholder Text', 'zandc' ),
		'desc' => esc_html__( 'Search input placeholder text.', 'zandc' ),
		'id'   => 'zandc_search_input_placeholder',
		'std'  => esc_html__( 'Search domain', 'zandc' ),
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__( 'Show Search Button', 'zandc' ),
		'id'      => 'zandc_show_search_btn',
		'std'     => 'yes',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'yes' => esc_html__( 'Yes', 'zandc' ),
			'no'  => esc_html__( 'No', 'zandc' )
		)
	);

	$options[] = array(
		'name'  => esc_html__( 'Search Button Text', 'zandc' ),
		'id'    => 'zandc_search_btn_text',
		'std'   => esc_html__( 'Search', 'zandc' ),
		'type'  => 'text',
		'class' => 'mini', //mini, tiny, small
	);

	$options[] = array(
		'name'    => esc_html__( 'Show Whois In', 'zandc' ),
		'id'      => 'zandc_show_whois_in',
		'std'     => 'popup',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'disable'     => esc_html__( 'Disable', 'zandc' ),
			'popup'       => esc_html__( 'Popup', 'zandc' ),
			'custom_page' => esc_html__( 'Custom Page', 'zandc' )
		)
	);

	$options[] = array(
		'name'    => esc_html__( 'Whois Page', 'zandc' ),
		'id'      => 'zandc_whois_page',
		'std'     => '',
		'type'    => 'select',
		'class'   => 'small', //mini, tiny, small
		'options' => $options_pages,
		'desc'    => esc_html__( 'Whois page should contain shortcode: [zwhois]', 'zandc' )
	);

	$options[] = array(
		'name'  => esc_html__( 'Whois Title', 'zandc' ),
		'id'    => 'zandc_whois_title',
		'std'   => esc_html__( 'Whois record for {domain}', 'zandc' ),
		'desc'  => esc_html__( 'Default: Whois record for {domain}. The {domain} will be replaced by domain name.', 'zandc' ),
		'type'  => 'text',
		'class' => 'small', //mini, tiny, small
	);

	$options[] = array(
		'name' => esc_html__( 'Available Result Message', 'zandc' ),
		'desc' => esc_html__( 'Edit the available result message or leave it empty to use default message. The {domain} will be replaced by domain name.', 'zandc' ),
		'id'   => 'zandc_avai_result_message',
		'std'  => esc_html__( 'The domain {domain} is not registered', 'zandc' ),
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__( 'Not Available Result Message', 'zandc' ),
		'desc' => esc_html__( 'Edit the message or leave it empty to use default message. The {domain} will be replaced by domain name.', 'zandc' ),
		'id'   => 'zandc_not_avai_result_message',
		'std'  => esc_html__( 'The domain {domain} is registered', 'zandc' ),
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__( 'Not Supported TLD Extensions Messages', 'zandc' ),
		'desc' => esc_html__( 'Edit the message or leave it empty to use default message. The {ext} will be replaced by TLD extension.', 'zandc' ),
		'id'   => 'zandc_not_supported_tld_ext',
		'std'  => esc_html__( 'Sorry, currently there is WHOIS server for this TLD extension: {ext}', 'zandc' ),
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__( 'Integration', 'zandc' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name'    => esc_html__( 'Integration With', 'zandc' ),
		'id'      => 'zandc_integrate_with',
		'std'     => 'disable',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'disable'     => esc_html__( 'Disable', 'zandc' ),
			'woocommerce' => esc_html__( 'WooCommerce', 'zandc' ),
			'whmcs'       => esc_html__( 'WHMCS', 'zandc' ),
			'link'        => esc_html__( 'Link', 'zandc' )
		)
	);

	$options[] = array(
		'name' => esc_html__( 'Top Level Domain Extensions Integrated With Products', 'zandc' ),
		'desc' => esc_html__( 'List of TLD extensions integration with products, each extension and product id is separated by a vertical stripe. {ext1}-{product_id1}|{ext2}-{product_id2}|{ext3}-{product_id3}. Ex: com-23|org-18|net-65|us|jp-674.', 'zandc' ),
		'id'   => 'zandc_tld_exts_integrated_with_wc_products',
		'std'  => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name'  => esc_html__( 'WooCommerce Integration Button Text', 'zandc' ),
		'id'    => 'zandc_wc_integration_btn_text',
		'std'   => esc_html__( 'Add To Cart', 'zandc' ),
		'type'  => 'text',
		'class' => 'mini', //mini, tiny, small
	);

	$options[] = array(
		'name'  => esc_html__( 'Integration Link', 'zandc' ),
		'id'    => 'zandc_integration_link',
		'std'   => '',
		'desc'  => esc_html__( 'Put the custom link you want integration. The {domain} will be replaced by domain name. Ex for WHMSC: http://biling.yourhosturl.com. Ex for custom link: http://zanthemes.net/?regdomain={domain}', 'zandc' ),
		'type'  => 'text',
		'class' => 'small', //mini, tiny, small
	);

	$options[] = array(
		'name'  => esc_html__( 'Integration Link Text', 'zandc' ),
		'id'    => 'zandc_integration_link_text',
		'std'   => esc_html__( 'Order', 'zandc' ),
		'type'  => 'text',
		'class' => 'mini', //mini, tiny, small
	);

	$options[] = array(
		'name' => esc_html__( 'Google reCAPTCHA', 'zandc' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name'    => esc_html__( 'Enable reCAPTCHA', 'zandc' ),
		'id'      => 'zandc_enable_recaptcha',
		'std'     => 'no',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'yes' => esc_html__( 'Yes', 'zandc' ),
			'no'  => esc_html__( 'No', 'zandc' )
		),
		'desc'    => esc_html__( 'reCAPTCHA is a free service from Google that helps protect websites from spam and abuse. A “CAPTCHA” is a test to tell human and bots apart. It is easy for humans to solve, but hard for “bots” and other malicious software to figure out.', 'zandc' )
	);

	$options[] = array(
		'name'  => esc_html__( 'reCaptcha Site Key', 'zandc' ),
		'id'    => 'zandc_recaptcha_site_key',
		'std'   => '',
		'type'  => 'text',
		'class' => 'small', //mini, tiny, small
		'desc'  => wp_kses(
			__( '<a href="https://www.google.com/recaptcha/admin">Get reCaptcha Key</a>', 'zandc' ),
			array(
				'a' => array(
					'href'  => true,
					'title' => true
				)
			)
		)
	);

	$options[] = array(
		'name'  => esc_html__( 'reCaptcha Secret Key', 'zandc' ),
		'id'    => 'zandc_recaptcha_secret_key',
		'std'   => '',
		'type'  => 'text',
		'class' => 'small', //mini, tiny, small
		'desc'  => wp_kses(
			__( '<a href="https://www.google.com/recaptcha/admin">Get reCaptcha Secret Key</a>', 'zandc' ),
			array(
				'a' => array(
					'href'  => true,
					'title' => true
				)
			)
		)
	);

	$options[] = array(
		'name' => esc_html__( 'Instant Domain Search', 'zandc' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name'    => esc_html__( 'Enable Instant Domain Search', 'zandc' ),
		'id'      => 'zandc_enable_instant_domain_search',
		'std'     => 'yes',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'yes' => esc_html__( 'Yes', 'zandc' ),
			'no'  => esc_html__( 'No', 'zandc' )
		),
		'desc'    => esc_html__( '"Instant Domain Search" is a feature that shows results as you type', 'zandc' )
	);

	$options[] = array(
		'name'    => esc_html__( 'Try Faster Checking', 'zandc' ),
		'id'      => 'zandc_try_faster_checking',
		'std'     => 'no',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'yes' => esc_html__( 'Yes', 'zandc' ),
			'no'  => esc_html__( 'No', 'zandc' )
		),
		'desc'    => esc_html__( 'Check domain immediately when typing', 'zandc' )
	);

	$options[] = array(
		'name'    => esc_html__( 'Try Country Detection', 'zandc' ),
		'id'      => 'zandc_try_country_detection',
		'std'     => 'yes',
		'type'    => 'select',
		'class'   => 'mini', //mini, tiny, small
		'options' => array(
			'yes' => esc_html__( 'Yes', 'zandc' ),
			'no'  => esc_html__( 'No', 'zandc' )
		),
		'desc'    => esc_html__( 'Domain checker will include a country-code top level domains (ccTLD) by country detection. This feature only work with instant domain search.', 'zandc' )
	);

	// Zan Domain Checker End ==============================
	return $options;
}