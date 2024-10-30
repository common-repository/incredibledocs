<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* 
	Front-end shortcodes (a shortcode must return a value!)

/*---------------------------------------------------------------------------------------*/
class IDOCS_Shortcodes {

	/*---------------------------------------------------------------------------------------*/
	public function __construct() {

	}
	/*---------------------------------------------------------------------------------------*/
	// utility method to define the list of shortcodes to display 
	public static function get_core_shortcodes_list () {
			
		return array (
			
			[ 'Core', 'Knowledge-Base View', '[idocs_kb_view]', 'kb_id, optional - category_id' ],

			[ 'Core','Document View', '[idocs_document_view]', 'kb_id, content_id' ],

			[ 'Core','Live Search', '[idocs_live_search]', 'kb_id' ],

			[ 'Core','Categories Cards', '[idocs_categories_cards]', 'kb_id, category_id (0 = kb root)' ],

			[ 'Core','KB FAQs', '[idocs_kb_faqs]', 'kb_id' ],

			[ 'Core','Category FAQs', '[idocs_category_faqs]', 'kb_id, category_id' ],

			[ 'Core','FAQs Group', '[idocs_faqs_group]', 'kb_id, faqs_group_id' ],
			
			[ 'Core','Document Tags', '[idocs_document_tags]', 'kb_id, content_id' ],
			
			[ 'Core','Related Documents', '[idocs_related_documents]', 'kb_id, content_id' ],

		);
		
	}	
	/*---------------------------------------------------------------------------------------*/
	// ROADMAP - utility method to define the list of shortcodes to display for the pro version 
	public static function get_pro_shortcodes_list () {
			
		return array (

			[ 'Pro','Document Like Rating', '[idocspro_document_like_rating]', 'kb_id, content_id' ],

			[ 'Pro','Document Feedback', '[idocspro_document_feedback]', 'kb_id, content_id' ],

			[ 'Pro','Popular Content', '[idocspro_popular_content]', 'kb_id, category_id (0 = all categories), last_days, top_n' ],

			[ 'Pro','Popular Search Keywords', '[idocspro_popular_search_keywords]', 'kb_id, last_days, top_n' ],

			[ 'Pro','Popular Tags', '[idocspro_popular_tags]', 'kb_id, last_days, top_n' ],

			[ 'Pro','Content by Tag', '[idocspro_content_by_tag]', 'tag_id' ],

			[ 'Pro','Top Rated Documents', '[idocspro_top_rated_documents]', 'kb_id, category_id (0 = all categories), last_days, top_n' ],

			[ 'Pro','Top Rated Authors', '[idocspro_top_rated_authors]', 'kb_id, category_id (0 = all categories), last_days, top_n' ],
			
		);
	}	
	/*---------------------------------------------------------------------------------------*/
	// [idocs_kb_view]
	public function idocs_kb_view ( $atts ) {

		/*--------------------------------------------*/
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$category_id = isset( $atts ['category_id'] ) ? intval(sanitize_text_field($atts ['category_id'])) : 0;
		ob_start();
		/*--------------------------------------------*/
		if (self::kb_and_category_check($kb_id, 0)) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_view.php';

		};
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_live_search]
	public function idocs_live_search ( $atts, $content = null ) {
		
		/*--------------------------------------------*/
		// get the current knowledge-base id 
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		ob_start();		
		/*--------------------------------------------*/
		if (self::kb_and_category_check($kb_id, 0)) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_live_search.php';

		};
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function check_for_any_direct_content ( $category_id ) {

		/*--------------------------------------------*/
		// cache data is removed when any content item is updated - "update_content_post"
		$cached_data =  get_transient( 'idocs_transient_direct_content_flags');
		// If the cached data is not found, fetch it from the database
		// or that info for that category id is not available in the cach
		if ( false === $cached_data || !(isset($cached_data[$category_id])) ) {
		
			//error_log('no cache data or the info for that category not available');

			// get the list of all terms under the 'idocs-content-type-taxo' taxanomy 
			$terms = IDOCS_Taxanomies::get_content_types_terms_caching();
			/*------------------------------------------------*/
			$types = array();
			// reduce the result to the term id
			foreach ($terms as $term) {

				// exclude the FAQ content item which is displayed in a dedicated FAQs area 
				if ($term->name !='FAQ') {

					array_push($types, $term->term_id);

				}
			}
			/*--------------------------------------------*/
			$args = array(
				'post_type' => 'idocs_content',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'tax_query' => array(
					/*--------------------------------------------*/
					'relation' => 'AND', // Add this line to ensure both conditions are met
					array(
						'taxonomy' => 'idocs-category-taxo',
						'field' => 'term_id',
						'terms' => $category_id,
						'include_children' => false, // Exclude posts from sub-categories
						'operator' => 'IN',
					),
					array(
						'taxonomy'         => 'idocs-content-type-taxo', 
						'field'            => 'term_id',
						'terms'            => $types, 
						'operator'         => 'IN',
					),
					/*--------------------------------------------*/
				)
			);
			/*--------------------------------------------*/
			$the_query = new WP_Query( $args ); 
			$total_docs = $the_query->post_count;
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that category
			//error_log('setting caching for that category');
			if ($total_docs == 0) {
				$cached_data[$category_id] = 0;
				set_transient( 'idocs_transient_direct_content_flags', $cached_data, 10800);
				return false;
			}
			else {
				$cached_data[$category_id] = 1;
				set_transient( 'idocs_transient_direct_content_flags', $cached_data, 10800);
				return true;
			}
		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that category 
		else {
			//error_log('getting direct content flag from the cache');
			if ($cached_data[$category_id] == 0) { // false
				return false;
			}
			if ($cached_data[$category_id] == 1) { // true
				return true;
			}
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function kb_and_category_check($kb_id, $category_id) {

		/*--------------------------------------------*/
		$kb_exist = term_exists($kb_id, 'idocs-kb-taxo');
		$check = true;
		/*--------------------------------------------*/
		if ( ! $kb_exist  ) {

			?>
			<p>Error: knowledge base id does not exist</p>
			<?php

			$check = false;
		};
		/*--------------------------------------------*/
		if ( $kb_exist && $category_id != 0 ) {

			$category_exist = term_exists($category_id, 'idocs-category-taxo');
			if ( ! $category_exist ) {

				?>
				<p>Error: category id does not exist</p>
				<?php

			};
			$check = false;
		};
		/*--------------------------------------------*/
		return $check;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function document_check($document_id) {

		/*--------------------------------------------*/
		$document_exist = get_post($document_id);
		$check = true;
		/*--------------------------------------------*/
		if ( ! $document_exist  ) {

			?>
			<p>Error: document id does not exist</p>
			<?php

			$check = false;
		};
		/*--------------------------------------------*/
		return $check;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_categories_cards]
	public function idocs_categories_cards ( $atts ) {

		/*--------------------------------------------*/
		// knowledge-base master filter 
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$category_id = isset($atts['category_id']) ? intval(sanitize_text_field($atts['category_id'])) : 0;
		ob_start();
		/*--------------------------------------------*/
		if (self::kb_and_category_check($kb_id, $category_id)) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_categories_cards.php';

		};
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function faqs_per_group ( $faq_group_id ) {

		// get the list of all faqs related to specific faq-group
		$args = array(

			'post_type' => 'idocs_content',
			'posts_per_page' => -1,
			
			'tax_query' => array(
				array(
					'taxonomy' => 'idocs-faq-group-taxo',
					'field' => 'term_id',
					'terms' => $faq_group_id,
					'operator' => 'IN',
					'include_children' => false,
				)
			),
			'meta_key' => 'idocs-content-display-order-meta',
    		'orderby'  => 'meta_value_num',
			'order' => 'ASC',

		);
		/*--------------------------------------------*/
		$the_query = new WP_Query( $args );
		return $the_query; 	
	}
	/*---------------------------------------------------------------------------------------*/
	public static function faqs_per_group_caching ( $faq_group_id ) {

		$cached_data =  get_transient( 'idocs_transient_faqs_per_group');
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that faq_group id is not available in the cach
		if ( false === $cached_data || !(isset($cached_data[$faq_group_id])) ) {

			$faqs_terms = self::faqs_per_group($faq_group_id);
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that category
			//error_log('setting caching for that category');
			$cached_data[$faq_group_id] = $faqs_terms;
			//error_log('setting cache - faqs per group');
			set_transient( 'idocs_transient_faqs_per_group', $cached_data, 10800);
			return $faqs_terms;
		}
		/*--------------------------------------------*/
		else {

			//error_log('getting stored cache - faqs per group');
			return $cached_data[$faq_group_id];

		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function generate_faqs_schema_for_seo( $kb_id, $category_id ) {
		
		// try to get the transient cache 
		$cached_data = get_transient( 'idocs_transient_faqs_schema_for_seo_' . $kb_id );
		/*--------------------------------------------*/
		// faqs schema cache is not available 
		if ( false === $cached_data || !(isset($cached_data[$category_id])) ) {

			$faqs_array = array ();
			//$faq_groups = IDOCS_Shortcodes::faq_groups_per_kb_and_category ( $kb_id, $category_id );
			$faq_groups = IDOCS_Taxanomies::get_groups_terms_per_category_caching($kb_id, $category_id);
			/*--------------------------------------------*/
			if ($faq_groups == null) {
				return null;;
			}
			/*--------------------------------------------*/
			foreach ($faq_groups as $faq_group) {

				$the_query = IDOCS_Shortcodes::faqs_per_group ( $faq_group->term_id );
				while ( $the_query->have_posts() ) {

					$the_query->the_post();
					$faq_group_name = $faq_group->name;

					$faqs_array[$faq_group_name][] = array( 
						'Question' => get_the_title(),
						'Answer' => get_the_content()
					);
				
				}
			}
			/*--------------------------------------------*/
			$faq_data = array(
				"@context" => "https://schema.org",
				"@type" => "WebPage", // the page is used to display both the main categories of a knowledge base and the FAQs
				"mainEntity" => array()
			);
			/*--------------------------------------------*/
			foreach ($faqs_array as $faq_group_name => $faqs) {

				$faq_group_entity = array(
					"@type" => "ItemList",
					"name" => $faq_group_name,
					"itemListElement" => array()
				);
			
				foreach ($faqs as $faq) {
					$faq_group_entity["itemListElement"][] = array(
						"@type" => "Question",
						"name" => $faq['Question'],
						"acceptedAnswer" => array(
							"@type" => "Answer",
							"text" => $faq['Answer']
						)
					);
				}
			
				$faq_data["mainEntity"][] = $faq_group_entity;
			}
			/*--------------------------------------------*/
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}

			$cached_data[$category_id] = $faq_data;
			set_transient( 'idocs_transient_faqs_schema_for_seo_' . $kb_id, $cached_data, 10800);
			//error_log("stored the faqs data in caching");
			return $faq_data;	
		}
		/*--------------------------------------------*/
		else {

			//error_log("access faqs data from the caching");
			return $cached_data[$category_id];

		}
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_kb_faqs]
	public function idocs_kb_faqs ( $atts ) {

		/*--------------------------------------------*/
		// knowledge-base master filter 
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$category_id = 0;
		/*--------------------------------------------*/
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_faqs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_category_faqs]
	public function idocs_category_faqs ( $atts ) {

		/*--------------------------------------------*/
		// knowledge-base master filter 
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$category_id = isset($atts['category_id']) ? intval(sanitize_text_field($atts['category_id'])) : 0;
		/*--------------------------------------------*/
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_faqs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_faqs_group]
	public function idocs_faqs_group ( $atts ) {

		/*--------------------------------------------*/
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$faqs_group_id = intval(sanitize_text_field($atts['faqs_group_id']));
		$faq_group_term = get_term_by('id', $faqs_group_id, 'idocs-faq-group-taxo');
		$faq_group_label = $faq_group_term ->name; 
		/*--------------------------------------------*/
		ob_start();
		/*--------------------------------------------*/
		// Display FAQ Group
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_faq_group.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_faqs]
	public function idocs_faqs ( $atts ) {

		/*--------------------------------------------*/
		// knowledge-base master filter 
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		$category_id = isset($atts['category_id']) ? intval(sanitize_text_field($atts['category_id'])) : 0;
		/*--------------------------------------------*/
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_faqs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function total_content_type_in_category( $cat_id, $content_type ) {

		// cache data is removed when any cpt is added/updated (as the content type per category can change)
		$cached_data =  get_transient( 'idocs_transient_total_content_types' );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that cat_id & content_type is not available in the cache
		if ( false === $cached_data || !( isset($cached_data[$cat_id][$content_type]) ) ) {

			//error_log('no cache data or the info of that content_type:' . $content_type .' for that category is not available');
			$term = get_term_by('slug', $content_type, 'idocs-content-type-taxo');
			// handle scenario of accessing content types that are part of the pro
			if ($term == null ) {

				$total = 0;
			} 
			else {

				$args = array(
					'post_type' => 'idocs_content',
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
					'tax_query' => array(
						/*--------------------------------------------*/
						'relation' => 'AND',
						array(
							'taxonomy' => 'idocs-category-taxo',
							//'field' => 'slug',
							//'terms' => $slug,
							'field' => 'term_id',
							'terms' => $cat_id,
							'operator' => 'IN',
							'include_children' => true,
						),
						array(
							'taxonomy'         => 'idocs-content-type-taxo', 
							'field'            => 'term_id',
							'terms'            => $term->term_id, 
							'operator'         => 'IN',
						),
						/*--------------------------------------------*/
					)
				);
				$the_query = new WP_Query( $args ); 
				$total = $the_query->post_count;
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data available but not on that post
			//error_log('setting cache for that post - total content type');
			// add the complete term object to the cached_data array 
			$cached_data[$cat_id][$content_type] = $total;
			set_transient( 'idocs_transient_total_content_types', $cached_data, 10800);
			//do_action( 'qm/debug', $category_term );
			return $total;

		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that category
		else {

			//error_log('getting total content:'. $content_type . ' from the cache');
			//do_action( 'qm/debug', $cached_data[$term_slug] );
			return $cached_data[$cat_id][$content_type];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function display_content_item ( $content_type, $post_id, $icon_size, $category_content_item_icon_color, &$video_counter, $show_faqs  ) {

		switch ( $content_type ) {
			/*--------------------------------------------*/
			case "Document":
				?>
				<a href="<?php echo esc_url(get_permalink()); ?>" title = "Document Content Item Link"> 
					<div class="idocs-content-item">
						<?php IDOCS_ICONS::echo_icon_svg_tag('rectangle-list', $icon_size, $icon_size, 1, $category_content_item_icon_color);?> 
						<span class="idocs-content-item-title">
							<?php echo esc_html(ucfirst(get_the_title())); ?>
						</span>
					</div>															
				</a>
				<?php
				break;
			/*--------------------------------------------*/
			case "Link":
				$newtab = false;
				$newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
				//$newtab = get_post_meta($post_id, 'idocs-content-newtab-meta', true);
				$link_url = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-link-meta');
				//$link_url = get_post_meta($post_id, 'idocs-content-link-meta', true);
				if (empty($link_url)) {
					$link_url = esc_url("link_not_configured");
				}
				?>
					<a  href="<?php echo esc_url($link_url); ?>"
							<?php if ($newtab) echo esc_attr(" target=_blank");?>>
						<div class="idocs-content-item">
							<?php IDOCS_ICONS::echo_icon_svg_tag('link', $icon_size, $icon_size, 1, $category_content_item_icon_color);?> 
							<span class="idocs-content-item-title">
								<?php echo esc_html(ucfirst(get_the_title())); ?>
							</span>
						</div>	
					</a>										
				<?php
				break;
			/*--------------------------------------------*/
			case "FAQ":

				if ($show_faqs) {

					?>
					<a href="<?php echo esc_url(get_permalink()); ?>" title = "FAQ Content Item Link"> 
						<div class="idocs-content-item">
							<?php IDOCS_ICONS::echo_icon_svg_tag('circle-question', $icon_size, $icon_size, 1, $category_content_item_icon_color);?> 
							<span class="idocs-content-item-title">
								<?php echo esc_html(ucfirst(get_the_title())); ?>
							</span>
						</div>															
					</a>
					<?php

				}
				break;
			/*--------------------------------------------*/
			case "Internal-Video":
				// display pro content 
				$pdf_counter = 0;
				do_action( 'idocspro_display_video', $content_type, $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $pdf_counter );		
				$video_counter++;
				break;
			/*--------------------------------------------*/
			case "YouTube-Video":
				$pdf_counter = 0;
				// display pro content 
				do_action( 'idocspro_display_video', $content_type, $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $pdf_counter );		
				$video_counter++;
				break;	
		};
	}
	/*---------------------------------------------------------------------------------------*/
	// idocs_cards_category_with_docs
	public function idocs_cards_category_with_docs ( $atts ) {

		/*--------------------------------------------*/
		$category_id = intval(sanitize_text_field($atts ['category_id']));
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_category_with_docs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_cards_category_no_docs]
	public function idocs_cards_category_no_docs ( $atts ) {

		$category_id = intval(sanitize_text_field($atts ['category_id']));
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_category_no_docs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function display_category_counters($total_docs, $total_links, $total_videos, $total_faqs, $css_class ) {

		?>
		<div class="<?php echo esc_attr($css_class);?>">
			<?php 
			if ($total_docs) {
				if ($total_docs > 1) {
					?>
					<?php echo esc_html($total_docs . ' Docs'); ?>
					<?php
				} else {
					?>
					<?php echo esc_html($total_docs . ' Doc'); ?>
					<?php
				}
			}
			/*--------------------------------------------*/
			if ($total_docs && $total_links) {
				?>
				<?php echo esc_html(' | '); ?>
				<?php
			}
			/*--------------------------------------------*/
			if ($total_links) {
				if ($total_links > 1) {
					?>
					<?php echo esc_html( $total_links . ' Links'); ?>
					<?php
				} else {
					?>
					<?php echo esc_html( $total_links . ' Link'); ?>
					<?php
				}
			}
			/*--------------------------------------------*/
			if ($total_videos && ($total_docs || $total_links)) {
				?>
				<?php echo esc_html(' | '); ?>
				<?php
			}
			/*--------------------------------------------*/
			if ($total_videos) {
				if ($total_videos > 1) {
					?>
					<?php echo esc_html($total_videos . ' Videos'); ?>
					<?php
				} else {
					?>
					<?php echo esc_html($total_videos . ' Video'); ?>
					<?php
				}
			}
			/*--------------------------------------------*/
			if ($total_faqs && ($total_docs || $total_links || $total_videos)) {
				?>
				<?php echo esc_html(' | '); ?>
				<?php
			}
			/*--------------------------------------------*/
			if ($total_faqs) {
				if ($total_faqs > 1) {
					?>
					<?php echo esc_html($total_faqs . ' FAQs'); ?>
					<?php
				} else {
					?>
					<?php echo esc_html($total_faqs . ' FAQ'); ?>
					<?php
				}
			}
			/*--------------------------------------------*/
			if (!$total_docs && !$total_links && !$total_videos && !$total_faqs) {
				?>
				<?php echo esc_html__('No Content', 'incredibledocs'); ?>
				<?php
			}
			?>
		</div>
		<?php 
	}
	/*---------------------------------------------------------------------------------------*/
	/* <div class="col-<?php echo esc_attr($col_size); ?> d-flex justify-content-center" style="min-width: 250px;">	 */

	public static function high_level_card_layout($col_size, $category_index, $kb_id, $wp_term, $category_icon_url, $total_docs, $total_links, $total_videos, $total_faqs) {

		$term_permalink = get_term_link ($wp_term->slug, 'idocs-category-taxo');
		/*--------------------------------------------*/
		?>
		<div class="col-<?php echo esc_attr($col_size); ?>" style="min-width: 250px;">	
			<div class="idocs-category-card" id="idocs-category-card-<?php echo esc_attr($category_index); ?>" data-kb_id="<?php echo esc_attr($kb_id); ?>" data-category_id="<?php echo esc_attr($wp_term->term_id); ?>" >
				<a href="<?php echo esc_url($term_permalink); ?>" title = "Category Card Link">
						<?php
						/*--------------------------------------------*/
						if ( empty ($category_icon_url) ) {
							// use the icon picker 
							//$category_icon_key =  get_term_meta( $wp_term->term_id, 'idocs-category-taxo-icon-picker', true );
							$category_icon_key = IDOCS_Taxanomies::get_term_meta_caching(  $wp_term->term_id, 'idocs-category-taxo-icon-picker', false);

							if ( $category_icon_key != null ) {
								// how to use: class="idocs-category-title-icon"
								?>
								<span class="idocs-category-title-icon">
									<?php IDOCS_ICONS::echo_icon_svg_tag($category_icon_key, 0, 0, 1);?>
								</span>
								<?php
							}
							else {
								?>
								<p><?php echo esc_html__( 'Missing category icon', 'incredibledocs' );?></p>
								<?php
							}
						} else { // use the custom icon

							?>
								<img class="idocs-category-title-icon" src="<?php echo esc_url($category_icon_url); ?>" alt="category icon" >
							<?php
						}
						/*--------------------------------------------*/
						?>
						<h4 class="idocs-category-title"> <?php echo esc_html(ucfirst($wp_term->name)); ?> </h4>
						<?php
						IDOCS_Shortcodes::display_category_counters($total_docs, $total_links, $total_videos, $total_faqs, "idocs-categories-cards-category-counter" );
						/*--------------------------------------------*/
						?>
				</a>
			</div>			
		</div>
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	
	/*---------------------------------------------------------------------------------------*/
	public static function detailed_card_layout($col_size, $category_index, $kb_id, $wp_term, $category_icon_url, $total_docs, $total_links, $total_videos, $total_faqs) {

		$term_permalink = get_term_link ($wp_term->slug, 'idocs-category-taxo');
		/*--------------------------------------------*/
		?>
		<div class="col-<?php echo esc_attr($col_size); ?>" style="min-width: 220px;">
			<a id="idocs-category-link" href="<?php echo esc_url($term_permalink); ?>" >
				<div class="idocs-category-card-detailed" id="idocs-category-card-<?php echo esc_attr($category_index); ?>" data-kb_id="<?php echo esc_attr($kb_id); ?>" data-category_id="<?php echo esc_attr($wp_term->term_id); ?>" >
						<!---------------------------------------------->
						<div class="idocs-category-card-box-1">
							<?php
							if ( empty ($category_icon_url) ) {
								// use the icon picker
								$category_icon_key = IDOCS_Taxanomies::get_term_meta_caching(  $wp_term->term_id, 'idocs-category-taxo-icon-picker', false); 
								//$category_icon_key =  get_term_meta( $wp_term->term_id, 'idocs-category-taxo-icon-picker', true );
								
								if ( $category_icon_key != null ) {
									// how to use: class="idocs-category-title-icon"
									?>
									
										<div class="idocs-category-title-icon">
											<?php IDOCS_ICONS::echo_icon_svg_tag($category_icon_key, 0, 0, 1);?>
										</div>
									<?php
									
								}
								else {
									?>
									<p><?php echo esc_html__( 'Missing category icon', 'incredibledocs' );?></p>
									<?php
								}
							} else { // use the custom icon

								?>
									<div>
										<img class="idocs-category-title-icon" src="<?php echo esc_url($category_icon_url); ?>" alt="category icon" >
									<div>
								<?php
							}
							/*--------------------------------------------*/
							?>
							<h4 class="idocs-category-title-card-layout"> <?php echo esc_html(ucfirst($wp_term->name)); ?> </h4>
							<?php
							IDOCS_Shortcodes::display_category_counters($total_docs, $total_links, $total_videos, $total_faqs, "idocs-categories-cards-category-counter-detailed-card" );
							/*--------------------------------------------*/
							?>
						</div>
						<!---------------------------------------------->	
						<div class="idocs-category-card-box-2">
							<div class="idocs-category-description">
								<?php echo esc_html($wp_term->description); ?> 
							</div> 
						</div>
						<!---------------------------------------------->		
				</div>
			</a>			
		</div>
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// idocs_cards_root
	public function idocs_cards_root ( $atts ) {
		
		/*--------------------------------------------*/
		$kb_id = intval(sanitize_text_field($atts ['kb_id']));
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_root.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// utility function using recursion to create the breadcrumb 
	public static function build_breadcrumb($category) {

		$breadcrumbs = array ();
		if ($category == null) return $breadcrumbs;
		// going backward from category to category parent 
		// check if this is a root main category or a sub-category 
		if($category->parent != 0 ) {

			$parent = get_term_by( 'term_id', $category->parent, 'idocs-category-taxo' );
			$parent_breadcrumbs = IDOCS_Shortcodes::build_breadcrumb($parent);
			foreach ($parent_breadcrumbs as $breadcrumb) {

				$breadcrumbs[] = $breadcrumb;	

			}
		}
		//do_action( 'qm/debug', get_term_link($category, 'idocs-category-taxo' ));
		$breadcrumbs[] = array($category->name, get_term_link($category, 'idocs-category-taxo' )) ;
		/*--------------------------------------------*/
		return $breadcrumbs;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_breadcrumbs]
	public function idocs_breadcrumbs ( $atts, $content = null ) {

		/*--------------------------------------------*/
		$doc_id = intval(sanitize_text_field($atts ['document_id']));
		$kb_id =  intval(sanitize_text_field($atts ['kb_id']));
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_breadcrumbs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_kb_breadcrumbs]
	public function idocs_kb_breadcrumbs ( $atts, $content = null ) {

		/*--------------------------------------------*/
		$category_id = intval(sanitize_text_field($atts ['category_id']));
		$kb_id =  intval(sanitize_text_field($atts ['kb_id']));
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_breadcrumbs.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	// utility function to generate TOC for a document content (for block-based themes)
	public static function generate_toc_block_based_theme ( $content, $attributes ) {

		/*--------------------------------------------*/
		$toc_title = $attributes['toc_title_text'];
		$toc_header_start = substr($attributes['toc_header_start'], -1, 1);
		$toc_header_end = substr($attributes['toc_header_end'], -1, 1);
		/*--------------------------------------------*/
		ob_start();
		?>
		<nav class="idocs-toc-box">
			<div class="idocs-toc-title">			
				<?php echo esc_html($toc_title); ?>
			</div>
			
			<div class='idocs-toc-items'>
				
				<?php

				$pattern = '#<(h['. $toc_header_start . '-' . $toc_header_end .  '])(.*?)>(.*?)</\1>#si';

				$index = 1;
				$content = preg_replace_callback($pattern, function ($matches) use (&$index, &$tableOfContents) {
				
					// use the first grouping as the $tag.
					$tag = $matches[1];
					// uses the 3rd grouping as the title of the heading, 
					// but it strips the tags first, because there are cases where the heading contains other HTML elements, like <a> or <strong>. 
					$title = strip_tags($matches[3]);
					// Checks if the currently matched element already has an id attribute set. 
					// If it does, it’s going to be stored in the $matchedIds variable.
					$hasId = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[2], $matchedIds);
					// Create the unique ID that’s going to be used as a href attribute on the TOC, and as an ID for the actual heading.
					// First, we check if it already had an ID. If it did, we’re going to use that one. 
					// However, if it did not have one, we’re going to create one based on the $index and the ‘slugified’ version of the title.
					// Generating one from the heading content, using a built-in WordPress function called sanitize_title().
					$id = $hasId ? $matchedIds[2] : $index++ . '-' . sanitize_title($title);
					// appending to the TOC the new element.
					?>	
					<a href="#<?php echo esc_attr($id);?>" style="text-decoration: none;" title = "TOC Item Link"> 
						<div class="idocs-toc-item toc-item-<?php echo esc_attr($tag);?>" data-section-id="<?php echo esc_attr($id);?>">
							<?php echo esc_html($title); ?> 
						</div>
					</a>
					<?php

					// We’re checking if it had an ID, and if it did, we’re returning the matched string, untouched.
					// If it did not have an ID, we’re going to return a modified version of the matched heading HTML, which includes the ID. 
					// This is essential in order to be able to link from the TOC to the heading itself through basic browser functionalities.

					if ($hasId) {
						return $matches[0];
					}
					return sprintf('<%s%s id="%s">%s</%s>', $tag, $matches[2], $id, $matches[3], $tag);
				}, $content);
				
				// appends a closing <div> tag to the TOC.
				?>
			</div>
		</nav>
		<?php
		/*--------------------------------------------*/
		$toc = ob_get_contents();
		ob_end_clean();
		// return the modified $content which now includes ID attributes for all the headings.
		return array ($content, $toc);

	}
	/*---------------------------------------------------------------------------------------*/
	// utility function to generate TOC for a document content (for non block-based themes)
	public static function generate_toc ( $content, $current_kb_id ) {

		/*--------------------------------------------*/
		$design_settings = IDOCS_Database::get_plugin_design_settings($current_kb_id, null);
		$toc_title = $design_settings['toc_box_title_text'];
		$toc_header_start = substr($design_settings['toc_items_header_start'], -1, 1);
		$toc_header_end = substr($design_settings['toc_items_header_end'], -1, 1);
		/*--------------------------------------------*/
		ob_start();
		?>
		<nav class="idocs-toc-box">
			<div class="idocs-toc-title">			
				<?php echo esc_html($toc_title); ?>
			</div>
			
			<div class='idocs-toc-items'>
				
				<?php

				$pattern = '#<(h['. $toc_header_start . '-' . $toc_header_end .  '])(.*?)>(.*?)</\1>#si';

				$index = 1;
				// Insert the IDs and create the TOC.
				// Grab each heading and add it to the $tableOfContents variable.
				// Give each heading in the article a unique ID, in order to be able later to link the TOC links to their respective headings.
				$content = preg_replace_callback($pattern, function ($matches) use (&$index, &$tableOfContents) {
				/*
					> HTML tags that start with the letter h and end in 1, 2, 3, 4, 5, or 6
					> grouping the tag name ((h[1-6])), its attributes ((.*?)>), and the tag’s inner HTML (>(.*?)</\1>) into separate groups,
					to be able to use them individually in the callback function. 
					> s flag, to make the . selector match newlines, and the i to ignore the case of the matched elements.
					> The callback function references $tableOfContents and $index so that we can have access to them from inside. 
					We’re also using the & operator to get their pointer reference so that we’ll be able to change their values.
					> The $matches parameter is filled in by preg_replace_callback() with the groupings matched based on the regex pattern.
				*/ 

					// use the first grouping as the $tag.
					$tag = $matches[1];
					// uses the 3rd grouping as the title of the heading, 
					// but it strips the tags first, because there are cases where the heading contains other HTML elements, like <a> or <strong>. 
					$title = strip_tags($matches[3]);
					// Checks if the currently matched element already has an id attribute set. 
					// If it does, it’s going to be stored in the $matchedIds variable.
					$hasId = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[2], $matchedIds);
					// Create the unique ID that’s going to be used as a href attribute on the TOC, and as an ID for the actual heading.
					// First, we check if it already had an ID. If it did, we’re going to use that one. 
					// However, if it did not have one, we’re going to create one based on the $index and the ‘slugified’ version of the title.
					// Generating one from the heading content, using a built-in WordPress function called sanitize_title().
					$id = $hasId ? $matchedIds[2] : $index++ . '-' . sanitize_title($title);
					// appending to the TOC the new element.
					/* <a class ="link-light" <?php echo 'href="#' . $id .'"'; ?> > <?php echo esc_html($title); ?> </a>*/

					/*
					<div class="idocs-toc-items toc-item-<?php echo esc_attr($tag);?>">
						<a href="#<?php echo esc_attr($id);?>"> 
							<?php echo esc_html($title); ?> 
						</a>
					</div>
					*/
					?>	
					<a href="#<?php echo esc_attr($id);?>" style="text-decoration: none;" title = "TOC Item Link"> 
						<div class="idocs-toc-item toc-item-<?php echo esc_attr($tag);?>" data-section-id="<?php echo esc_attr($id);?>">
							<?php echo esc_html($title); ?> 
						</div>
					</a>
					<?php

					// We’re checking if it had an ID, and if it did, we’re returning the matched string, untouched.
					// If it did not have an ID, we’re going to return a modified version of the matched heading HTML, which includes the ID. 
					// This is essential in order to be able to link from the TOC to the heading itself through basic browser functionalities.

					if ($hasId) {
						return $matches[0];
					}
					return sprintf('<%s%s id="%s">%s</%s>', $tag, $matches[2], $id, $matches[3], $tag);
				}, $content);
				
				// appends a closing <div> tag to the TOC.
				?>
				
			</div>
		</nav>
		<?php
		/*--------------------------------------------*/
		$toc = ob_get_contents();
		ob_end_clean();
		// return the modified $content which now includes ID attributes for all the headings.
		return array ($content, $toc);

	}
	/*---------------------------------------------------------------------------------------*/
	public static function navigator_accordion_item($documents_order_by, $category_num, $top_category, $show_category_counter, $post_counts, $show_accordion, $the_query, $category_tree, $post_categories, $wp_term, $active_doc_id ) {

		/*--------------------------------------------*/
		//$category_icon_url =  get_term_meta( $wp_term->term_id, 'idocs-category-taxo-icon-url', true );
		$category_icon_url = IDOCS_Taxanomies::get_term_meta_caching(  $wp_term->term_id, 'idocs-category-taxo-icon-url', false);
		/*--------------------------------------------*/
		?>
		<div class="accordion-item">
			<!-- HEADER -->
			<div class="accordion-header idocs-accordion-header-main">
				<button class="accordion-button btn-link"  type="button" 
						data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo esc_attr($category_num);?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr($category_num);?>">
					<div class="idocs-navigation-box-category-container-main">	
							<?php
								if ( empty ($category_icon_url) ) {
									// use the icon picker 
									//$category_icon_key =  get_term_meta( $wp_term->term_id, 'idocs-category-taxo-icon-picker', true );
									$category_icon_key = IDOCS_Taxanomies::get_term_meta_caching(  $wp_term->term_id, 'idocs-category-taxo-icon-picker', false);

									if ( $category_icon_key != null ) {
										?>
											<?php IDOCS_ICONS::echo_icon_svg_tag($category_icon_key, 0, 0, 1);?>
										<?php
									}
								} 
								else { // use the custom icon

									?>
									<span class="idocs-navigation-box-category-icon">
										<img class="idocs-category-title-icon" src="<?php echo esc_url($category_icon_url); ?>" alt="category icon" >
									</span>	
									<?php
								}
								?>
								<span class="idocs-navigation-box-category-title">
									<?php echo esc_html(ucfirst($top_category->name));?>
								</span>
								<?php
								 
								//echo esc_html(ucfirst($top_category->name));
								if ($show_category_counter) {
									?>
									<span class="idocs-navigation-box-category-counter">
											<?php echo esc_attr($post_counts); ?> 
									<span>
									<?php
								}
								?>
						
					</div>
				</button>
			</div>
			<!-- Collapse Div -->
			<div id="collapse-<?php echo esc_attr($category_num);?>" class="accordion-collapse collapse <?php echo esc_attr($show_accordion);?>"  data-bs-parent="#idocs-navigator-accordion">
				<!-- BODY -->
				<div class="accordion-body idocs-navigation-box-accordion-body">
					<div class="list-group">
						<?php
							
						// Display all documents that are part of the selected query 
						while ($the_query->have_posts()) {

							$the_query->the_post();
							$newtab = false;
							$post_id = get_the_ID();
							$newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
							$content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
							$content_type_term = get_term_by('id', $content_type_id , 'idocs-content-type-taxo' );
							/*$content_type_term->name*/

							//$newtab = get_post_meta($post_id, 'idocs-content-newtab-meta', true);
							if ( empty ($newtab) ) {
								$newtab = 0;
							}
							//do_action( 'qm/debug', $newtab );
							?>
							<a class="list-group-item list-group-item-action list-group-item-success idocs-navigation-box-document-item" href="<?php echo esc_url(get_the_permalink());?>" <?php if ($newtab) echo esc_attr(" target=_blank");?>>
							
									<?php
									$icon_size = 16;
									$color = "";
									IDOCS_ICONS::echo_icon_svg_tag('rectangle-list', 0, 0, 1 );
									?>
									<?php 
										
										if ( $active_doc_id == $post_id ) {
											//do_action( 'qm/debug', $current_doc_id );
											?>
											<span style="padding-left:5px;" class="idocs-navigation-box-document-item-active">
												<?php echo esc_html(ucfirst(get_the_title())); ?>
											<span>
											<?php
										} else {
											?>
											<span style="padding-left:5px;" >
												<?php echo esc_html(ucfirst(get_the_title())); ?>
											<span>
											<?php	
										}
									?>
							</a>															
							<?php
						}
						wp_reset_postdata();
						?>
						<!------------------------------->
						<!-- Sub-Categories -->
						<?php
						
						if (! empty($category_tree) )  {
							//do_action( 'qm/debug', $category_tree );
							//do_action( 'qm/debug', $category_num );
							//do_action( 'qm/debug', $post_categories[0]->name );
							// Display category tree in Recursion
							IDOCS_CategoryTree::display_category_tree($category_tree, $category_num, $post_categories[0]->name, $show_category_counter, $active_doc_id, $documents_order_by);
						
						}
						?>
					</div>
				</div> <!-- accordion-body -->
			</div> <!-- Collapse Div -->
		</div> <!-- accordion-item -->
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// [idocs_sidebar_navigator]
	public function idocs_sidebar_navigator ( $atts, $content = null ) {
		
		/*--------------------------------------------*/
		$current_kb_id = sanitize_text_field($atts ['kb_id']);
		$current_doc_id = sanitize_text_field($atts ['document_id']);
		$show_category_counter = sanitize_text_field($atts ['show_category_counter']);
		$hide_empty_categories = sanitize_text_field($atts ['hide_empty_categories']);
		ob_start();
		/*--------------------------------------------*/
		require_once IDOCS_DIR_PATH . 'public/templates/idocs_sidebar_navigator.php';
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public function idocs_document_view ( $atts, $content = null ) {

		/*--------------------------------------------*/
		$current_kb_id = sanitize_text_field($atts ['kb_id']);
		$current_document_id = sanitize_text_field($atts ['content_id']);
		$current_category_id = IDOCS_CPT::get_post_meta_caching($current_document_id, 'idocs-content-category-meta');
		ob_start();
		/*--------------------------------------------*/
		if ( self::kb_and_category_check($current_kb_id, 0) && self::document_check($current_document_id) ) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_document_view.php';

		}
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public function idocs_document_tags ( $atts, $content = null) {
		
		/*--------------------------------------------*/
		$current_document_id = intval(sanitize_text_field($atts ['content_id']));
		$show_tag_background_color = sanitize_text_field($atts['show_tag_background']);
		ob_start();
		/*--------------------------------------------*/
		if ( self::document_check($current_document_id) ) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_document_tags.php';

		};
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
	public function idocs_related_documents ( $atts, $content = null ) {
		
		/*--------------------------------------------*/
		$doc_id = intval(sanitize_text_field($atts['content_id']));
		ob_start();
		/*--------------------------------------------*/
		if ( self::document_check($doc_id) ) {

			require_once IDOCS_DIR_PATH . 'public/templates/idocs_related_documents.php';

		};
		/*--------------------------------------------*/
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	/*---------------------------------------------------------------------------------------*/
}
/*---------------------------------------------------------------------------------------*/
// https://www.codepicky.com/wordpress-table-of-contents/
// https://wordpress.stackexchange.com/questions/27909/get-custom-post-type-by-tag
// https://wordpress.stackexchange.com/questions/27909/get-custom-post-type-by-tag

