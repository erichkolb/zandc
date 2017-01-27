<?php
/**
 * For full documentation, please visit: https://github.com/ReduxFramework/ReduxFramework/wiki
 * */

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( !class_exists( 'ZanDcReduxFrameworkConfig' ) ) {

	class ZanDcReduxFrameworkConfig
	{

		public $args     = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( !class_exists( 'ReduxFramework' ) ) {
				return;
			}

			$this->initSettings();
		}

		public function initSettings() {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( !isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			//add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

			// Function to test the compiler hook and demo CSS output.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			// Change the default value of a field after it's been set, but before it's been useds
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
			// Dynamically add a section. Can be also used to modify sections/fields
			//add_filter( 'redux/options/' . $this->args['opt_name'] . '/sections', array( $this, 'dynamic_section' ) );

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		/**
		 *
		 * This is a test function that will let you see when the compiler hook occurs.
		 * It only runs if a field   set with compiler=>true is changed.
		 * */
		function compiler_action( $options, $css ) {

		}

		function zandc_redux_update_options_user_can_register( $options, $css ) {
			global $zandc;
			$users_can_register = isset( $zandc['opt-users-can-register'] ) ? $zandc['opt-users-can-register'] : 0;
			update_option( 'users_can_register', $users_can_register );
		}

		/**
		 *
		 * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 * Simply include this function in the child themes functions.php file.
		 *
		 * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 * so you must use get_template_directory_uri() if you want to use any of the built in icons
		 * */
		function dynamic_section( $sections ) {
			//$sections = array();
			$sections[] = array(
				'title'  => esc_html__( 'Section via hook', 'zandc' ),
				'desc'   => wp_kses( __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'zandc' ), array( 'p' => array( 'class' => array() ) ) ),
				'icon'   => 'el-icon-paper-clip',
				// Leave this as a blank section, no options just some intro text set above.
				'fields' => array(),
			);

			return $sections;
		}

		/**
		 *
		 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
		 * */
		function change_arguments( $args ) {
			//$args['dev_mode'] = true;

			return $args;
		}

		/**
		 *
		 * Filter hook for filtering the default value of any given field. Very useful in development mode.
		 * */
		function change_defaults( $defaults ) {
			$defaults['str_replace'] = "Testing filter hook!";

			return $defaults;
		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::instance(), 'plugin_metalinks' ), null, 2 );

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );

			}
		}

		public function setSections() {

			/**
			 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 * */
			// Background Patterns Reader
			$sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
			$sample_patterns_url = ReduxFramework::$_url . '../sample/patterns/';
			$sample_patterns = array();

			if ( is_dir( $sample_patterns_path ) ) :

				if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
					$sample_patterns = array();

					while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

						if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
							$name = explode( ".", $sample_patterns_file );
							$name = str_replace( '.' . end( $name ), '', $sample_patterns_file );
							$sample_patterns[] = array( 'alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file );
						}
					}
				endif;
			endif;

			ob_start();

			$ct = wp_get_theme();
			$this->theme = $ct;
			$item_name = $this->theme->get( 'Name' );
			$tags = $this->theme->Tags;
			$screenshot = $this->theme->get_screenshot();
			$class = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf( __( 'Customize &#8220;%s&#8221;', 'zandc' ), $this->theme->display( 'Name' ) );
			?>
			<div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
				<?php if ( $screenshot ) : ?>
					<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
						<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize"
						   title="<?php echo esc_attr( $customize_title ); ?>">
							<img src="<?php echo esc_url( $screenshot ); ?>"
							     alt="<?php esc_attr_e( 'Current theme preview', 'zandc' ); ?>"/>
						</a>
					<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>"
					     alt="<?php esc_attr_e( 'Current theme preview', 'zandc' ); ?>"/>
				<?php endif; ?>

				<h4>
					<?php echo sanitize_text_field( $this->theme->display( 'Name' ) ); ?>
				</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf( __( 'By %s', 'zandc' ), $this->theme->display( 'Author' ) ); ?></li>
						<li><?php printf( __( 'Version %s', 'zandc' ), $this->theme->display( 'Version' ) ); ?></li>
						<li><?php echo '<strong>' . esc_html__( 'Tags', 'zandc' ) . ':</strong> '; ?><?php printf( $this->theme->display( 'Tags' ) ); ?></li>
					</ul>
					<p class="theme-description"><?php echo esc_attr( $this->theme->display( 'Description' ) ); ?></p>
					<?php
					if ( $this->theme->parent() ) {
						printf(
							' <p class="howto">' . wp_kses( __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'zandc' ), array( 'a' => array( 'href' => array() ) ) ) . '</p>', esc_html__( 'http://codex.wordpress.org/Child_Themes', 'zandc' ), $this->theme->parent()
							                                                                                                                                                                                                                                                            ->display( 'Name' )
						);
					}
					?>

				</div>

			</div>

			<?php
			$item_info = ob_get_contents();

			ob_end_clean();

			$sampleHTML = '';

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

			/*--General Settings--*/
			$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'General Settings', 'zandc' ),
				'fields' => array(
					array(
						'id'    => 'general_introduction',
						'type'  => 'info',
						'style' => 'success',
						'title' => esc_html__( 'Welcome to Instant Domain Checker options panel', 'zandc' ),
						'icon'  => 'el-icon-info-sign',
						'desc'  => esc_html__( 'From here you can config Instant Domain Checker in the way you need.', 'zandc' ),
					),
					array(
						'id'       => 'zandc_tld_exts',
						'type'     => 'textarea',
						'title'    => esc_html__( 'Top Level Domain Extensions', 'zandc' ),
						'desc'     => esc_html__( 'List of supported TLD extensions, each extension is separated by a vertical stripe. Ex: com|org|net|us|jp. Empty list means all extensions are allowed.', 'zandc' ),
						'validate' => '',
						'default'  => zan_dc_get_option_prev_version( 'zandc_tld_exts', '' )
					),
					array(
						'id'       => 'zandc_max_num_of_exts',
						'type'     => 'text',
						'title'    => esc_html__( 'Maximum Results', 'zandc' ),
						'desc'     => esc_html__( 'Maximum number of checking results. Ex: 5.', 'zandc' ),
						'validate' => 'number',
						'msg'      => esc_html__( 'Maximum results must be a number', 'zandc' ),
						'default'  => zan_dc_get_option_prev_version( 'zandc_max_num_of_exts', 5 )
					),
					array(
						'id'       => 'zandc_search_input_placeholder',
						'type'     => 'text',
						'title'    => esc_html__( 'Placeholder Text', 'zandc' ),
						'desc'     => esc_html__( 'Search input placeholder text.', 'zandc' ),
						'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_search_input_placeholder', esc_html__( 'Search domain', 'zandc' ) )
					),
					array(
						'id'      => 'zandc_show_search_btn',
						'type'    => 'select',
						'title'   => esc_html__( 'Show Search Button', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'default' => zan_dc_get_option_prev_version( 'zandc_show_search_btn', 'yes' )
					),
					array(
						'id'       => 'zandc_search_btn_text',
						'type'     => 'text',
						'title'    => esc_html__( 'Search Button Text', 'zandc' ),
						'desc'     => esc_html__( 'Search input placeholder text.', 'zandc' ),
						'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_search_btn_text', esc_html__( 'Search', 'zandc' ) ),
						'required' => array( 'zandc_show_search_btn', '=', 'yes' ),
					),
					array(
						'id'      => 'zandc_show_whois_in',
						'type'    => 'select',
						'title'   => esc_html__( 'Show Whois In', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'disable'     => esc_html__( 'Disable', 'zandc' ),
							'popup'       => esc_html__( 'Popup', 'zandc' ),
							'custom_page' => esc_html__( 'Custom Page', 'zandc' )
						),
						'default' => zan_dc_get_option_prev_version( 'zandc_show_whois_in', 'popup' )
					),
					array(
						'id'       => 'zandc_whois_page',
						'type'     => 'select',
						'title'    => esc_html__( 'Whois Page', 'zandc' ),
						'multi'    => false,
						// Must provide key => value pairs for select options
						'options'  => $options_pages,
						'default'  => zan_dc_get_option_prev_version( 'zandc_whois_page', '' ),
						'required' => array( 'zandc_show_whois_in', '=', 'custom_page' ),
					),
					array(
						'id'       => 'zandc_whois_title',
						'type'     => 'text',
						'title'    => esc_html__( 'Whois Title', 'zandc' ),
						'desc'     => esc_html__( 'Default: Whois record for {domain}. The {domain} will be replaced by domain name.', 'zandc' ),
						//'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_whois_title', esc_html__( 'Whois record for {domain}', 'zandc' ) ),
						'required' => array( 'zandc_show_whois_in', '!=', 'disable' ),
					),
					array(
						'id'       => 'zandc_whois_btn_text',
						'type'     => 'text',
						'title'    => esc_html__( 'Whois Button Text', 'zandc' ),
						'desc'     => esc_html__( 'Default: Whois. The {domain} will be replaced by domain name.', 'zandc' ),
						//'validate' => 'no_html',
						'default'  => esc_html__( 'Whois', 'zandc' ),
						'required' => array( 'zandc_show_whois_in', '!=', 'disable' ),
					),
					array(
						'id'      => 'zandc_show_transfer_btn',
						'type'    => 'select',
						'title'   => esc_html__( 'Show Transfer Button', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'default' => 'no',
						'desc'    => esc_html__( 'Show transfer button when domain is not available', 'zandc' ),
					),
					//					array(
					//						'id'       => 'zandc_transfer_exts',
					//						'type'     => 'textarea',
					//						'title'    => esc_html__( 'Extensions Allowed Show Transfer Button', 'zandc' ),
					//						'desc'     => esc_html__( 'List of TLD, SLD extensions that allowed to show transfer button, each extension is separated by a vertical stripe. Ex: com|org|net|us|jp|co.uk. Empty list means all extensions are allowed.', 'zandc' ),
					//						'validate' => '',
					//						'default'  => zan_dc_get_option_prev_version( 'zandc_tld_exts', '' ),
					//						'required' => array( 'zandc_show_transfer_btn', '=', 'yes' ),
					//					),
					array(
						'id'       => 'zandc_transfer_link',
						'type'     => 'text',
						'title'    => esc_html__( 'Transfer Link', 'zandc' ),
						//'validate' => 'no_html',
						'desc'     => esc_html__( 'Put the transfer link you want. The {domain}, {domain_not_ext}, {ext} will be replaced by domain name, domain name without exstension, exstention. Ex: http://zanthemes.net/?a=add&domain=transfer&sld={domain_not_ext}&tld=.{ext}', 'zandc' ),
						'required' => array( 'zandc_show_transfer_btn', '=', 'yes' ),
					),
					array(
						'id'       => 'zandc_transfer_btn_text',
						'type'     => 'text',
						'title'    => esc_html__( 'Transfer Button Text', 'zandc' ),
						'desc'     => esc_html__( 'Default: Transfer. The {domain} will be replaced by domain name.', 'zandc' ),
						//'validate' => 'no_html',
						'default'  => esc_html__( 'Transfer', 'zandc' ),
						'required' => array( 'zandc_show_transfer_btn', '=', 'yes' ),
					),
					array(
						'id'      => 'zandc_avai_result_message',
						'type'    => 'text',
						'title'   => esc_html__( 'Available Result Message', 'zandc' ),
						'desc'    => esc_html__( 'Edit the available result message or leave it empty to use default message. The {domain} will be replaced by domain name.', 'zandc' ),
						//'validate' => 'no_html',
						'default' => zan_dc_get_option_prev_version( 'zandc_avai_result_message', esc_html__( 'The domain {domain} is not registered', 'zandc' ) )
					),
					array(
						'id'      => 'zandc_not_avai_result_message',
						'type'    => 'text',
						'title'   => esc_html__( 'Not Available Result Message', 'zandc' ),
						'desc'    => esc_html__( 'Edit the message or leave it empty to use default message. The {domain} will be replaced by domain name.', 'zandc' ),
						//'validate' => 'no_html',
						'default' => zan_dc_get_option_prev_version( 'zandc_not_avai_result_message', esc_html__( 'The domain {domain} is registered', 'zandc' ) )
					),
					array(
						'id'      => 'zandc_not_supported_tld_ext',
						'type'    => 'text',
						'title'   => esc_html__( 'Not Supported TLD Extensions Message', 'zandc' ),
						'desc'    => esc_html__( 'Edit the message or leave it empty to use default message. The {ext} will be replaced by TLD extension.', 'zandc' ),
						//'validate' => 'no_html',
						'default' => zan_dc_get_option_prev_version( 'zandc_not_supported_tld_ext', esc_html__( 'Sorry, currently there is WHOIS server for this TLD extension: {ext}', 'zandc' ) )
					),
				),
			);

			/* Instant Domain Search Settings */
			$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'Instant Domain Search', 'zandc' ),
				'fields' => array(
					array(
						'id'      => 'zandc_enable_instant_domain_search',
						'type'    => 'select',
						'title'   => esc_html__( 'Enable Instant Domain Search', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'desc'    => esc_html__( '"Instant Domain Search" is a feature that shows results as you type', 'zandc' ),
						'default' => zan_dc_get_option_prev_version( 'zandc_enable_instant_domain_search', 'yes' )
					),
					array(
						'id'      => 'zandc_try_faster_checking',
						'type'    => 'select',
						'title'   => esc_html__( 'Try Faster Checking', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'desc'    => esc_html__( 'Check domain immediately when typing.', 'zandc' ),
						'default' => zan_dc_get_option_prev_version( 'zandc_try_faster_checking', 'yes' )
					),
					array(
						'id'       => 'zandc_check_popular_only',
						'type'     => 'switch',
						'title'    => esc_html__( 'Check Popular Domain Name Only', 'zandc' ),
						'on'       => esc_html__( 'On', 'zandc' ),
						'off'      => esc_html__( 'Off', 'zandc' ),
						'desc'     => esc_html__( 'Warning: Show popular results, may be some other results not show. But checking speed is faster.', 'zandc' ),
						'default'  => 0,
						'required' => array( 'zandc_try_faster_checking', '=', 'yes' ),
					),
					array(
						'id'      => 'zandc_try_country_detection',
						'type'    => 'select',
						'title'   => esc_html__( 'Try Country Detection', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'desc'    => esc_html__( 'Domain checker will include a country-code top level domains (ccTLD) by country detection. This feature only work with instant domain search.', 'zandc' ),
						'default' => zan_dc_get_option_prev_version( 'zandc_try_country_detection', 'yes' )
					),
					array(
						'id'       => 'zandc_sld_exts',
						'type'     => 'textarea',
						'title'    => esc_html__( 'Second Level Domain Extensions', 'zandc' ),
						'desc'     => esc_html__( 'Try to check SLD extensions from the list, each extension is separated by a vertical stripe. Ex: co.uk|org.uk|co.at|org.au. Empty list means ignore SLD checking. Note: Not all of SLDs can\'t be checked.', 'zandc' ),
						'validate' => '',
						'default'  => ''
					),
					array(
						'id'       => 'zandc_sld_whois_servers',
						'type'     => 'textarea',
						'title'    => esc_html__( 'Second Level Domain Whois Servers', 'zandc' ),
						'desc'     => esc_html__( 'List of whois servers for SLD extensions. Each server in one line. Format: http://sldwhoisserveraddress.com/{domain}|sld. Ex: https://who.is/whois/{domain}|co.uk', 'zandc' ),
						'validate' => '',
						'default'  => ''
					),
					array(
						'id'       => 'zandc_show_sld_results_before_tld_results',
						'type'     => 'switch',
						'title'    => esc_html__( 'Show SLDs Checking Results Before TLDs Checking Results', 'zandc' ),
						'on'       => esc_html__( 'Yes', 'zandc' ),
						'off'      => esc_html__( 'No', 'zandc' ),
						'default'  => 0,
						'required' => array( 'zandc_sld_exts', '!=', '' ),
					),
					array(
						'id'      => 'zandc_using_ssl',
						'type'    => 'switch',
						'title'   => esc_html__( 'Using SSL Protocol?', 'zandc' ),
						'on'      => esc_html__( 'Yes', 'zandc' ),
						'off'     => esc_html__( 'No', 'zandc' ),
						'default' => 1,
					),
					array(
						'id'       => 'zandc_remove_form_wrap',
						'type'     => 'switch',
						'title'    => esc_html__( 'Remove From Wrap', 'hoangphat' ),
						'subtitle' => esc_html__( 'Remove from tag wrap for instant search. It maybe useful in some case, such as compatible with Gravity Form', 'hoangphat' ),
						'default'  => '0',
						'on'       => esc_html__( 'Yes', 'hoangphat' ),
						'off'      => esc_html__( 'No', 'hoangphat' ),
						'required' => array( 'zandc_enable_instant_domain_search', '=', array( 'yes' ) ),
					),
				)
			);

			/* Integration Settings */
			$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'Integration Settings', 'zandc' ),
				'fields' => array(
					array(
						'id'      => 'zandc_integrate_with',
						'type'    => 'select',
						'title'   => esc_html__( 'Integration With', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'disable'     => esc_html__( 'Disable', 'zandc' ),
							'woocommerce' => esc_html__( 'WooCommerce', 'zandc' ),
							'whmcs'       => esc_html__( 'WHMCS', 'zandc' ),
							'link'        => esc_html__( 'Link', 'zandc' )
						),
						'default' => zan_dc_get_option_prev_version( 'zandc_integrate_with', 'disable' )
					),
					array(
						'id'       => 'zandc_tld_exts_integrated_with_wc_products',
						'type'     => 'textarea',
						'title'    => esc_html__( 'Top Level Domain Extensions Integration With Products', 'zandc' ),
						'desc'     => esc_html__( 'List of TLD extensions integration with products, each extension and product id is separated by a vertical stripe. {ext1}-{product_id1}|{ext2}-{product_id2}|{ext3}-{product_id3}. Ex: com-23|org-18|net-65|us|jp-674.', 'zandc' ),
						'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_tld_exts_integrated_with_wc_products', '' ),
						'required' => array( 'zandc_integrate_with', '=', 'woocommerce' ),
					),
					array(
						'id'       => 'zandc_wc_integration_btn_text',
						'type'     => 'text',
						'title'    => esc_html__( 'WooCommerce Integration Button Text', 'zandc' ),
						//'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_wc_integration_btn_text', esc_html__( 'Add To Cart', 'zandc' ) ),
						'required' => array( 'zandc_integrate_with', '=', 'woocommerce' ),
					),
					array(
						'id'       => 'zandc_integration_link',
						'type'     => 'text',
						'title'    => esc_html__( 'Integration Link', 'zandc' ),
						//'validate' => 'no_html',
						'desc'     => esc_html__( 'Put the custom link you want integration. The {domain} will be replaced by domain name. Ex for WHMSC: http://biling.yourhosturl.com. Ex for custom link: http://zanthemes.net/?regdomain={domain}', 'zandc' ),
						'default'  => zan_dc_get_option_prev_version( 'zandc_integration_link', '' ),
						'required' => array( 'zandc_integrate_with', '=', array( 'link', 'whmcs' ) ),
					),
					array(
						'id'       => 'zandc_integration_link_open_new_tab',
						'type'     => 'switch',
						'title'    => esc_html__( 'Open Link In New Tab?', 'hoangphat' ),
						'default'  => '1',
						'on'       => esc_html__( 'Yes', 'hoangphat' ),
						'off'      => esc_html__( 'No', 'hoangphat' ),
						'required' => array( 'zandc_integrate_with', '=', array( 'link' ) ),
					),
					array(
						'id'       => 'zandc_integration_link_text',
						'type'     => 'text',
						'title'    => esc_html__( 'Integration Link Text', 'zandc' ),
						//'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_integration_link_text', esc_html__( 'Order', 'zandc' ) ),
						'required' => array( 'zandc_integrate_with', '=', array( 'link', 'whmcs' ) ),
					),
				)
			);

			/* Google reCAPTCHA Settings */
			$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'Google reCAPTCHA', 'zandc' ),
				'fields' => array(
					array(
						'id'      => 'zandc_enable_recaptcha',
						'type'    => 'select',
						'title'   => esc_html__( 'Enable reCAPTCHA', 'zandc' ),
						'multi'   => false,
						// Must provide key => value pairs for select options
						'options' => array(
							'yes' => esc_html__( 'Yes', 'zandc' ),
							'no'  => esc_html__( 'No', 'zandc' )
						),
						'desc'    => esc_html__( 'reCAPTCHA is a free service from Google that helps protect websites from spam and abuse. A “CAPTCHA” is a test to tell human and bots apart. It is easy for humans to solve, but hard for “bots” and other malicious software to figure out.', 'zandc' ),
						'default' => zan_dc_get_option_prev_version( 'zandc_enable_recaptcha', 'no' )
					),
					array(
						'id'       => 'zandc_recaptcha_site_key',
						'type'     => 'text',
						'title'    => esc_html__( 'reCaptcha Site Key', 'zandc' ),
						'desc'     => wp_kses(
							__( '<a href="https://www.google.com/recaptcha/admin" target="_blank">Get reCaptcha Key</a>', 'zandc' ),
							array(
								'a' => array(
									'href'   => true,
									'title'  => true,
									'target' => true
								)
							)
						),
						'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_recaptcha_site_key', '' ),
						'required' => array( 'zandc_enable_recaptcha', '=', 'yes' ),
					),
					array(
						'id'       => 'zandc_recaptcha_secret_key',
						'type'     => 'text',
						'title'    => esc_html__( 'reCaptcha Secret Key', 'zandc' ),
						'desc'     => wp_kses(
							__( '<a href="https://www.google.com/recaptcha/admin" target="_blank">Get reCaptcha Secret Key</a>', 'zandc' ),
							array(
								'a' => array(
									'href'   => true,
									'title'  => true,
									'target' => true
								)
							)
						),
						'validate' => 'no_html',
						'default'  => zan_dc_get_option_prev_version( 'zandc_recaptcha_secret_key', '' ),
						'required' => array( 'zandc_enable_recaptcha', '=', 'yes' ),
					),
				)
			);

			/* Custom Css Settings */
			$this->sections[] = array(
				'icon'   => 'el-icon-magic',
				'title'  => esc_html__( 'Custom CSS', 'zandc' ),
				'fields' => array(
					array(
						'id'       => 'custom_css_code',
						'type'     => 'ace_editor',
						'title'    => esc_html__( 'Custom CSS', 'zandc' ),
						'subtitle' => esc_html__( 'Paste your custom CSS code here.', 'zandc' ),
						'mode'     => 'css',
						'theme'    => 'monokai',
						'desc'     => esc_html__( 'Custom css code.', 'zandc' ),
						'default'  => "",
					)
				)
			);

			/* Custom JS Settings */
			/*$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'Custom JS', 'zandc' ),
				'fields' => array(
					array(
						'id'       => 'custom_js_code',
						'type'     => 'ace_editor',
						'title'    => esc_html__( 'Custom JS ', 'zandc' ),
						'subtitle' => esc_html__( 'Paste your custom JS code here.', 'zandc' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'desc'     => esc_html__( 'Custom javascript code', 'zandc' ),
						//'default' => "jQuery(document).ready(function(){\n\n});"
					),
				)
			);*/
		}

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
				'id'      => 'redux-opts-1',
				'title'   => esc_html__( 'Theme Information 1', 'zandc' ),
				'content' => wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zandc' ), array( 'p' ) ),
			);

			$this->args['help_tabs'][] = array(
				'id'      => 'redux-opts-2',
				'title'   => esc_html__( 'Theme Information 2', 'zandc' ),
				'content' => wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zandc' ), array( 'p' ) ),
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zandc' ), array( 'p' ) );
		}

		/**
		 *
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 * */
		public function setArguments() {

			//$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name'            => 'zandc', // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'        => '<span class="zan-plugin-name">Instant Domain Checker</span>', // Name that appears at the top of your panel
				'display_version'     => ZANDC_VERSION, // Version that appears at the top of your panel
				'menu_type'           => 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'      => true, // Show the sections below the admin menu item or not
				'menu_title'          => esc_html__( 'IDC Options', 'zandc' ),
				'page_title'          => esc_html__( 'IDC Options', 'zandc' ),
				// You will need to generate a Google API key to use this feature.
				// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
				'google_api_key'      => '', // Must be defined to add google fonts to the typography module
				//'async_typography'    => true, // Use a asynchronous font on the front end or font string
				//'admin_bar'           => false, // Show the panel pages on the admin bar
				'global_variable'     => 'zandc', // Set a different name for your global variable other than the opt_name
				'dev_mode'            => false, // Show the time the page took to load, etc
				'customizer'          => true, // Enable basic customizer support
				// OPTIONAL -> Give you extra features
				'page_priority'       => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				//'page_parent'        => 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'    => 'manage_options', // Permissions needed to access the options panel.
				'menu_icon'           => 'dashicons-admin-site', // Specify a custom URL to an icon
				'last_tab'            => '', // Force your panel to always open to a specific tab (by id)
				'page_icon'           => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
				'page_slug'           => 'zandc_options', // Page slug used to denote the panel
				'save_defaults'       => true, // On load save the defaults to DB before user clicks save or not
				'default_show'        => false, // If true, shows the default value next to each field that is not the default value.
				'default_mark'        => '', // What to print by the field's title if the value shown is default. Suggested: *
				// CAREFUL -> These options are for advanced use only
				'transient_time'      => 60 * MINUTE_IN_SECONDS,
				'output'              => true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'          => true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				//'domain'              => 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
				'footer_credit'       => esc_html__( 'Zan Themes WordPress Team', 'zandc' ), // Disable the footer credit of Redux. Please leave if you can help it.
				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'            => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'show_import_export'  => true, // REMOVE
				'system_info'         => false, // REMOVE
				'help_tabs'           => array(),
				'help_sidebar'        => '', // esc_html__( '', $this->args['domain'] );
				'show_options_object' => false,
				'hints'               => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',

					'tip_style'    => array(
						'color'   => 'light',
						'shadow'  => true,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position' => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'   => array(
						'show' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'click mouseleave',
						),
					),
				),
			);

			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/thuydungcafe',
				'title' => 'Like us on Facebook',
				'icon'  => 'el-icon-facebook',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el-icon-twitter',
			);

			// Panel Intro text -> before the form
			if ( !isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
				if ( !empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				}
				else {
					$v = str_replace( "-", "_", $this->args['opt_name'] );
				}

			}
			else {

			}

		}

	}

	new ZanDcReduxFrameworkConfig();
}


/**
 *
 * Custom function for the callback referenced above
 */
if ( !function_exists( 'redux_my_custom_field' ) ):

	function redux_my_custom_field( $field, $value ) {
		print_r( $field );
		print_r( $value );
	}

endif;

/**
 *
 * Custom function for the callback validation referenced above
 * */
if ( !function_exists( 'redux_validate_callback_function' ) ):

	function redux_validate_callback_function( $field, $value, $existing_value ) {
		$error = false;
		$value = 'just testing';

		$return['value'] = $value;
		if ( $error == true ) {
			$return['error'] = $field;
		}

		return $return;
	}

endif;