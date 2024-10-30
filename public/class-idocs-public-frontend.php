<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/*
	> enqueue fronted styles and javascript on-demand
/*---------------------------------------------------------------------------------------*/
class IDOCS_Public_Frontend {

	/*---------------------------------------------------------------------------------------*/
	// any add/edit of a page will reset the overall cache (all pages) of a list of shortcodes per page 
	public function reset_shortcodes_per_page_cache( $post_id ) {

		delete_transient( 'idocs_transient_shortcodes_per_page');

	}
	/*---------------------------------------------------------------------------------------*/
	// Register the stylesheets (CSS) for the public-facing side of the site.	
	public function enqueue_styles() {

		// exit if a block-based theme is being used. 
		
		if ( wp_is_block_theme() ) {

			
			/*--------------------------------------------*/
			// Enqueue custom styles
			/*
			wp_enqueue_style( 'idocs-bootstrap-css', 
							IDOCS_ADMIN_URL . 'css/vendor/bootstrap.min.css', 
							array(), 
							'5.3.3', 
							'all' 
							);
			// create an empty css handler for dynamic inline custom styles
			wp_enqueue_style( 'idocs-custom-css', 
							IDOCS_PUBLIC_URL . 'css/idocs-custom.css', 
							array(), 
							IDOCS_VERSION, 
							'all' 
							);
			/*--------------------------------------------*/
			return;
		}
		
		global $wp_query;
		/*--------------------------------------------*/
		// note - is_tax('idocs-category-taxo') is not working even if the query array includes that specific taxo.
		// therefore using the $wp_query directly and checking if the taxo included in that array.
		$cat_taxo = array_key_exists('idocs-category-taxo', $wp_query->query);
		$tag_taxo = array_key_exists('idocs-tag-taxo', $wp_query->query);
		$faqgroup_taxo = array_key_exists('idocs-faq-group-taxo', $wp_query->query);
		/*--------------------------------------------*/
		$is_kb_view = is_archive() && is_tax('idocs-kb-taxo') && ! $cat_taxo && ! $tag_taxo;
		$is_cat_view = is_archive() && $cat_taxo;
		$is_tag_view = is_archive() && $tag_taxo;
		$is_faqgroup_view = is_archive() && $faqgroup_taxo;
		$is_doc_view = is_singular( 'idocs_content' );
		/*--------------------------------------------*/
		/*
		do_action( 'qm/debug', "is_doc_view:" . $is_doc_view);
		do_action( 'qm/debug', "is_kb_view:" . $is_kb_view);
		do_action( 'qm/debug', "is_cat_view:" . $is_cat_view);
		do_action( 'qm/debug', "is_cat_view:" . $is_tag_view);
		*/
		/*--------------------------------------------*/
		if ( $is_doc_view || $is_kb_view || $is_cat_view || $is_tag_view ) {		
			
			/*--------------------------------------------*/
			// Enqueue custom styles
			wp_enqueue_style( 'idocs-bootstrap-css', 
							IDOCS_ADMIN_URL . 'css/vendor/bootstrap.min.css', 
							array(), 
							'5.3.3', 
							'all' 
							);
			// create an empty css handler for dynamic inline custom styles
			wp_enqueue_style( 'idocs-custom-css', 
							IDOCS_PUBLIC_URL . 'css/idocs-custom.css', 
							array(), 
							IDOCS_VERSION, 
							'all' 
							);
			/*--------------------------------------------*/
			// load the css for document 			
			if ( $is_doc_view  ) {
				
				// get the document id --> kb term --> kb_id
				$current_document_id = get_queried_object() != null ? get_queried_object()->ID : '';
				$current_kb = wp_get_object_terms($current_document_id, 'idocs-kb-taxo' );
				$kb_id = $current_kb[0]->term_id;
				// load the css for document view with the relevant kb design
				self::load_document_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id);
			}
			/*--------------------------------------------*/
			if ( $is_kb_view || $is_cat_view ) {
				
				$kb_id = get_queried_object()->term_id;	
				// load the css for kb view with the relevant kb design			
				self::load_kb_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id, 0);
			}
			/*--------------------------------------------*/
			if ( $is_tag_view ) {
			
				$kb_id = get_queried_object()->term_id;	
				// load the css for kb view with the relevant kb design			
				self::load_tag_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id);
			}
			/*--------------------------------------------*/
			if ( $is_faqgroup_view ) {
			
				$kb_id = get_queried_object()->term_id;	
				// load the css for kb view with the relevant kb design			
				self::load_faqgroup_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id);
			}
			/*--------------------------------------------*/
			return;
		}
		/*--------------------------------------------*/
		// in case it maybe a page with a plugin shortcode
		self::on_demand_load_css_for_shortcodes();
				
	}
	/*---------------------------------------------------------------------------------------*/
	// Register the JavaScript for the public-facing side of the site.
	public function enqueue_scripts() {
		
	
		if (wp_is_block_theme()) {

			return;
		}
			
		global $wp_query;
		/*--------------------------------------------*/
		$cat_taxo = array_key_exists('idocs-category-taxo', $wp_query->query);
		$tag_taxo = array_key_exists('idocs-tag-taxo', $wp_query->query);
		$faqgroup_taxo = array_key_exists('idocs-faq-group-taxo', $wp_query->query);
		/*--------------------------------------------*/
		$is_kb_view = is_archive() && is_tax('idocs-kb-taxo') && ! $cat_taxo && ! $tag_taxo;
		$is_cat_view = is_archive() && $cat_taxo;
		$is_tag_view = is_archive() && $tag_taxo;
		$is_faqgroup_view = is_archive() && $faqgroup_taxo;
		$is_doc_view = is_singular( 'idocs_content' );
		/*--------------------------------------------*/
		if ( $is_doc_view || $is_kb_view || $is_cat_view || $is_tag_view || $is_faqgroup_view ) {			
			
			// bootstrap 
			wp_enqueue_script( 'idocs-bootstrap-js',
								IDOCS_ADMIN_URL . 'js/vendor/bootstrap.min.js',
								array( 'jquery' ), 
								'5.3.3', 
								false 
							);
			/*--------------------------------------------*/
			// live search 
			// Pro version is not active! - load the core version live search engine 
			if (!apply_filters("idocspro_pro_version_active", false)) {

				//error_log('pro version is not active!');

				// live search 
				wp_enqueue_script( 'class-idocs-live-search-js', 

						   IDOCS_PUBLIC_URL . 'js/class-idocs-live-search.min.js', 
						   //IDOCS_PUBLIC_URL . 'js/class-idocs-live-search.js', 
						   // Enqueue a script with jQuery as a dependency.
						   array( 'jquery' ), 
						   IDOCS_VERSION, 
						   // Script will be loaded at the footer!!! 
						   true 
						);
				// Localizes a registered script- let us output Javascript data (as variables) to the HTML source page 
				// Works only if the script has already been registered.
				wp_localize_script( 'class-idocs-live-search-js', // the script name we will pass the data 
									'idocs_ajax_obj',      // Name of the JavaScript object
									array(
										// this is used by the JS to send GET requests to specific site url 
										'root_url' => get_site_url(),
										'nonce'     => wp_create_nonce( 'wp_rest' ),
									)  
								);

			}		
			/*--------------------------------------------*/
			if ( $is_kb_view || $is_cat_view ) {

				// categories drill
				wp_enqueue_script( 'class-idocs-categories-drill-js', 
							IDOCS_PUBLIC_URL . 'js/class-idocs-categories-drill.min.js', 
							// Enqueue a script with jQuery as a dependency.
							array( 'jquery' ), 
							IDOCS_VERSION, 
							// Script will be loaded at the footer!!! 
							true 
							);
				wp_localize_script( 'class-idocs-categories-drill-js', // the script name we will pass the data 
									'idocs_ajax_obj',      // Name of the JavaScript object
									array(
										// this is used by the JS to send GET requests to specific site url 
										'root_url' => get_site_url(),
										'nonce'     => wp_create_nonce( 'wp_rest' ),
										'ajax_url' => admin_url('admin-ajax.php'),
									)  
							);
				
			}
			/*--------------------------------------------*/
			if ( $is_doc_view ) {

				// print content
				wp_enqueue_script( 'class-idocs-print-js', 
							IDOCS_PUBLIC_URL . 'js/class-idocs-print.min.js', 
							// Enqueue a script with jQuery as a dependency.
							array( 'jquery' ), 
							IDOCS_VERSION, 
							// Script will be loaded at the footer!!! 
							true 
						);

				// toc highlights while scolling 
				wp_enqueue_script( 'class-idocs-sticky-toc-js', 
							IDOCS_PUBLIC_URL . 'js/class-idocs-sticky-toc.min.js', 
							// Enqueue a script with jQuery as a dependency.
							array( 'jquery' ), 
							IDOCS_VERSION, 
							// Script will be loaded at the footer!!! 
							true 
						);
			}
			/*--------------------------------------------*/
			return;
		}
		/*--------------------------------------------*/
		// in case, it maybe a page with a plugin shortcode 
		self::on_demand_load_js_for_shortcodes();
		
	}
	/*---------------------------------------------------------------------------------------*/
	// https://medium.com/@latzikatz/using-javascript-modules-in-your-wordpress-plugins-553a9cbb2d47
	// https://make.wordpress.org/core/2023/11/21/exploration-to-support-modules-and-import-maps/
	public function add_type_to_script($tag, $handle, $src){

		if ('idocs-pdfjs-pdf' === $handle || 
			'idocs-pdfjs-worker' === $handle ||
			'idocs-pdfjs-sandbox' === $handle ) {
			$tag = str_replace('<script ', '<script type="module" ', $tag);
			//$tag = '<script type="module" src="' . esc_url($src) . '"></script>';
		} 
		return $tag;

	}
	/*---------------------------------------------------------------------------------------*/
	public function get_shortcode_parameters($content, $shortcode) {

		/*--------------------------------------------*/
		$pattern = get_shortcode_regex(array($shortcode));
		preg_match_all('/' . $pattern . '/s', $content, $matches);
		//do_action( 'qm/debug',  $matches);
		$parameters = array();
		/*--------------------------------------------*/
		foreach ($matches[3] as $match) {
			$shortcode_attributes = shortcode_parse_atts($match);
			if ($shortcode_attributes !== false) {
				
				$parameters[] = $shortcode_attributes;
			}
		}
		/*--------------------------------------------*/
		return $parameters;
	}
	/*---------------------------------------------------------------------------------------*/
	public function enqueue_bootstrap_and_customc_css_handler() {

		wp_enqueue_style( 'idocs-bootstrap-css', 
							IDOCS_ADMIN_URL . 'css/vendor/bootstrap.min.css', 
							array(), 
							'5.3.3', 
							'all' 
							);
		
		wp_enqueue_style( 'idocs-custom-css', // this css handler is used to add css dynamically. 
						IDOCS_PUBLIC_URL . 'css/idocs-custom.css', 
						array(), 
						IDOCS_VERSION, 
						'all' 
						);
		

	}
	/*---------------------------------------------------------------------------------------*/
	public function run_shortcodes_scan_on_page ($page_id, $page_content) {

		//delete_transient( 'idocs_transient_shortcodes_per_page');
		$cached_data =  get_transient( 'idocs_transient_shortcodes_per_page');
		/*--------------------------------------------*/
		if ( false === $cached_data || !(isset($cached_data[$page_id])) ) {

			/*--------------------------------------------*/
			$shortcodes_flags = [
			
				'idocs_kb_view' => 0,
				'idocs_document_view' => 0,
				'idocs_live_search' => 0,
				'idocs_categories_cards' => 0,
				//'idocs_faqs' => 0,
				'idocs_kb_faqs' => 0,
				'idocs_category_faqs' => 0,
				'idocs_faqs_group' => 0,
				'idocs_document_tags' => 0,
				'idocs_related_documents' => 0,
	
			];
			/*--------------------------------------------*/
			// Loop through each shortcode
			foreach ($shortcodes_flags as $shortcode => &$flag) {
				
				if (has_shortcode($page_content, $shortcode)) {

					$flag = 1; // Set flag to true if shortcode exists in $post_content

				}
			};
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			};
			/*--------------------------------------------*/
			// scenario #2 - cache data available but not on that page
			//error_log('setting cache - list of shortcodes in that page:' . $page_id);
			$cached_data[$page_id] = $shortcodes_flags;
			set_transient( 'idocs_transient_shortcodes_per_page', $cached_data, 10800);
			return $shortcodes_flags;
		}
		/*--------------------------------------------*/
		else {

			//error_log('getting list of shortcodes in that page from cache:' . $page_id);
			return $cached_data[$page_id];

		}
	}
	/*---------------------------------------------------------------------------------------*/
	// load the required css if a specific shortcode is used inside a page 
	public function on_demand_load_css_for_shortcodes() {
	
		/*--------------------------------------------*/
		// check if the WP head is related to a page, if not exit. 
		if ( ! is_page() ) 
			return;
		/*--------------------------------------------*/
		// get the object information of the current page 
		$current_page = get_queried_object();
		$page_id = $current_page->ID;
		//do_action( 'qm/debug',  $current_page);
		/*--------------------------------------------*/
		$page_shortcodes_flags = self::run_shortcodes_scan_on_page($page_id, $current_page->post_content);
		//do_action( 'qm/debug',  $page_shortcodes_flags);
		/*--------------------------------------------*/
		// check if the page includes specific shortcodees 
		if(  $page_shortcodes_flags['idocs_kb_view'] ) {
			
			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_kb_view');
			//do_action( 'qm/debug',  $parameters);
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::load_kb_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id, $page_id);

			};
			/*--------------------------------------------*/
			return;
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_document_view'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_document_view');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));

			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::load_document_view_and_sub_shortcodes_css('idocs-custom-css', $kb_id);

			};
			/*--------------------------------------------*/
			return;
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_live_search'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_live_search');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::live_search_shortcode_load_css('idocs-custom-css', $kb_id);
			};
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_categories_cards'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_categories_cards');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::categories_cards_shortcode_load_css('idocs-custom-css', $kb_id);

			};
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_kb_faqs'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_kb_faqs');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::faqs_shortcode_load_css('idocs-custom-css', $kb_id);
				
			};
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_category_faqs'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_category_faqs');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::faqs_shortcode_load_css('idocs-custom-css', $kb_id);
				
			};
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_faqs_group'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_faqs_group');
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			if (term_exists($kb_id, 'idocs-kb-taxo')) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				self::faqs_shortcode_load_css('idocs-custom-css', $kb_id);
				
			};
		}				
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_related_documents'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_related_documents');

			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			$content_id = intval(sanitize_text_field($parameters[0]['content_id']));

			if (term_exists($kb_id, 'idocs-kb-taxo') ) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
				self::load_related_documents_css($design_settings, 'idocs-custom-css');

			};	
		}
		/*--------------------------------------------*/
		if(  $page_shortcodes_flags['idocs_document_tags'] ) {

			$this->enqueue_bootstrap_and_customc_css_handler();
			// extract the kb_id from the shortcode parameters 
			$parameters = $this->get_shortcode_parameters($current_page->post_content, 'idocs_document_tags');
			
			$kb_id = intval(sanitize_text_field($parameters[0]['kb_id']));
			$content_id = intval(sanitize_text_field($parameters[0]['content_id']));
			/*--------------------------------------------*/
			if (term_exists($kb_id, 'idocs-kb-taxo') ) {

				// passing the css handler to load css for kb and sub-shortcodes.  
				$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
				self::load_document_tags_css($design_settings, 'idocs-custom-css');

			};	
		}
	}	
	/*---------------------------------------------------------------------------------------*/
	public static function load_kb_view_and_sub_shortcodes_css($css_handler, $kb_id, $page_id) {

		// load the stored customizer design settings
		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/
		self::load_kb_view_css($design_settings, $css_handler);	
		self::load_live_search_css($design_settings, $css_handler);
		self::load_dynamic_video_links_css($design_settings, $css_handler);
		self::load_breadcrumbs_css($design_settings, $css_handler);
		self::load_categories_cards_css($design_settings, $css_handler);
		self::load_faqs_css($design_settings, $css_handler);
		self::load_no_page_title_css( $design_settings, $css_handler, $page_id );
		self::load_five_stars_rating_css($design_settings, $css_handler);
		/*--------------------------------------------*/

	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_tag_view_and_sub_shortcodes_css($css_handler, $kb_id) {

		// load the stored customizer design settings
		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/
		self::load_tag_view_css($design_settings, $css_handler);	
		self::load_live_search_css($design_settings, $css_handler);
		self::load_dynamic_video_links_css($design_settings, $css_handler);
		self::load_tag_content_cards_css($design_settings, $css_handler);
		/*--------------------------------------------*/
		
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_faqgroup_view_and_sub_shortcodes_css($css_handler, $kb_id) {

		// load the stored customizer design settings
		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/
		self::load_faqgroup_view_css($design_settings, $css_handler);	
		self::load_live_search_css($design_settings, $css_handler);
		self::load_document_likes_css($design_settings, $css_handler);
		/*--------------------------------------------*/

	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_no_page_title_css($design_settings, $css_handler, $page_id ) {

		$show_page_title = $design_settings['kb_view_show_page_title'];
		/*--------------------------------------------*/
		if ( $page_id == 0 || $show_page_title) {

			return;

		}
		/*--------------------------------------------*/
		ob_start();
		?>
			<style type="text/css"> 

				.page-id-<?php echo esc_attr($page_id); ?> .entry-title {
					display:none; 
				}

			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	// loading javascripts for shortcodes 
	public function on_demand_load_js_for_shortcodes() {

		// check if the WP head is related to a page, if not exit. 
		if ( ! is_page() ) 
			return;
		// get the object information of the current page 
		$current_page = get_queried_object();
		$page_id = $current_page->ID;

		//do_action( 'qm/debug',  $current_page);
		$loaded_scripts = [

			'bootstrap' => false,
			'live-search' => false,
			'categories-drill' => false,
			'print' => false,
			'sticky-toc' => false,
			
		];
		/*--------------------------------------------*/
		$page_shortcodes_flags = self::run_shortcodes_scan_on_page($page_id, $current_page->post_content);
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_kb_view'] ) {

			self::load_bootstrap_js($loaded_scripts);
			self::load_live_search_js($loaded_scripts);
			self::categories_drill_js($loaded_scripts);
		
		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_document_view'] ) {

			self::load_bootstrap_js($loaded_scripts);
			self::load_live_search_js($loaded_scripts);
		
		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_live_search'] ) {

			self::load_bootstrap_js($loaded_scripts);
			self::load_live_search_js($loaded_scripts);
			
		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_categories_cards'] ) {

			self::load_bootstrap_js($loaded_scripts);
			self::categories_drill_js($loaded_scripts);
	
		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_kb_faqs'] || $page_shortcodes_flags['idocs_category_faqs'] || $page_shortcodes_flags['idocs_faqs_group']) {

			self::load_bootstrap_js($loaded_scripts);

		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_document_tags'] ) {

			//self::load_bootstrap_js($loaded_scripts);

		}
		/*--------------------------------------------*/
		if ( $page_shortcodes_flags['idocs_related_documents'] ) {


		}
		/*--------------------------------------------*/
		
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_bootstrap_js(&$loaded_scripts) {

		if ($loaded_scripts['bootstrap'] == false ) {

			wp_enqueue_script( 'idocs-bootstrap-js',
								IDOCS_ADMIN_URL . 'js/vendor/bootstrap.min.js',
								array( 'jquery' ), 
								'5.3.3', 
								false 
							);
			$loaded_scripts['bootstrap'] == true;
		};
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_print_js(&$loaded_scripts) {
		
		if ($loaded_scripts['print'] == false ) {	

			// print content
			wp_enqueue_script( 'class-idocs-print-js', 
				IDOCS_PUBLIC_URL . 'js/class-idocs-print.min.js', 
				// Enqueue a script with jQuery as a dependency.
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! 
				true 
			);
			/*--------------------------------------------*/
			$loaded_scripts['print'] == true;
		};
	
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_sticky_toc_js(&$loaded_scripts) {
		
		if ($loaded_scripts['sticky-toc'] == false ) {	
			
			// toc highlights while scolling 
			wp_enqueue_script( 'class-idocs-sticky-toc-js', 
				IDOCS_PUBLIC_URL . 'js/class-idocs-sticky-toc.min.js', 
				// Enqueue a script with jQuery as a dependency.
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! 
				true 
			);
			/*--------------------------------------------*/
			$loaded_scripts['sticky-toc'] == true;
		};
	}
	/*---------------------------------------------------------------------------------------*/
	public static function categories_drill_js(&$loaded_scripts) {

		if ($loaded_scripts['categories-drill'] == false ) {

			// categories drill
			wp_enqueue_script( 'class-idocs-categories-drill-js', 
							IDOCS_PUBLIC_URL . 'js/class-idocs-categories-drill.min.js', 
							// Enqueue a script with jQuery as a dependency.
							array( 'jquery' ), 
							IDOCS_VERSION, 
							// Script will be loaded at the footer!!! 
							true 
						);

			wp_localize_script( 'class-idocs-categories-drill-js', // the script name we will pass the data 
					'idocs_ajax_obj',      // Name of the JavaScript object
					array(
						// this is used by the JS to send GET requests to specific site url 
						'root_url' => get_site_url(),
						'nonce'     => wp_create_nonce( 'wp_rest' ),
					));
			/*--------------------------------------------*/	
			$loaded_scripts['categories-drill'] == true;
		};

	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_live_search_js(&$loaded_scripts) {

		/*--------------------------------------------*/
		// Pro version is NOT active! - load the core version live search engine 
		if (!apply_filters("idocspro_pro_version_active", false)) {

			if ($loaded_scripts['live-search'] == false ) {
				// live search 
				wp_enqueue_script( 'class-idocs-live-search-js', 
								IDOCS_PUBLIC_URL . 'js/class-idocs-live-search.min.js', 
								// Enqueue a script with jQuery as a dependency.
								array( 'jquery' ), 
								IDOCS_VERSION, 
								// Script will be loaded at the footer!!! 
								true 
							);
			
				wp_localize_script( 'class-idocs-live-search-js', // the script name we will pass the data 
									'idocs_ajax_obj',      // Name of the JavaScript object
									array(
										// this is used by the JS to send GET requests to specific site url 
										'root_url' => get_site_url(),
										'nonce'     => wp_create_nonce( 'wp_rest' ),
									)  
								);
				$loaded_scripts['live-search'] == true;					
			};
		}
		/*--------------------------------------------*/ 
		else { 

			// pro is active - load pro scripts 
			do_action('idocs_load_pro_scripts_super_search');
			do_action('idocs_load_pro_scripts_rating');

		};
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_view_and_sub_shortcodes_js() {
		
		wp_enqueue_script( 'idocs-bootstrap-js',
							IDOCS_ADMIN_URL . 'js/vendor/bootstrap.min.js',
							array( 'jquery' ), 
							'5.3.3', 
							false 
						);
		// live search 
		wp_enqueue_script( 'class-idocs-live-search-js', 
				IDOCS_PUBLIC_URL . 'js/class-idocs-live-search.min.js', 
				// Enqueue a script with jQuery as a dependency.
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! 
				true 
	 	);
		wp_localize_script( 'class-idocs-live-search-js', // the script name we will pass the data 
					'idocs_ajax_obj',      // Name of the JavaScript object
					array(
						// this is used by the JS to send GET requests to specific site url 
						'root_url' => get_site_url(),
						'nonce'     => wp_create_nonce( 'wp_rest' ),
					)  
		);
	}
	/*---------------------------------------------------------------------------------------*/
	public static function live_search_shortcode_load_css($css_handler, $kb_id) {

		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/	
		self::load_live_search_css($design_settings, $css_handler);	
		self::load_dynamic_video_links_css($design_settings, $css_handler);

	}
	/*---------------------------------------------------------------------------------------*/
	public static function categories_cards_shortcode_load_css($css_handler, $kb_id) {

		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/	
		self::load_categories_cards_css($design_settings, $css_handler);	

	}
	/*---------------------------------------------------------------------------------------*/
	public static function faqs_shortcode_load_css($css_handler, $kb_id) {


		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
		/*--------------------------------------------*/	
		self::load_faqs_css($design_settings, $css_handler);	

	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_view_and_sub_shortcodes_css($css_handler, $kb_id) {

		$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);	
		/*--------------------------------------------*/
		self::load_live_search_css($design_settings, $css_handler);	
		self::load_dynamic_video_links_css($design_settings, $css_handler);
		self::load_breadcrumbs_css($design_settings, $css_handler);
		self::load_document_view_css($design_settings, $css_handler);
		self::load_navigation_css($design_settings, $css_handler);
		self::load_document_likes_css($design_settings, $css_handler);
		self::load_five_stars_rating_css($design_settings, $css_handler);
		self::load_document_feedback_css($design_settings, $css_handler);
		self::load_toc_css($design_settings, $css_handler);
		self::load_related_documents_css($design_settings, $css_handler);
		self::load_document_tags_css($design_settings, $css_handler);
		/*--------------------------------------------*/
		
	}
	/*---------------------------------------------------------------------------------------*/	
	public static function load_dynamic_video_links_css($design_settings, $css_handler) {

		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css">
			 
			.idocs-video-overlay {
					display: none;
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background-color: rgba(0, 0, 0, 0.8);
					justify-content: center;
					align-items: center;
				}
				/*--------------------------------------------*/
				.idocs-video-popup-container {
					position: relative;
					background-color: #d55454;
					padding: 10px;
					max-width: 800px;
					max-height: 80%;
					overflow: auto;
				}
				/*--------------------------------------------*/
				.idocs-video-close-btn {
					position: absolute;
					top: 0; right: 0;
					top: 10px;
					right: 10px;
					cursor: pointer;
					padding: 5px 10px;
					background-color: red;
					border: none;
					border-radius: 5px;
					font-size: 20px;
				}

		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		// Remove the <style> tag while keeping its content
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/	
	public static function load_live_search_css($design_settings, $css_handler) {

		/*--------------------------------------------*/
		$live_search_box_margin_padding = explode(",", $design_settings["live_search_box_margin_padding"]);
		$live_search_input_box_padding = explode(",", $design_settings["live_search_input_box_padding"]);
		$live_search_result_item_padding = explode(",", $design_settings["live_search_result_item_padding"]);
		$live_search_result_content_filter_padding = explode(",", $design_settings["live_search_result_content_filter_padding"]);
		$live_search_box_border_radius = $design_settings['live_search_box_border_radius'];
		$live_search_input_box_border_radius = $design_settings['live_search_input_box_border_radius'];
		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css">
			 .idocs-live-search-box {
				background-color: <?php echo esc_attr($design_settings['live_search_box_background_color']); ?>; 
				background-image:   <?php
										if ($design_settings['live_search_box_background_image']) {
										?>
											url("<?php echo esc_url($design_settings['live_search_box_background_image']); ?>");
										<?php
										}
										else 
											echo esc_attr("none;");
									?>
				background-position: center; 
				background-repeat: no-repeat; 
				background-size: cover; 
				<?php
				 if (intval($design_settings['live_search_box_border_width'] != 0)) {
					?>
						border-color: <?php echo esc_attr($design_settings['live_search_box_border_color']); ?>;
						border-style: solid;
						border-radius: <?php echo esc_attr($live_search_box_border_radius) . 'px'; ?>;
						border-width: <?php echo esc_attr($design_settings['live_search_box_border_width']) . 'px'; ?>;
						
					<?php
				 }
				?>
				margin-top: <?php echo esc_attr($live_search_box_margin_padding[0]) . 'px'; ?>;
				margin-right: <?php echo esc_attr($live_search_box_margin_padding[1]) . 'px'; ?>;
				margin-bottom: <?php echo esc_attr($live_search_box_margin_padding[2]) . 'px'; ?>;
				margin-left: <?php echo esc_attr($live_search_box_margin_padding[3]) . 'px'; ?>;
				padding-top: <?php echo esc_attr($live_search_box_margin_padding[4]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($live_search_box_margin_padding[5]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($live_search_box_margin_padding[6]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($live_search_box_margin_padding[7]). 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-right-sub-box {

				/*
					used to center the right side content while "ignoring" the icon on the left side. 
				    apply a calculated padding-right to col-10. 
					The calculation calc((100% / 12) * 2) calculates the width of col-2 (2 out of 12 columns) as a percentage of the row width and adds that as padding to the right of col-10. This ensures that the content inside col-10 is centered horizontally within the row, ignoring the space taken by col-2.
				*/
				padding-right: calc((100% / 12) * 2);
				/*  this conatiner will be relative to the all HTML page but now sub-divs inside can be positioned absolutely relative to this container */
				position: relative;
			}
			/*--------------------------------------------*/
            .idocs-live-search-box {
				display: flex;
   				flex-direction: row;
   				width: 100%;
				box-sizing: border-box;
   				text-align: center;
				background-color: <?php echo esc_attr($design_settings['live_search_box_background_color']); ?>; 
				background-image:   <?php
										if ($design_settings['live_search_box_background_image']) {
										?>
											url("<?php echo esc_url($design_settings['live_search_box_background_image']); ?>");
										<?php
										}
										else 
											echo esc_attr("none;");
									?>
				background-position: center; 
				background-repeat: no-repeat; 
				background-size: cover; 
				<?php
				 if (intval($design_settings['live_search_box_border_width'] != 0)) {
					?>
						border-color: <?php echo esc_attr($design_settings['live_search_box_border_color']); ?>;
						border-style: solid;
						border-radius: <?php echo esc_attr($live_search_box_border_radius) . 'px'; ?>;
						border-width: <?php echo esc_attr($design_settings['live_search_box_border_width']) . 'px'; ?>;
						
					<?php
				 }
				?>
				margin-top: <?php echo esc_attr($live_search_box_margin_padding[0]) . 'px'; ?>;
				margin-right: <?php echo esc_attr($live_search_box_margin_padding[1]) . 'px'; ?>;
				margin-bottom: <?php echo esc_attr($live_search_box_margin_padding[2]) . 'px'; ?>;
				margin-left: <?php echo esc_attr($live_search_box_margin_padding[3]) . 'px'; ?>;
				padding-top: <?php echo esc_attr($live_search_box_margin_padding[4]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($live_search_box_margin_padding[5]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($live_search_box_margin_padding[6]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($live_search_box_margin_padding[7]). 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-kb-icon {
				display: inline-block; /* This change will make the span element behave like a block-level element, allowing you to set its width, height, and other properties like margin. */
				width: <?php echo esc_attr($design_settings['live_search_kb_icon_width']) . '%'; ?>; 
				fill: <?php echo esc_attr($design_settings['live_search_kb_icon_color']); ?>; 
				opacity: <?php echo esc_attr($design_settings['live_search_kb_icon_opacity']); ?>; 
				margin-left: auto;
  				margin-right: auto;
			}
			/*--------------------------------------------*/
			.idocs-live-search-kb-icon svg{
				width: 100%; /* Make sure the SVG takes 100% of the width within the span */
				height: 100%; /* Maintain the aspect ratio */
			}
			/*--------------------------------------------*/
			.idocs-live-search-title-container {

				text-align: center; 
				padding-top: <?php echo esc_attr($design_settings['live_search_title_padding_top']) . 'px'; ?>; 
				padding-bottom: <?php echo esc_attr($design_settings['live_search_title_padding_bottom']) . 'px'; ?>; 
			}

			.idocs-live-search-title-container h2 {
				
				font-size: <?php echo esc_attr($design_settings['live_search_title_font_size']) . 'rem'; ?> !important; 
				font-weight: <?php echo esc_attr($design_settings['live_search_title_font_weight']); ?> !important;   
				color: <?php echo esc_attr($design_settings['live_search_title_color']); ?> !important; 
			}

			/*--------------------------------------------*/
			.idocs-live-search-sub-title-container h3{
				font-size: <?php echo esc_attr($design_settings['live_search_sub_title_font_size']) . 'rem'; ?> !important;
				text-align: center;
				color: <?php echo esc_attr($design_settings['live_search_sub_title_color']); ?> !important;
				padding-top: <?php echo esc_attr($design_settings['live_search_sub_title_padding_top']) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($design_settings['live_search_sub_title_padding_bottom']) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-and-output {
				position: relative;
				width: <?php echo esc_attr($design_settings['live_search_input_output_width']) . '%'; ?>; 
				min-width: 380px;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-box {
				background-color: <?php echo esc_attr($design_settings['live_search_input_box_background_color']); ?>;
				padding-top: <?php echo esc_attr($live_search_input_box_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($live_search_input_box_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($live_search_input_box_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($live_search_input_box_padding[3]) . 'px'; ?>;
				border-color: <?php echo esc_attr($design_settings['live_search_input_box_border_color']); ?>;
				border-style: solid;
				border-radius: <?php echo esc_attr($live_search_input_box_border_radius) . 'px ' ?>;
				border-width: <?php echo esc_attr($design_settings['live_search_input_box_border_width']) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-bar {
				width: <?php echo esc_attr($design_settings['live_search_input_bar_width']) . '%'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-category {
				width: <?php echo esc_attr((100-$design_settings['live_search_input_bar_width'])) . '%'; ?>;
				min-width: 200px;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-category select {
				background-color: <?php echo esc_attr($design_settings['live_search_input_box_background_color']); ?>;
				border: 1px dotted;
				font-size: <?php echo esc_attr($design_settings['live_search_input_box_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['live_search_input_box_text_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-field {
				width: 80%;
				background-color: <?php echo esc_attr($design_settings['live_search_input_box_background_color']); ?> !important;
				color: <?php echo esc_attr($design_settings['live_search_input_box_text_color']); ?> !important;
				font-size: <?php echo esc_attr($design_settings['live_search_input_box_font_size']) . 'rem'; ?> !important;
				border: none !important;	
			}
			/*--------------------------------------------*/
			.idocs-live-search-input-field::placeholder {
				color: <?php echo esc_attr($design_settings['live_search_input_box_text_placeholder_color']); ?> !important;
			}
			/*--------------------------------------------*/
			.idocs-live-search-close-search-button {
				margin: auto;
				cursor: pointer;
			}
			/*--------------------------------------------*/
			.idocs-live-search-close-search-button svg{
				fill: <?php echo esc_attr($design_settings['live_search_button_close_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-close-search-button svg:hover{
				fill: <?php echo esc_attr($design_settings['live_search_button_close_icon_hover_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result {

				position: absolute;
				/* display the search result atop the <hr> line in the document view */
				z-index: 9999; /* Set the z-index to a high value */
				top: 100%; /* Position it below the last .sub div */
				height: <?php echo esc_attr($design_settings['live_search_result_height']) . 'px'; ?>;
    			overflow-y: auto; /* Add scrollbars when content overflows */
				width: 100%;
				margin-top: 10px;
				font-size: <?php echo esc_attr($design_settings['live_search_result_item_font_size']) . 'rem'; ?>;
				background-color: <?php echo esc_attr($design_settings['live_search_result_background_color']); ?>;
				color: <?php echo esc_attr($design_settings['live_search_result_item_text_color']); ?>;
				border-color: <?php echo esc_attr($design_settings['live_search_result_border_color']); ?>;
				border-style: solid;
				/* Similar border radius and width as the search input box */
				border-radius: <?php echo esc_attr($live_search_input_box_border_radius) . 'px ' ?>;
				border-width: <?php echo esc_attr($design_settings['live_search_input_box_border_width']) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result ul{
				padding:5px 0 5px 0;
				margin:0px;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result ul a{
				color: <?php echo esc_attr($design_settings['live_search_result_item_text_color']); ?>;
				text-decoration: none;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item {
				display: flex; 
				align-items: center; 
				list-style-type: none; 
				font-size: <?php echo esc_attr($design_settings['live_search_result_item_font_size']) . 'rem'; ?>;
				padding-top: <?php echo esc_attr($live_search_result_item_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($live_search_result_item_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($live_search_result_item_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($live_search_result_item_padding[3]) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item:hover {
				color: <?php echo esc_attr($design_settings['live_search_result_item_text_hover_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['live_search_result_item_background_hover_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item-active {
				color: <?php echo esc_attr($design_settings['live_search_result_item_text_hover_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['live_search_result_item_background_hover_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item svg {
				width: <?php echo esc_attr($design_settings['live_search_result_item_icon_size']) . 'rem'; ?>;
				height: <?php echo esc_attr($design_settings['live_search_result_item_icon_size']) . 'rem'; ?>;	
				fill: <?php echo esc_attr($design_settings['live_search_result_item_icon_color']); ?>	
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item-title {

				flex: 0 0 70%;
				text-align: left; 
    			padding-left: 10px; /* Add some padding between the icon and title */
			}
			/*--------------------------------------------*/
			.idocs-live-search-result-item-category {
				flex: 0 0 25%;
    			text-align: left; /* Align the category to the right */
			}
			/*--------------------------------------------*/
			.idocs-content-filters-box {

				display: flex;
				justify-content: space-between; /* Distribute buttons evenly */
			}
			/*--------------------------------------------*/
			.idocs-content-filters-box button:focus{

				background-color: <?php echo esc_attr($design_settings['live_search_result_content_filter_background_hover_color']); ?>;	

			}
			/*--------------------------------------------*/
			.idocs-content-filter {
				flex: 1; /* Each button takes equal space */
				/*min-width: 150px; /* Set a minimum width for each button */
				background-color: <?php echo esc_attr($design_settings['live_search_result_content_filter_background_color']); ?>;	
				color: <?php echo esc_attr($design_settings['live_search_result_content_filter_text_color']); ?>;

				/*border: none; /* Remove border */ 
				outline: none; /* Remove outline */
				cursor: pointer; /* Change cursor to pointer */
				
				padding-top: <?php echo esc_attr($live_search_result_content_filter_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($live_search_result_content_filter_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($live_search_result_content_filter_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($live_search_result_content_filter_padding[3]) . 'px'; ?>;

				text-align: center; /* Center text */
				transition: background-color 0.3s; /* Add smooth background color transition */
			}
			/*--------------------------------------------*/
			.idocs-content-filter:hover {
				
				background-color: <?php echo esc_attr($design_settings['live_search_result_content_filter_background_hover_color']); ?>;	
			}
			/*--------------------------------------------*/
			.idocs-content-filter svg {
				
				/*transform: translate(-50%, -50%); */
				width: 20%; /* Set the width of the icon */
				height: auto; /* Maintain aspect ratio */
				padding: 5px;
				fill: <?php echo esc_attr($design_settings['live_search_result_content_filter_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			
			/*--------------------------------------------*/

		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		// Remove the <style> tag while keeping its content
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_kb_view_css($design_settings, $css_handler) {

		$kb_view_margin_padding = explode(",", $design_settings["kb_view_margin_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 
				.idocs-kb-view {
					background-image:   <?php
										if ($design_settings['kb_view_background_image']) {
										?>
											url("<?php echo esc_url($design_settings['kb_view_background_image']); ?>");
										<?php
										}
										else 
											echo esc_attr("none;");
									?>
					background-position: center; 
  					background-repeat: no-repeat; 
  					background-size: cover; 
					background-color: <?php echo esc_attr($design_settings['kb_view_background_color']); ?>;
					width: <?php echo esc_attr($design_settings['kb_view_width']) . '%'; ?>;
					margin-top: <?php echo esc_attr($kb_view_margin_padding[0]) . 'px'; ?>;
					margin-right: auto;
					margin-bottom: <?php echo esc_attr($kb_view_margin_padding[2]) . 'px'; ?>;
					margin-left: auto;
					padding-top: <?php echo esc_attr($kb_view_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($kb_view_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($kb_view_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($kb_view_margin_padding[7]) . 'px'; ?>;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_faqgroup_view_css($design_settings, $css_handler) {

		$faqgroup_view_margin_padding = explode(",", $design_settings["faqgroup_view_margin_padding"]);
		//$faqgroup_content_box_margin_padding = explode(",", $design_settings["faqgroup_content_box_margin_padding"]);
		/*
		.idocs-faqgroup-content-box {
					background-color: <?php echo esc_attr($design_settings['faqgroup_content_box_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['faqgroup_content_box_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['faqgroup_content_box_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['faqgroup_content_box_border_width']) . 'px'; ?>;

					margin-top: <?php echo esc_attr($faqgroup_content_box_margin_padding[0]) . 'px'; ?>;
					margin-right: <?php echo esc_attr($faqgroup_content_box_margin_padding[1]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($faqgroup_content_box_margin_padding[2]) . 'px'; ?>;
					margin-left: <?php echo esc_attr($faqgroup_content_box_margin_padding[3]) . 'px'; ?>;
					padding-top: <?php echo esc_attr($faqgroup_content_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($faqgroup_content_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($faqgroup_content_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($faqgroup_content_box_margin_padding[7]) . 'px'; ?>;
				}

		*/
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 
				
				.idocs-faqgroup-view {
					background-image:   <?php
											if ($design_settings['faqgroup_view_background_image']) {
											?>
												url("<?php echo esc_url($design_settings['faqgroup_view_background_image']); ?>");
											<?php
											}
											else 
												echo esc_attr("none;");
										?>
					background-position: center; 
					background-repeat: no-repeat; 
					background-size: cover; 
					background-color: <?php echo esc_attr($design_settings['faqgroup_view_background_color']); ?>;
					width: <?php echo esc_attr($design_settings['faqgroup_view_width']) . '%'; ?>;
					margin-top: <?php echo esc_attr($faqgroup_view_margin_padding[0]) . 'px'; ?>;
					margin-right: auto;
					margin-bottom: <?php echo esc_attr($faqgroup_view_margin_padding[2]) . 'px'; ?>;
					margin-left: auto;
					padding-top: <?php echo esc_attr($faqgroup_view_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($faqgroup_view_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($faqgroup_view_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($faqgroup_view_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_tag_view_css($design_settings, $css_handler) {

		$tag_view_margin_padding = explode(",", $design_settings["tag_view_margin_padding"]);
		$tag_content_box_margin_padding = explode(",", $design_settings["tag_content_box_margin_padding"]);
		$tag_view_title_margin_padding = explode(",", $design_settings["tag_view_title_margin_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 
				
				.idocs-tag-view {
					background-image:   <?php
											if ($design_settings['tag_view_background_image']) {
											?>
												url("<?php echo esc_url($design_settings['tag_view_background_image']); ?>");
											<?php
											}
											else 
												echo esc_attr("none;");
										?>
					background-position: center; 
					background-repeat: no-repeat; 
					background-size: cover; 
					background-color: <?php echo esc_attr($design_settings['tag_view_background_color']); ?>;
					width: <?php echo esc_attr($design_settings['tag_view_width']) . '%'; ?>;
					margin-top: <?php echo esc_attr($tag_view_margin_padding[0]) . 'px'; ?>;
					margin-right: auto;
					margin-bottom: <?php echo esc_attr($tag_view_margin_padding[2]) . 'px'; ?>;
					margin-left: auto;
					padding-top: <?php echo esc_attr($tag_view_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($tag_view_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($tag_view_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($tag_view_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-tag-content-box {
					background-color: <?php echo esc_attr($design_settings['tag_content_box_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['tag_content_box_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['tag_content_box_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['tag_content_box_border_width']) . 'px'; ?>;

					margin-top: <?php echo esc_attr($tag_content_box_margin_padding[0]) . 'px'; ?>;
					margin-right: <?php echo esc_attr($tag_content_box_margin_padding[1]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($tag_content_box_margin_padding[2]) . 'px'; ?>;
					margin-left: <?php echo esc_attr($tag_content_box_margin_padding[3]) . 'px'; ?>;
					padding-top: <?php echo esc_attr($tag_content_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($tag_content_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($tag_content_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($tag_content_box_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-tag-content-title {

					color: <?php echo esc_attr($design_settings['tag_view_title_text_color']); ?> !important;
					background-color: <?php echo esc_attr($design_settings['tag_view_title_background_color']); ?> !important;
					font-size: <?php echo esc_attr($design_settings['tag_view_title_font_size']) . 'rem'; ?> !important;

					margin-top: <?php echo esc_attr($tag_view_title_margin_padding[0]) . 'px'; ?>;
					margin-right: <?php echo esc_attr($tag_view_title_margin_padding[1]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($tag_view_title_margin_padding[2]) . 'px'; ?>;
					margin-left: <?php echo esc_attr($tag_view_title_margin_padding[3]) . 'px'; ?>;

					padding-top: <?php echo esc_attr($tag_view_title_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($tag_view_title_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($tag_view_title_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($tag_view_title_margin_padding[7]) . 'px'; ?>;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_tag_content_cards_css($design_settings, $css_handler) {

		$category_content_item_padding = explode(",", $design_settings["category_content_item_padding"]);
		$tag_content_card_padding = explode(",", $design_settings["tag_content_card_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			
			<style type="text/css"> 
				/*--------------------------------------------*/
				.idocs-content-list {
					overflow: auto; /* Add scrollbars when content overflows */
					background-color: <?php echo esc_attr($design_settings['tag_content_card_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['tag_content_card_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['tag_content_card_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['tag_content_card_border_width']) . 'px'; ?> ;
					height: <?php echo esc_attr($design_settings['tag_content_card_height']) . 'px'; ?>;

					box-shadow: <?php
									if ($design_settings['tag_content_card_show_shadow']) {
										echo esc_attr("4px 4px 4px " . $design_settings['tag_content_card_shadow_color']);
									}
									else 
										echo esc_attr("none;");  ?>;

					padding-top: <?php echo esc_attr($tag_content_card_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($tag_content_card_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($tag_content_card_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($tag_content_card_padding[3]) . 'px'; ?>;
				}
				.idocs-content-list:hover {

					background-color: <?php echo esc_attr($design_settings['tag_content_card_hover_background_color']); ?>;

				}
				/*--------------------------------------------*/
				.idocs-content-list ul{
					padding:5px 0 5px 0;
					margin:0px;
				}
				/*--------------------------------------------*/
				.idocs-content-list ul a{
					color: <?php echo esc_attr($design_settings['live_search_result_item_text_color']); ?>;
					text-decoration: none;
				}
				/*--------------------------------------------*/
				.idocs-content-item {
					display: flex; 
					align-items: center;
					list-style-type: none; /* Remove bullets */
					font-size: <?php echo esc_attr($design_settings['category_content_item_font_size']) . 'rem'; ?>;
					color: <?php echo esc_attr($design_settings['category_content_item_text_color']); ?>;
					padding-top: <?php echo esc_attr($category_content_item_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($category_content_item_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($category_content_item_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($category_content_item_padding[3]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-content-item:hover {
					color: <?php echo esc_attr($design_settings['category_content_item_text_hover_color']); ?>;
					background-color: <?php echo esc_attr($design_settings['category_content_item_background_hover_color']); ?>;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_categories_cards_css($design_settings, $css_handler) {

		$text_align_options = array(

			'value1' => 'left',
			'value2' => 'center',
			'value3' => 'right',
			
		);
		$categories_box_margin_padding = explode(",", $design_settings["categories_box_margin_padding"]);
		$category_card_padding = explode(",", $design_settings["category_card_padding"]);
		$category_content_item_padding = explode(",", $design_settings["category_content_item_padding"]);
		$sub_categories_box_padding = explode(",", $design_settings["sub_categories_box_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 

				.idocs-categories-box {
					background-color: <?php echo esc_attr($design_settings['categories_box_background_color']); ?>;
					background-image:   <?php
										if ($design_settings['categories_box_background_image']) {
										?>
											url("<?php echo esc_url($design_settings['categories_box_background_image']); ?>");
										<?php
										}
										else 
											echo esc_attr("none;");
									?>
					background-position: center; 
					background-repeat: no-repeat; 
					background-size: cover; 
					width: <?php echo esc_attr($design_settings['categories_box_width']) . '%'; ?>;
					min-height: <?php echo esc_attr($design_settings['categories_box_minimum_height']) . 'px'; ?>;
					margin-top: <?php echo esc_attr($categories_box_margin_padding[0]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($categories_box_margin_padding[2]) . 'px'; ?>;
					margin-right: auto;
					margin-left: auto;
					padding-top: <?php echo esc_attr($categories_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($categories_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($categories_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($categories_box_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-content-list {
					overflow: auto; /* Add scrollbars when content overflows */
					background-color: <?php echo esc_attr($design_settings['category_card_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['category_card_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['category_card_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['category_card_border_width']) . 'px'; ?> ;
					box-shadow: <?php
									if ($design_settings['category_card_show_shadow']) {
										echo esc_attr("5px 5px 5px #888888;");
									}
									else 
										echo esc_attr("none;");  ?>;
					height: <?php echo esc_attr($design_settings['categories_box_minimum_height']) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-content-list ul{
					padding:5px 0 5px 0;
					margin:0px;
				}
				/*--------------------------------------------*/
				.idocs-content-list ul a{
					color: <?php echo esc_attr($design_settings['live_search_result_item_text_color']); ?>;
					text-decoration: none;
				}
				/*--------------------------------------------*/
				.idocs-content-item {
					display: flex; 
					align-items: center;
					list-style-type: none; /* Remove bullets */
					font-size: <?php echo esc_attr($design_settings['category_content_item_font_size']) . 'rem'; ?>;
					color: <?php echo esc_attr($design_settings['category_content_item_text_color']); ?>;
					padding-top: <?php echo esc_attr($category_content_item_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($category_content_item_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($category_content_item_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($category_content_item_padding[3]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-content-item:hover {
					color: <?php echo esc_attr($design_settings['category_content_item_text_hover_color']); ?>;
					background-color: <?php echo esc_attr($design_settings['category_content_item_background_hover_color']); ?>;
				}
				/*--------------------------------------------*/
				.idocs-category-sub-categories {

					background-color: <?php echo esc_attr($design_settings['sub_categories_box_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['sub_categories_box_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['sub_categories_box_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['sub_categories_box_border_width']) . 'px'; ?>;
					box-shadow: <?php
									if ($design_settings['sub_categories_box_show_shadow']) {
										echo esc_attr("4px 4px 4px " . $design_settings['sub_categories_box_shadow_color']);
									}
									else 
										echo esc_attr("none;");  ?>;
					
					padding-top: <?php echo esc_attr($sub_categories_box_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($sub_categories_box_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($sub_categories_box_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($sub_categories_box_padding[3]) . 'px'; ?>;

					min-height: <?php echo esc_attr($design_settings['categories_box_minimum_height']) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				/*
				.idocs-video-overlay {
					display: none;
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background-color: rgba(0, 0, 0, 0.8);
					justify-content: center;
					align-items: center;
				}
				/*--------------------------------------------*/
				/*
				.idocs-video-popup-container {
					position: relative;
					background-color: #d55454;
					padding: 10px;
					max-width: 800px;
					max-height: 80%;
					overflow: auto;
				}
				/*--------------------------------------------*/
				/*
				.idocs-video-close-btn {
					position: absolute;
					top: 0; right: 0;
					top: 10px;
					right: 10px;
					cursor: pointer;
					padding: 5px 10px;
					background-color: red;
					border: none;
					border-radius: 5px;
					font-size: 20px;
				}
				/*--------------------------------------------*/
				/*
				.idocs-faq-overlay {
					display: none;
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background-color: rgba(0, 0, 0, 0.8);
					justify-content: center;
					align-items: center;
				}
				/*--------------------------------------------*/
				/*
				.idocs-faq-popup-container {
					position: relative;
					background-color: #d55454;
					padding: 10px;
					max-width: 800px;
					max-height: 80%;
					overflow: auto;
				}
				/*--------------------------------------------*/
				/*
				.idocs-faq-close-btn {
					position: absolute;
					top: 0; right: 0;
					top: 10px;
					right: 10px;
					cursor: pointer;
					padding: 5px 10px;
					background-color: red;
					border: none;
					border-radius: 5px;
					font-size: 20px;
				}
				/*--------------------------------------------*/
				.idocs-category-card, .idocs-category-card-detailed {
					height: <?php echo esc_attr($design_settings['category_card_height']) . 'px'; ?>;
					background-color: <?php echo esc_attr($design_settings['category_card_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['category_card_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['category_card_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['category_card_border_width']) . 'px'; ?>;
					box-shadow: <?php
									if ($design_settings['category_card_show_shadow']) {
										echo esc_attr("4px 4px 4px " . $design_settings['category_card_shadow_color']);
									}
									else 
										echo esc_attr("none;");  ?>;
					padding-top: <?php echo esc_attr($category_card_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($category_card_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($category_card_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($category_card_padding[3]) . 'px'; ?>;
					cursor: pointer; 
					visibility: <?php
					   			if ( $design_settings['categories_box_animated_categories'] ) {
									echo esc_attr("hidden");
								}
								else 
									echo esc_attr("visible");
							 ?>;
					opacity: <?php
					   			if ( $design_settings['categories_box_animated_categories'] ) {
									echo esc_attr("0");
								}
								else 
									echo esc_attr("1");
							 ?>;
					transition: opacity 0.5s ease-in-out; 
				}
				/*--------------------------------------------*/
				.idocs-category-card-detailed {
					display: flex;
					flex-direction: row;
					width: 100%;
					box-sizing: border-box;
   					text-align: center;
					height: <?php echo esc_attr($design_settings['category_card_height']) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-category-card-box-1,  .idocs-category-card-box-2 {
					flex: 0 0 40%;
					box-sizing: border-box;
					display: flex;
					justify-content: center;
					align-items: center;
					flex-direction: column;
				}
				/*--------------------------------------------*/
				.idocs-category-card-box-2 {
					flex: 0 0 60%;
					border-left: 2px solid #ccc;
				}
				/*--------------------------------------------*/
				.idocs-category-card:hover, .idocs-category-card-detailed:hover {
					background-color: <?php echo esc_attr($design_settings['category_card_hover_background_color']); ?>;
					box-shadow: <?php
									if ($design_settings['category_card_show_shadow']) {
										echo esc_attr("5px 5px 5px" . $design_settings['category_card_hover_shadow_color']);
									}
									else 
										echo esc_attr("none;");
								?>;
					
					<?php
					
						if ($design_settings['category_card_hover_transition_effect']) {

							?>
							transition: box-shadow 0.3s ease-in;
							transition: background-color 0.3s ease-in;
							<?php
						}
						
					?>
					
				}
				/*--------------------------------------------*/
				#idocs-category-link {
					text-decoration: none;
				}
				/*--------------------------------------------*/
				.idocs-category-card a {
					text-decoration: none;
				}
				/*--------------------------------------------*/
				.idocs-category-title {
					font-size: <?php echo esc_attr($design_settings['category_title_font_size']) . 'rem'; ?> !important;
					color: <?php echo esc_attr($design_settings['category_title_text_color']); ?> !important;
					text-align: <?php 
									$key = $design_settings['category_title_text_alignment']; 
									echo esc_attr($text_align_options[$key]);
								?>;
					padding-top:15px;
					overflow: hidden;
					white-space: nowrap; 
					text-overflow: ellipsis;
				}
				/*--------------------------------------------*/
				.idocs-category-title-card-layout {
					font-size: <?php echo esc_attr(0.8 * $design_settings['category_title_font_size']) . 'rem'; ?>;
					color: <?php echo esc_attr($design_settings['category_title_text_color']); ?>;
					text-align: <?php 
									$key = $design_settings['category_title_text_alignment']; 
									echo esc_attr($text_align_options[$key]);
								?>;
					padding-top:15px;
					overflow: hidden;
					white-space: nowrap; 
					text-overflow: ellipsis;
				}
				/*--------------------------------------------*/
				.idocs-category-description {
					font-size: <?php echo esc_attr($design_settings['category_description_font_size']) . 'rem'; ?>;
					color: <?php echo esc_attr($design_settings['category_description_text_color']); ?>;
					padding:0 5px 0 15px;
					text-align: left;
					overflow: hidden; 
				}
				/*--------------------------------------------*/
				.idocs-category-title-icon {
					width: <?php echo esc_attr($design_settings['category_title_icon_size']) . 'px'; ?>;
					height: <?php echo esc_attr($design_settings['category_title_icon_size']) . 'px'; ?>;
					color: <?php echo esc_attr($design_settings['category_title_icon_color']); ?>;
					display: <?php
								if ( ! $design_settings['category_title_icon_show'])
									echo esc_attr('none');
								else
									echo esc_attr('block');
							?>;
					margin-left: auto;
  					margin-right: auto;
				}
				/*--------------------------------------------*/
				.idocs-category-title-icon svg {
					fill: <?php echo esc_attr($design_settings['category_title_icon_color']); ?>;
					width: 100%; 
					height: 100%; 
				}
				/*--------------------------------------------*/
				.idocs-categories-cards-category-counter {
					color: <?php echo esc_attr($design_settings['category_title_counter_text_color']); ?>;
					background-color: <?php echo esc_attr($design_settings['category_title_counter_background_color']) ?>; 

					font-size: <?php echo esc_attr($design_settings['category_title_counter_font_size']) . 'rem'; ?>;
					font-weight: bold;
					display: <?php
								if ( ! $design_settings['category_title_show_counter'])
									echo esc_attr('none');
								else
									echo esc_attr('block');
							?>;
					text-align: <?php 
									$key = $design_settings['category_title_counter_text_alignment']; 
									echo esc_attr($text_align_options[$key]);
								?>;
				}
				/*--------------------------------------------*/
				.idocs-categories-cards-category-counter-detailed-card {
					color: <?php echo esc_attr($design_settings['category_title_counter_text_color']); ?>;
					background-color: <?php echo esc_attr($design_settings['category_title_counter_background_color']); ?>;
					font-size: <?php echo esc_attr(0.8 * $design_settings['category_title_counter_font_size']) . 'rem'; ?>;
					font-weight: bold;
					display: <?php
								if ( ! $design_settings['category_title_show_counter'])
									echo esc_attr('none');
								else
									echo esc_attr('block');
							?>;
					text-align: <?php 
									$key = $design_settings['category_title_counter_text_alignment']; 
									echo esc_attr($text_align_options[$key]);
								?>;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_faqs_css($design_settings, $css_handler) {

		$faqs_box_margin_padding = explode(",", $design_settings["faqs_box_margin_padding"]);
		$faqs_group_title_padding = explode(",", $design_settings["faqs_group_title_padding"]);
		$faqs_item_title_padding = explode(",", $design_settings["faqs_item_title_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 

				.idocs-faq-box {
					width: <?php echo esc_attr($design_settings['faqs_box_width']) . '%'; ?>;
					min-width: 250px;
					background-color: <?php echo esc_attr($design_settings['faqs_box_background_color']) ; ?>;
					margin-top: <?php echo esc_attr($faqs_box_margin_padding[0]) . 'px'; ?>;
					margin-right: auto;
					margin-bottom: <?php echo esc_attr($faqs_box_margin_padding[2]) . 'px'; ?>;
					margin-left: auto;
					padding-top: <?php echo esc_attr($faqs_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($faqs_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($faqs_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($faqs_box_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-faq-box-title {
					color: <?php echo esc_attr($design_settings['faqs_box_title_color']); ?>;
					font-size: <?php echo esc_attr($design_settings['faqs_box_title_font_size']) . 'rem'; ?>;
					display: block;
					text-align: center;
				}
				/*--------------------------------------------*/
				.idocs-faq-group-title {
					color: <?php echo esc_attr($design_settings['faqs_group_title_color']); ?>;
					font-size: <?php echo esc_attr($design_settings['faqs_group_title_font_size']) . 'rem'; ?>;
					font-weight: 600;
					display: block;
					padding-top: <?php echo esc_attr($faqs_group_title_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($faqs_group_title_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($faqs_group_title_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($faqs_group_title_padding[3]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-faq-item-title {
					color: <?php echo esc_attr($design_settings['faqs_item_title_color']); ?> !important;
					background-color: <?php echo esc_attr($design_settings['faqs_item_title_background_color']); ?> !important;
					font-size: <?php echo esc_attr($design_settings['faqs_item_title_font_size']) . 'rem'; ?> !important;
					padding-top: <?php echo esc_attr($faqs_item_title_padding[0]) . 'px'; ?> !important;
					padding-right: <?php echo esc_attr($faqs_item_title_padding[1]) . 'px'; ?> !important;
					padding-bottom: <?php echo esc_attr($faqs_item_title_padding[2]) . 'px'; ?> !important;
					padding-left: <?php echo esc_attr($faqs_item_title_padding[3]) . 'px'; ?> !important;
				}
				/*--------------------------------------------*/
				.idocs-faq-item-title:hover {
					background-color: <?php echo esc_attr($design_settings['faqs_item_title_hover_background_color']); ?> !important;
				}
				/*--------------------------------------------*/
				.idocs-faq-item-content {
					background-color: <?php echo esc_attr($design_settings['faqs_item_content_background_color']); ?> !important;
					font-size: <?php echo esc_attr($design_settings['faqs_item_content_font_size']) . 'rem'; ?> !important;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_view_css($design_settings, $css_handler) {

		$left_sidebar_box_padding = explode(",", $design_settings["left_sidebar_box_padding"]);
		$content_and_sidebars_box_margin_padding = explode(",", $design_settings["content_and_sidebars_box_margin_padding"]);
		$document_view_margin_padding = explode(",", $design_settings["document_view_margin_padding"]);
		$document_content_box_padding = explode(",", $design_settings["document_content_box_padding"]);
		$right_sidebar_box_padding = explode(",", $design_settings["right_sidebar_box_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			<style type="text/css"> 

				/*--------------------------------------------*/
				@media print {

					.exclude-from-print {
						display: none; 
					}

					.content-to-print {
						display: block !important;
					}
				}
				/*--------------------------------------------*/
				.idocs-document-view {
					background-image:   <?php
											if ($design_settings['document_view_background_image']) {
											?>
												url("<?php echo esc_url($design_settings['document_view_background_image']); ?>");
											<?php
											}
											else 
												echo esc_attr("none;");
										?>
					background-position: center; 
					background-repeat: no-repeat; 
					background-size: cover; 
					background-color: <?php echo esc_attr($design_settings['document_view_background_color']); ?>;
					width: <?php echo esc_attr($design_settings['document_view_width']) . '%'; ?>;
					margin-top: <?php echo esc_attr($document_view_margin_padding[0]) . 'px'; ?>;
					margin-right: auto;
					margin-bottom: <?php echo esc_attr($document_view_margin_padding[2]) . 'px'; ?>;
					margin-left: auto;
					padding-top: <?php echo esc_attr($document_view_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($document_view_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($document_view_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($document_view_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-content-and-sidebars {
					background-color: <?php echo esc_attr($design_settings['content_and_sidebars_box_background_color']); ?>;
					border-color: <?php echo esc_attr($design_settings['content_and_sidebars_box_border_color']); ?>;
					border-style: solid;
					border-radius: <?php echo esc_attr($design_settings['content_and_sidebars_box_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['content_and_sidebars_box_border_width']) . 'px'; ?>;
					margin-top: <?php echo esc_attr($content_and_sidebars_box_margin_padding[0]) . 'px'; ?>;
					margin-right: <?php echo esc_attr($content_and_sidebars_box_margin_padding[1]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($content_and_sidebars_box_margin_padding[2]) . 'px'; ?>;
					margin-left: <?php echo esc_attr($content_and_sidebars_box_margin_padding[3]) . 'px'; ?>;
					padding-top: <?php echo esc_attr($content_and_sidebars_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($content_and_sidebars_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($content_and_sidebars_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($content_and_sidebars_box_margin_padding[7]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-left-sidebar-box {
					background-color: <?php echo esc_attr($design_settings['left_sidebar_box_background_color']); ?>;
					padding-top: <?php echo esc_attr($left_sidebar_box_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($left_sidebar_box_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($left_sidebar_box_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($left_sidebar_box_padding[3]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-document-content-box {
					background-color: <?php echo esc_attr($design_settings['document_content_box_background_color']); ?>;	
					padding-top: <?php echo esc_attr($document_content_box_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($document_content_box_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($document_content_box_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($document_content_box_padding[3]) . 'px'; ?>;
				}

				/*--------------------------------------------*/
				#document-content ol {

					list-style: decimal;

				}				
				/*--------------------------------------------*/
				.idocs-document-metadata {
					color: <?php echo esc_attr($design_settings['document_metadata_text_color']); ?>;
					font-size: <?php echo esc_attr($design_settings['document_metadata_font_size']) . 'rem'; ?>;
				}
				/*--------------------------------------------*/
				#idocs-print-document-button {
					cursor: pointer;
				}
				/*--------------------------------------------*/
				#idocs-print-document-button svg{
					fill: <?php echo esc_attr($design_settings['document_metadata_print_color']); ?>;
				}
				/*--------------------------------------------*/
				.idocs-right-sidebar-box {
					background-color: <?php echo esc_attr($design_settings['right_sidebar_box_background_color']); ?>;
					padding-top: <?php echo esc_attr($right_sidebar_box_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($right_sidebar_box_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($right_sidebar_box_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($right_sidebar_box_padding[3]) . 'px'; ?>;
				}
				/*--------------------------------------------*/
				.idocs-star-rating {

					/*display: flex; 
					align-items: center; */
					display: inline; /* Display the main span inline */
					
				}
				.idocs-star-rating span {
    				display: inline-block; /* Display the nested spans as inline-block */
    				vertical-align: middle; /* Align the nested spans vertically in the middle */
					
				}
				.idocs-star-rating svg{
    				
					fill:orange;
				}
				/*--------------------------------------------*/
				#idocs-content-rating-score {
					color: orange; 
				}
				/*--------------------------------------------*/
				.idocs-document-navigation-links {
    				display: flex;
    				justify-content: space-between; /* Align items to the start and end of the container */
					
				}
				/*--------------------------------------------*/
				.idoc-document-nav-link {
					
    				display: inline-block;
    				padding: 10px;
				}
				.idoc-document-nav-link svg {
    				
					fill: <?php echo esc_attr($design_settings['document_navigation_link_icon_color']); ?>;
				}
				/*--------------------------------------------*/
				

		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_breadcrumbs_css($design_settings, $css_handler) {

		$breadcrumbs_box_margin_padding = explode(",", $design_settings["breadcrumbs_box_margin_padding"]);
		$breadcrumbs_box_item_padding = explode(",", $design_settings["breadcrumbs_box_item_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css"> 
			.idocs-right-breadcrumbs-box {
				background-color: <?php echo esc_attr($design_settings['breadcrumbs_box_background_color']); ?>;
				border-color: <?php echo esc_attr($design_settings['breadcrumbs_box_border_color']); ?>;
				border-style: solid;
				border-radius: <?php echo esc_attr($design_settings['breadcrumbs_box_border_radius']) . 'px'; ?>;
				border-width: <?php echo esc_attr($design_settings['breadcrumbs_box_border_width']) . 'px'; ?>;
				margin-top: <?php echo esc_attr($breadcrumbs_box_margin_padding[0]) . 'px'; ?>;
				margin-right: <?php echo esc_attr($breadcrumbs_box_margin_padding[1]) . 'px'; ?>;
				margin-bottom: <?php echo esc_attr($breadcrumbs_box_margin_padding[2]) . 'px'; ?>;
				margin-left: <?php echo esc_attr($breadcrumbs_box_margin_padding[3]). 'px'; ?>;
				padding-top: <?php echo esc_attr($breadcrumbs_box_margin_padding[4]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($breadcrumbs_box_margin_padding[5]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($breadcrumbs_box_margin_padding[6]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($breadcrumbs_box_margin_padding[7]) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-right-breadcrumbs-box a {
				text-decoration: none;
			}
			/*--------------------------------------------*/
			.idocs-right-breadcrumbs-box-item {
				background-color: <?php echo esc_attr($design_settings['breadcrumbs_box_item_background_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['breadcrumbs_box_item_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['breadcrumbs_box_item_text_color']); ?>;
				padding-top: <?php echo esc_attr($breadcrumbs_box_item_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($breadcrumbs_box_item_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($breadcrumbs_box_item_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($breadcrumbs_box_item_padding[3]) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-right-breadcrumbs-box-item:hover {	
				color: <?php echo esc_attr($design_settings['breadcrumbs_box_item_text_hover_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['breadcrumbs_box_item_hover_background_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-right-breadcrumbs-box-separator {
				color: <?php echo esc_attr($design_settings['breadcrumbs_box_separator_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['breadcrumbs_box_separator_font_size']) . 'rem'; ?>;
			}
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_navigation_css($design_settings, $css_handler) {

		$text_align_options = array(

			'value1' => 'left',
			'value2' => 'center',
			'value3' => 'right',
			
		);
		$navigation_box_padding = explode(",", $design_settings["navigation_box_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css"> 
			.idocs-navigation-box {
				background-color: <?php echo esc_attr($design_settings['navigation_box_background_color']); ?>;
				border-color: <?php echo esc_attr($design_settings['navigation_box_border_color']); ?>;
				border-style: solid;
				border-radius: <?php echo esc_attr($design_settings['navigation_box_border_radius']) . 'px'; ?>;
				border-width: <?php echo esc_attr($design_settings['navigation_box_border_width']) . 'px'; ?>;
				padding-top: <?php echo esc_attr($navigation_box_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($navigation_box_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($navigation_box_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($navigation_box_padding[3]) . 'px'; ?>;
				box-shadow: <?php
									if ($design_settings['navigation_box_show_shadow']) {
										echo esc_attr("4px 4px 4px " . $design_settings['navigation_box_shadow_color']);
									}
									else 
										echo esc_attr("none;");  ?>;
			}
			/*--------------------------------------------*/
			.idocs-accordion-header-main .btn-link {
				text-decoration: none;
				background-color: <?php echo esc_attr($design_settings['navigation_box_category_title_background_color']); ?>;
				width:100%;
				box-sizing: border-box; /* Include padding and border in the width calculation */
				padding:0px;
			}
			/*--------------------------------------------*/
			.idocs-accordion-header-main .btn-link:focus,
			.idocs-accordion-header-main .btn-link:hover,
			.idocs-accordion-header-main .btn-link.active { 

				background-color: <?php echo esc_attr($design_settings['navigation_box_category_accordion_color']); ?>;
			
			}
			/*--------------------------------------------*/
			.idocs-accordion-header-sub .btn-link {
				text-decoration: none;
				background-color: <?php echo esc_attr($design_settings['navigation_box_sub_category_title_background_color']); ?>;
				width:100%;
				box-sizing: border-box; 
				padding:0px;
			}
			/*--------------------------------------------*/
			.idocs-accordion-header-sub .btn-link:focus,
			.idocs-accordion-header-sub .btn-link:hover,
			.idocs-accordion-header-sub .btn-link.active { 
				background-color: <?php echo esc_attr($design_settings['navigation_box_category_accordion_color']); ?>;
	
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-container-main {
				background-color: <?php echo esc_attr($design_settings['navigation_box_category_title_background_color']); ?> !important;
				padding: 10px 0px 10px 10px;
				display: flex;
				align-items: center; 
				width: 94%; 
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-container-sub {
				background-color: <?php echo esc_attr($design_settings['navigation_box_sub_category_title_background_color']); ?> !important;
				padding: 10px 0px 10px 10px;
				display: flex;
				align-items: center; 
				width: 94%; 
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-title {
				
				padding-left:10px;
				font-size: <?php echo esc_attr($design_settings['navigation_box_category_title_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_category_title_text_color']); ?>;
				font-weight: <?php 
								if ( $design_settings['navigation_box_category_title_bold'] )
									echo esc_attr("bold;");
								else
									echo esc_attr("normal;");
							 ?>;
				overflow: hidden;
				white-space: nowrap; 
				text-overflow: ellipsis;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-container-main svg {

				width: <?php echo esc_attr($design_settings['navigation_box_category_icon_size']) . 'rem'; ?>;
				height: <?php echo esc_attr($design_settings['navigation_box_category_icon_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_category_icon_color']); ?>;
				display: <?php
							if ( ! $design_settings['navigation_box_show_category_icon'])
								echo esc_attr('none');
							else
								echo esc_attr('block');
						?>;
				fill: <?php echo esc_attr($design_settings['navigation_box_category_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-container-sub svg {
				width: <?php echo esc_attr($design_settings['navigation_box_category_icon_size']) . 'rem'; ?>;
				height: <?php echo esc_attr($design_settings['navigation_box_category_icon_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_category_icon_color']); ?>;
				display: <?php
							if ( ! $design_settings['navigation_box_show_category_icon'])
								echo esc_attr('none');
							else
								echo esc_attr('block');
						?>;
				fill: <?php echo esc_attr($design_settings['navigation_box_category_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-category-counter {
				
				width: <?php echo esc_attr($design_settings['navigation_box_category_counter_circle_width']) . 'rem'; ?> ;
				height: <?php echo esc_attr($design_settings['navigation_box_category_counter_circle_height']) . 'rem'; ?>;
				font-size: <?php echo esc_attr($design_settings['navigation_box_category_counter_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_category_counter_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['navigation_box_category_counter_background_color']); ?>;

				display: flex;
				justify-content: center;
				align-items: center;
				margin-left: auto;							

				border-color: <?php echo esc_attr($design_settings['navigation_box_category_counter_border_color']); ?>;
				border-style: solid;
				border-width: <?php echo esc_attr($design_settings['navigation_box_category_counter_border_width']) . 'px'; ?>;
				border-radius: <?php echo esc_attr($design_settings['navigation_box_category_counter_border_radius']) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-sub-category-title {
				padding-left:10px;
				font-size: <?php echo esc_attr($design_settings['navigation_box_sub_category_title_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_sub_category_title_text_color']); ?>;
				font-weight: <?php 
								if ( $design_settings['navigation_box_sub_category_title_bold'] )
									echo esc_attr("bold;");
								else
									echo esc_attr("normal;");
							 ?>;
				overflow: hidden;
				white-space: nowrap; 
				text-overflow: ellipsis;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-accordion-body {
				padding:0px; 
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-document-item {
				font-size: <?php echo esc_attr($design_settings['navigation_box_document_item_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_document_item_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['navigation_box_document_item_background_color']); ?>;
				text-decoration: none !important;
				display: flex; 
				align-items: center; 
				list-style-type: none; 
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-document-item-active {
				color: <?php echo esc_attr($design_settings['navigation_box_document_item_active_text_color']); ?> !important;;
				font-weight:bold;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-document-item:hover {
				background-color: <?php echo esc_attr($design_settings['navigation_box_document_item_hover_text_background_color']); ?>;
				color: <?php echo esc_attr($design_settings['navigation_box_document_item_hover_text_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-document-item-icon {
				width: <?php echo esc_attr($design_settings['navigation_box_document_item_icon_width']) . 'rem'; ?>;
				height: auto;
				color: <?php echo esc_attr($design_settings['navigation_box_document_item_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-navigation-box-document-item svg {
				width: <?php echo esc_attr($design_settings['navigation_box_document_item_icon_width']) . 'rem'; ?>;
				height: <?php echo esc_attr($design_settings['navigation_box_document_item_icon_width']) . 'rem'; ?>;
				fill: <?php echo esc_attr($design_settings['navigation_box_document_item_icon_color']); ?>;
			}	
			/*--------------------------------------------*/
			.idocs-category-counter {
				color: <?php echo esc_attr($design_settings['category_title_counter_text_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['category_title_counter_font_size']) . 'rem'; ?>;
				display: <?php
							if ( ! $design_settings['category_title_show_counter'])
								echo esc_attr('none');
							else
								echo esc_attr('block');
						?>;
				text-align: <?php 
								$key = $design_settings['category_title_counter_text_alignment']; 
								echo esc_attr($text_align_options[$key]);
							?>;
			}
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_toc_css($design_settings, $css_handler) {

		$text_align_options = array(

			'value1' => 'left',
			'value2' => 'center',
			'value3' => 'right',
			
		);
		$toc_box_padding = explode(",", $design_settings["toc_box_padding"]);
		$toc_box_title_padding = explode(",", $design_settings["toc_box_title_padding"]);
		$toc_items_padding = explode(",", $design_settings["toc_items_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
			
		<style type="text/css"> 
			.idocs-toc-box {
				background-color: <?php echo esc_attr($design_settings['toc_box_background_color']); ?>;
				border-left: <?php 
								 echo esc_attr($design_settings['toc_box_border_width']) . 'px '; 
								 echo esc_attr('solid ');	
							 	 echo esc_attr($design_settings['toc_box_border_color']); 
							?>;	
				padding-top: <?php echo esc_attr($toc_box_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($toc_box_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($toc_box_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($toc_box_padding[3]) . 'px'; ?>;
				position: sticky;
				z-index: <?php echo esc_attr($design_settings['toc_box_sticky_z_index']); ?>;
    			top: <?php echo esc_attr($design_settings['toc_box_sticky_margin_top']) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-toc-title {
				font-size: <?php echo esc_attr($design_settings['toc_box_title_font_size']) . 'rem'; ?>;
				text-align: <?php 
								$key = $design_settings['toc_box_title_alignment']; 
								echo esc_attr($text_align_options[$key]);
							?>;
				color: <?php echo esc_attr($design_settings['toc_box_title_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['toc_box_title_background_color']); ?>;
				border-bottom: <?php echo esc_attr($design_settings['toc_box_title_border_width']) . 'px'; ?> solid <?php echo esc_attr($design_settings['toc_box_title_border_color']); ?>; 
				font-weight: bold;
				padding-top: <?php echo esc_attr($toc_box_title_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($toc_box_title_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($toc_box_title_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($toc_box_title_padding[3]) . 'px'; ?>;
				margin-bottom: 10px;
			}
			/*--------------------------------------------*/
			.idocs-toc-items {
				
			}
			/*--------------------------------------------*/
			.idocs-toc-item {
				font-size: <?php echo esc_attr($design_settings['toc_items_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['toc_items_text_color']); ?>;
				overflow: hidden;
				white-space: nowrap; 
				text-overflow: ellipsis;
				padding-top: <?php echo esc_attr($toc_items_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($toc_items_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($toc_items_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($toc_items_padding[3]) . 'px'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-toc-item:hover {
				background-color: <?php echo esc_attr($design_settings['toc_items_hover_background_color']); ?>;
				color: <?php echo esc_attr($design_settings['toc_items_text_hover_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-toc-item-active {

				background-color: <?php echo esc_attr($design_settings['toc_items_hover_background_color']); ?>;
				color: <?php echo esc_attr($design_settings['toc_items_text_hover_color']); ?>;

			}
			/*--------------------------------------------*/
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_likes_css($design_settings, $css_handler) {
		
		/*
		// load js when the shortcode is called directly 
		wp_enqueue_script( 'class-idocs-like-feedback-js', 
						IDOCS_PUBLIC_URL . 'js/class-idocs-like-rating.js', 
						// Enqueue a script with jQuery as a dependency.
						array( 'jquery' ), 
						IDOCS_VERSION, 
						// Script will be loaded at the footer!!! 
						true 
					);
		*/
		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css"> 

			.idocs-like-rating-box {
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_background_color']); ?>;
				align-items: center;
				display: flex;
				justify-content: center;
				padding: 10px;
				border-color: <?php echo esc_attr($design_settings['likes_rating_box_border_color']); ?>;
				border-width: <?php echo esc_attr($design_settings['likes_rating_box_title_border_width']) . 'px'; ?>;
				border-style: solid;
			}
			/*--------------------------------------------*/
			.idocs-like-rating-box .txt {
				color: <?php echo esc_attr($design_settings['likes_rating_box_text_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['likes_rating_box_title_font_size']) . 'rem'; ?>;
				font-weight: 500;
				padding-right: 15px;
			}
			/*--------------------------------------------*/
			.idocs-like-rating-box .actions {
				margin: 0;
				padding: 0;
			}
			/*--------------------------------------------*/
			#idocs-like-button {
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_background_color']); ?>;				
				border-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_border_color']); ?>;
				border-width: 2px;
				border-style: solid;
				border-radius: 5px;
				padding: 10px;
			}
			/*--------------------------------------------*/
			#idocs-like-button svg {
				fill: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			#idocs-like-button:hover {
				color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_icon_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_hover_background_color']); ?>;
			}
			/*--------------------------------------------*/
			#idocs-dislike-button {

				background-color: <?php echo esc_attr($design_settings['likes_rating_box_no_button_background_color']); ?>;
				border-color: <?php echo esc_attr($design_settings['likes_rating_box_no_button_border_color']); ?>;
				border-width: 2px;
				border-style: solid;
				border-radius: 5px;
				padding: 10px;
			}
			/*--------------------------------------------*/
			#idocs-dislike-button svg {
				fill: <?php echo esc_attr($design_settings['likes_rating_box_no_button_icon_color']); ?>;
			}
			/*--------------------------------------------*/
			#idocs-dislike-button:hover {
				color: <?php echo esc_attr($design_settings['likes_rating_box_no_button_icon_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_no_button_hover_background_color']); ?>;
			}
			/*--------------------------------------------*/
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_five_stars_rating_css($design_settings, $css_handler) {
		
		/*
		wp_enqueue_script( 'class-idocs-like-feedback-js', 
						IDOCS_PUBLIC_URL . 'js/class-idocs-stars-rating.js', 
						// Enqueue a script with jQuery as a dependency.
						array( 'jquery' ), 
						IDOCS_VERSION, 
						// Script will be loaded at the footer!!! 
						true 
					);


					.idocs-categories-box {
					background-color: <?php echo esc_attr($design_settings['categories_box_background_color']); ?>;
					background-image:   <?php
										if ($design_settings['categories_box_background_image']) {
										?>
											url("<?php echo esc_url($design_settings['categories_box_background_image']); ?>");
										<?php
										}
										else 
											echo esc_attr("none;");
									?>
					background-position: center; 
					background-repeat: no-repeat; 
					background-size: cover; 
					width: <?php echo esc_attr($design_settings['categories_box_width']) . '%'; ?>;
					min-height: <?php echo esc_attr($design_settings['categories_box_minimum_height']) . 'px'; ?>;
					margin-top: <?php echo esc_attr($categories_box_margin_padding[0]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($categories_box_margin_padding[2]) . 'px'; ?>;
					margin-right: auto;
					margin-left: auto;
					padding-top: <?php echo esc_attr($categories_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($categories_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($categories_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($categories_box_margin_padding[7]) . 'px'; ?>;
				}
		*/

		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css"> 

			.idocs-five-stars-rating-box {
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_background_color']); ?>;
				text-align: center;
				padding: 10px;
				border-color: <?php echo esc_attr($design_settings['likes_rating_box_border_color']); ?>;
				border-width: <?php echo esc_attr($design_settings['likes_rating_box_title_border_width']) . 'px'; ?>;
				border-style: solid;

				width: 100%;
				margin-right: auto;
				margin-left: auto;
			}
			/*--------------------------------------------*/
			.idocs-rating-main-header {
				
				text-align: center;
				color: <?php echo esc_attr($design_settings['likes_rating_box_text_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['likes_rating_box_title_font_size']) . 'rem'; ?>;
				font-weight: 500;
				
			}
			/*--------------------------------------------*/
			.idocs-rating-sub-header {
				
				text-align: center;
				color: <?php echo esc_attr($design_settings['likes_rating_box_text_color']); ?>;
				font-size: <?php echo esc_attr(($design_settings['likes_rating_box_title_font_size']) -0.5). 'rem'; ?>;
				font-weight: 500;
				padding-top: 5px;
				padding-bottom: 5px;
			}
			/*--------------------------------------------*/
			.idocs-five-stars {
				align-items: center;
				display: flex;
				justify-content: center;
			}
			/*--------------------------------------------*/
			.idocs-star-button {
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_background_color']); ?>;				
				border-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_border_color']); ?>;
				border-width: 0px;
				border-style: solid;
				border-radius: 5px;
				padding: 10px;
			}
			/*--------------------------------------------*/
			.idocs-star-button svg {
				fill: orange;
			}
			/*--------------------------------------------*/
			/*
			.idocs-star-button:hover {
				color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_icon_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['likes_rating_box_yes_button_background_color']); ?>;	
			}
			*/
			/*--------------------------------------------*/
			
			/*--------------------------------------------*/
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_feedback_css($design_settings, $css_handler) {
		
		/*
		// load js when the shortcode is called directly 
		wp_enqueue_script( 'class-idocs-improve-feedback-js', 
					IDOCS_PUBLIC_URL . 'js/class-idocs-improve-feedback.js', 
					// Enqueue a script with jQuery as a dependency.
					array( 'jquery' ), 
					IDOCS_VERSION, 
					// Script will be loaded at the footer!!! 
					true 
				);
		*/
		$feedback_box_padding = explode(",", $design_settings["feedback_box_padding"]);
		ob_start();
		/*--------------------------------------------*/
		?>
		<style type="text/css"> 

			.idocs-document-feedback-box {
				background-color: <?php echo esc_attr($design_settings['feedback_box_background_color']); ?>;
				color: <?php echo esc_attr($design_settings['feedback_box_title_text_color']); ?>;
				font-size: <?php echo esc_attr($design_settings['feedback_box_title_font_size']) . 'rem'; ?>;
				padding-top: <?php echo esc_attr($feedback_box_padding[0]) . 'px'; ?>;
				padding-right: <?php echo esc_attr($feedback_box_padding[1]) . 'px'; ?>;
				padding-bottom: <?php echo esc_attr($feedback_box_padding[2]) . 'px'; ?>;
				padding-left: <?php echo esc_attr($feedback_box_padding[3]). 'px'; ?>;
				border-color: <?php echo esc_attr($design_settings['feedback_box_border_color']); ?>;
				border-width: <?php echo esc_attr($design_settings['feedback_box_border_width']) .'px'; ?>;
				border-style: solid;
			}
			/*--------------------------------------------*/
			.idocs-document-feedback-box-title {
				font-weight: 500;
				padding: 0;
				margin: 0 0 15px;
				text-align: center;
				font-size: <?php echo esc_attr($design_settings['feedback_box_title_font_size']) . 'rem'; ?>;
			}
			/*--------------------------------------------*/
			.idocs-document-feedback-items input[type="text"], 
			.idocs-document-feedback-items input[type="email"], 
			.idocs-document-feedback-items textarea{
				font-size: <?php echo esc_attr($design_settings['feedback_box_item_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['feedback_box_item_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['feedback_box_item_background_color']); ?>;
			}
			/*--------------------------------------------*/
			.idocs-document-feedback-box .submit-button {
				font-size: <?php echo esc_attr($design_settings['feedback_box_submit_button_font_size']) . 'rem'; ?>;
				color: <?php echo esc_attr($design_settings['feedback_box_submit_button_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['feedback_box_submit_button_background_color']); ?>;
				border-color: <?php echo esc_attr($design_settings['feedback_box_submit_button_border_color']); ?>;
				border-width: 2px;
				border-style: solid;
				border-radius: 5px;
				padding: 10px;
			}
			/*--------------------------------------------*/
			.idocs-document-feedback-box .submit-button:hover {
				color: <?php echo esc_attr($design_settings['feedback_box_submit_button_text_color']); ?>;
				background-color: <?php echo esc_attr($design_settings['feedback_box_submit_button_hover_background_color']); ?>;	
			}
		</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_related_documents_css($design_settings, $css_handler) {

		ob_start();
		?>
			<style type="text/css">
				.idocs-related-documents-box h5 {
					color: <?php echo esc_attr($design_settings['related_document_tags_text_color']); ?>; 
					text-transform: none;
					font-weight: 600;
					padding: 0;
					margin: 0 0 15px;
				}
				/*--------------------------------------------*/
				.idocs-related-documents-box ul {
					list-style: none;
					padding: 0;
					margin: 0;
				}
				/*--------------------------------------------*/
				.idocs-related-documents-box ul li {
					padding: 0 0 5px;
				}
				/*--------------------------------------------*/
				.idocs-related-documents-box ul li a {
					color: blue;
				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	public static function load_document_tags_css($design_settings, $css_handler) {
		
		$document_tags_box_margin_padding = explode(",", $design_settings["document_tags_box_margin_padding"]);
		$document_tags_item_padding = explode(",", $design_settings["document_tags_item_padding"]);

		/*--------------------------------------------*/
		ob_start();
		?>
			<style type="text/css">
				
				.idocs-related-document-tags-box {
					margin-top: <?php echo esc_attr($document_tags_box_margin_padding[0]) . 'px'; ?>;
					margin-right: <?php echo esc_attr($document_tags_box_margin_padding[1]) . 'px'; ?>;
					margin-bottom: <?php echo esc_attr($document_tags_box_margin_padding[2]) . 'px'; ?>;
					margin-left: <?php echo esc_attr($document_tags_box_margin_padding[3]) . 'px'; ?>;
					padding-top: <?php echo esc_attr($document_tags_box_margin_padding[4]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($document_tags_box_margin_padding[5]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($document_tags_box_margin_padding[6]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($document_tags_box_margin_padding[7]). 'px'; ?>;	
				}
				/*--------------------------------------------*/
				.idocs-related-document-tags-box h5 {
					color: <?php echo esc_attr($design_settings['related_document_tags_text_color']); ?>; 
					text-transform: none;
					font-weight: 600;
					padding: 0;
					margin: 0 0 15px;
				}
				/*--------------------------------------------*/
				.idocs-document-tags-item {

					/*
						Inline: Elements are displayed inline, meaning they flow along with text and other inline elements. They don't start on a new line and only take up as much width as necessary.
						Block: Elements are displayed as blocks, meaning they start on a new line and take up the full width available.
					*/
					display: inline-block;
					padding-top: <?php echo esc_attr($document_tags_item_padding[0]) . 'px'; ?>;
					padding-right: <?php echo esc_attr($document_tags_item_padding[1]) . 'px'; ?>;
					padding-bottom: <?php echo esc_attr($document_tags_item_padding[2]) . 'px'; ?>;
					padding-left: <?php echo esc_attr($document_tags_item_padding[3]). 'px'; ?>;	
					font-size: <?php echo esc_attr($design_settings['document_tags_item_font_size']) . 'rem'; ?>;
					
					margin-top: 10px;
					border-radius: <?php echo esc_attr($design_settings['document_tags_item_border_radius']) . 'px'; ?>;
					border-width: <?php echo esc_attr($design_settings['document_tags_item_border_width']) . 'px'; ?>;
					border-color: <?php echo esc_attr($design_settings['document_tags_item_border_color']); ?>;
					border-style: solid;
				}

				.idocs-document-tags-item a {
					text-decoration: none;
					color: <?php echo esc_attr($design_settings['document_tags_item_text_color']); ?>; 

				}
			</style>
		<?php
		/*--------------------------------------------*/
		$custom_css = ob_get_contents();
		ob_end_clean();
		wp_add_inline_style( $css_handler, strip_tags($custom_css));
	}
	/*---------------------------------------------------------------------------------------*/
	/*---------------------------------------------------------------------------------------*/
    // a callback function to a filter hook - loading a custom taxanomy ('idocs-category-taxo') archive templete 
	public function load_custom_archive_template( $template ) {
		
		// Check if the current theme supports block templates (FSE)
		//if (current_theme_supports('block-templates')) {
		if ( wp_is_block_theme() ) {

			//global $wp_query;
			//do_action( 'qm/debug', $wp_query->query_vars );
		
			return $template; // Use the default single template handled by the Site Editor
		}
		/*--------------------------------------------*/
		// Determines whether the query is for an existing archive page.
		// Archive pages include category, tag, author, date, custom post type, and custom taxonomy based archives.
		global $wp_query;
		//do_action( 'qm/debug', $wp_query->query_vars );
		//do_action( 'qm/debug', is_tax(array('idocs-category-taxo') ));;
		//do_action( 'qm/debug', get_post_type($post) == 'idocs_content');;
		$is_that_doc_category = array_key_exists('idocs-category-taxo' ,$wp_query->query_vars );
		$is_that_doc_tag = array_key_exists('idocs-tag-taxo' ,$wp_query->query_vars );
		$is_that_faqgroup = array_key_exists('idocs-faq-group-taxo' ,$wp_query->query_vars );
		//do_action( 'qm/debug', $wp_query->query_vars);
		/*--------------------------------------------*/
		// check if the archeive page includes 'idocs-kb-taxo' taxanomy - load the idocs-kb-taxo template 
		if (is_archive() && 
			is_tax('idocs-kb-taxo') && 
			! $is_that_doc_category &&
			! $is_that_doc_tag &&
			! $is_that_faqgroup
			) {
			// adjust the path and file name of new template 
			$template = IDOCS_PUBLIC_PATH . 'templates/custom_archive_kb.php';
			return $template;
		}
		/*--------------------------------------------*/
		// check if the archeive page includes two taxanomies - load the idocs-category-taxo template
		if ( is_archive()  && 
			 is_tax(array('idocs-kb-taxo','idocs-category-taxo')) && 
			 $is_that_doc_category &&
			 ! $is_that_faqgroup
			) {
			// adjust the path and file name of new template 
			$template = IDOCS_PUBLIC_PATH . 'templates/custom_archive_category.php';	
			return $template;
		}
		/*--------------------------------------------*/
		// check if the archeive page includes two taxanomies - load the idocs-tag-taxo template
		if ( is_archive()  && 
			 is_tax(array('idocs-kb-taxo', 'idocs-tag-taxo')) &&
			 $is_that_doc_tag
			) {
			// adjust the path and file name of new template 
			$template = IDOCS_PUBLIC_PATH . 'templates/custom_archive_tag.php';	
			return $template;
		  }
		/*--------------------------------------------*/
		// check if the archeive page includes two taxanomies - load the idocs-faqgroup-taxo template
		if ( is_archive()  && 
			 is_tax(array('idocs-kb-taxo', 'idocs-faq-group-taxo')) &&
			 $is_that_faqgroup
			) {
			
			// adjust the path and file name of new template 
			$template = IDOCS_PUBLIC_PATH . 'templates/custom_archive_faqgroup.php';	
			return $template;
		  }
		
	}
	/*---------------------------------------------------------------------------------------*/
    // a callback function to a filter hook - loading a custom single templete (5e5827093d)
	public function load_custom_single_template( $template ) {
		
		// Check if the current theme supports block templates (FSE)
		if ( wp_is_block_theme() ) {

			return $template; // Use the default single template handled by the Site Editor
		}
		/*--------------------------------------------*/
		if (is_singular( 'idocs_content' )) {

			// adjust the path and file name of new template 
			$template = IDOCS_PUBLIC_PATH . 'templates/custom_single_template_document.php';

		}
		/*--------------------------------------------*/
		return $template;
	}
	/*---------------------------------------------------------------------------------------*/
	public function add_faq_json_ld_to_head() {

		global $wp_query;
		/*--------------------------------------------*/
		// note - is_tax('idocs-category-taxo') is not working even if the query array includes that specific taxo.
		// therefore using the $wp_query directly and checking if the taxo included in that array.
		$doc_taxo = array_key_exists('idocs-category-taxo', $wp_query->query);
		$is_kb_view = is_archive() && is_tax('idocs-kb-taxo') && ! $doc_taxo;
		$is_cat_view = is_archive() && $doc_taxo;
		/*--------------------------------------------*/
		if ($is_kb_view) {

			//error_log('KB View');
			$kb_id = get_queried_object()->term_id;
			//error_log($kb_id);
			$faq_data = IDOCS_Shortcodes::generate_faqs_schema_for_seo( $kb_id, 0 );
			//do_action( 'qm/debug', $faq_data );
			
			if ($faq_data) {
				?>
				<script type="application/ld+json">
					<?php echo json_encode($faq_data); ?>
				</script>
				<?php
			}
			
		}
		/*--------------------------------------------*/
		if ($is_cat_view ) {

			$kb_id = get_queried_object('idocs-kb-taxo') != null ? get_queried_object()->term_id : '';
			$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
			$lock_root_faqs = $design_settings["faqs_box_lock_root_faqs"];

			if ($lock_root_faqs) { // if FAQs are locked, then don't create the same FAQ schema for the categories 

				return null;

			}
			else {
				// getting the second taxonomy (category) from the wp_query object 
				$category_slug = $wp_query->query_vars['idocs-category-taxo'];
				// translate the slug to term_id 
				$term = IDOCS_Taxanomies::get_specific_category_term_by_slug_caching($category_slug);
				//$term = get_term_by('slug', $category_slug, 'idocs-category-taxo');
				$cat_id = $term->term_id;
				$faq_data = IDOCS_Shortcodes::generate_faqs_schema_for_seo( $kb_id, $cat_id );
				//do_action( 'qm/debug', $faq_data );
				if ($faq_data) {
					?>
					<script type="application/ld+json">
						<?php echo json_encode($faq_data); ?>
					</script>
					<?php
				}
			}
		}

	}
	/*---------------------------------------------------------------------------------------*/
	public function add_breadcrumbs_json_ld_to_head() {

		global $wp_query;
		/*--------------------------------------------*/
		// note - is_tax('idocs-category-taxo') is not working even if the query array includes that specific taxo.
		// therefore using the $wp_query directly and checking if the taxo included in that array.
		$cat_taxo = array_key_exists('idocs-category-taxo', $wp_query->query);
		$is_kb_view = is_archive() && is_tax('idocs-kb-taxo') && ! $cat_taxo;
		$is_cat_view = is_archive() && $cat_taxo;
		/*--------------------------------------------*/
		if ($is_kb_view || $is_cat_view) {

			//error_log('KB View');
			$kb_id = get_queried_object()->term_id;
			//error_log($kb_id);
			$schema_data = IDOCS_Shortcodes::generate_breadcrumbs_schema_for_seo( $kb_id );
			//do_action( 'qm/debug', $faq_data );
			
			if ($schema_data) {
				?>
				<script type="application/ld+json">
					<?php echo json_encode($schema_data); ?>
				</script>
				<?php
			}
			
		}
		/*--------------------------------------------*/
	}
	/*---------------------------------------------------------------------------------------*/
}

// https://wordpress.stackexchange.com/questions/326013/load-css-in-footer-like-your-can-with-js
// https://www.giftofspeed.com/defer-loading-css/
// https://developer.wordpress.org/reference/functions/wp_register_script/
// https://wpbeaches.com/add-javascript-css-files-head-wordpress/