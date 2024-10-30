<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------*/
class IDOCS_Plugin_Update {

    public function plugin_upgrade_process($upgrader_object, $options) {

        // Check if this is a plugin update
        if ($options['action'] == 'update' && $options['type'] == 'plugin') {

            // Check if the updated plugin is your plugin
            $plugin_slug = 'incredibledocs/incredibledocs.php';
            if (in_array($plugin_slug, $options['plugins'])) {

                IDOCS_Database::clear_all_kbs_design_transients();

                delete_transient( 'idocs_transient_terms_metadata');
                delete_transient( 'idocs_transient_direct_content_flags');
                delete_transient( 'idocs_transient_posts_metadata');
                delete_transient( 'idocs_transient_total_content_types' );
                delete_transient( 'idocs_transient_navigation_links' );
                delete_transient( 'idocs_transient_faqs_per_group');	
                delete_transient( 'idocs_transient_attached_terms_' . 'idocs-category-taxo');
                delete_transient( 'idocs_transient_attached_terms_' . 'idocs-kb-taxo');
                delete_transient( 'idocs_transient_attached_terms_' . 'idocs-tag-taxo');
                delete_transient( 'idocs_transient_attached_terms_' . 'idocs-faq-group-taxo');
                delete_transient( 'idocs_transient_content_types_terms');
                delete_transient( 'idocs_transient_category_terms');
                delete_transient( 'idocs_transient_category_terms_slugs');
			    delete_transient( 'idocs_transient_tag_terms_slugs' );
			    delete_transient( 'idocs_transient_tag_terms' );
                delete_transient( 'idocs_transient_faqgroup_terms_slugs' );
                delete_transient( 'idocs_transient_ip_to_country' );
                delete_transient( 'idocs_transient_shortcodes_per_page');

            }
        }
    }
   /*---------------------------------------------------------------------------------------*/
}
/*---------------------------------------------------------------------------------------*/
