<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
// Utility functions for building an displaying the category tree structure as part of the sidebar navigator 
/*---------------------------------------------------------------------------------------*/
class IDOCS_CategoryTree {

    /*---------------------------------------------------------------------------------------*/
    // recursion function to create the category tree 
    public static function build_category_tree ($category_id, $kb_id, $post_category_name, $hide_empty_categories) {

        $branch = array();
        // get all direct childrens of that category 
        $elements = get_terms( array(

            'taxonomy'   => 'idocs-category-taxo',
            'hide_empty' => $hide_empty_categories,
            'parent' => $category_id, 
            'meta_key'   => 'idocs-category-taxo-kb',
            'meta_value' => $kb_id,
            'include_children' => false,
            'orderby' => 'name',
            'order' => 'ASC', 
        ) );
        /*--------------------------------------------*/
        $show = '';
        $my_parent = '';
        /*--------------------------------------------*/    
        foreach( $elements as $element ) {

            // recursion call with the element branch. 
            // return children tree branch and if the parent element should be shown as one of the child should be shown. 
            list($children, $parent_show) = self::build_category_tree ($element->term_id, $kb_id, $post_category_name, $hide_empty_categories );
            /*--------------------------------------------*/            
            // check if the current category (for-loop) is equal to the current post category (single doc template)
            if 	($element->name == $post_category_name) {    

                $show = 'show'; // I need to to be shown 
                $my_parent = 'show'; // my parent should be shown as well. 
            }
            else	
                $show = '';
            /*--------------------------------------------*/
            //do_action( 'qm/debug', $show );
            $optimized_element = array (

                'term_id'=> $element->term_id, 
                'name'=>    $element->name,

            );    
            /*--------------------------------------------*/
            if (! empty($children) ) {
            
                // store the children array 
                $optimized_element['children'] = $children;
                // check if the current branch as a parent should be displayed 
                if ($parent_show == 'show') {
                    $show='show';
                    $my_parent = 'show';
                }
            }
            /*--------------------------------------------*/
            $optimized_element['show'] = $show;
            // add new element to the array 
            //$branch[] = $element;
            $branch[] = $optimized_element;
            
        };
        /*--------------------------------------------*/
        return array ($branch, $my_parent);
    }
    /*---------------------------------------------------------------------------------------*/
    // recursion function to display the category tree inside accordion HTML element (5e5827093d)
    public static function display_category_tree ($category_tree, $category_num, $post_category_name, $show_category_counter, $active_doc_id, $documents_order_by) {

        
        //$show_category_counter = true;
        $sub_category_num = 1;
        /*--------------------------------------------*/
        //$content_type_term = get_term_by('name', 'FAQ' , 'idocs-content-type-taxo' );
        $document_content_type_term = get_term_by('name', 'Document' , 'idocs-content-type-taxo' );

        foreach($category_tree as $category) {
            // get the list of all the direct content items (only "Documents") related to specific category 
            if ( $documents_order_by == 'custom_display_order' ) {

                $args = array(
                    'post_type' => 'idocs_content',
                    'posts_per_page' => -1,
                    'orderby' => 'meta_value_num', // Order by a numeric value
                    'meta_key' => 'idocs-content-display-order-meta', // Specify the meta key
                    'order' => 'ASC',
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'idocs-category-taxo',
                            'field' => 'term_id',
                            'terms' => $category['term_id'],
                            'operator' => 'IN',
                            'include_children' => false,
                        ),
                        array(
                            'taxonomy'         => 'idocs-content-type-taxo', 
                            'field'            => 'term_id',
                            'terms'            =>  $document_content_type_term->term_id, 
                            'operator'         => 'IN',
                        ),
                    )
                );
            }
            /*--------------------------------------------*/
            else {

                // get the list of all the direct content items (only "Documents") related to specific category 
                $args = array(

                    'post_type' => 'idocs_content',
                    'posts_per_page' => -1,
                    'orderby' => $documents_order_by,
                    'order' => 'ASC',
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'idocs-category-taxo',
                            'field' => 'term_id',
                            'terms' => $category['term_id'],
                            'operator' => 'IN',
                            'include_children' => false,
                        ),
                        array(
                            'taxonomy'         => 'idocs-content-type-taxo', 
                            'field'            => 'term_id',
                            'terms'            =>  $document_content_type_term->term_id, 
                            'operator'         => 'IN',
                        ),
                    )
                );
            }
            
            $the_query = new WP_Query( $args ); 					                        
            //do_action( 'qm/debug', $the_query );
            $post_counts = $the_query->post_count;
            /*--------------------------------------------*/ 
            ?>
            <div class="accordion-item">
                <div class="accordion-header idocs-accordion-header-sub" >
                    <button class="accordion-button btn-link"  type="button"  
                            data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo esc_attr($category_num . $sub_category_num);?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr($category_num);?>">
                        <div class="idocs-navigation-box-category-container-sub">	
                                <?php
                                    if ( empty ($category_icon_url) ) {
                                        // use the icon picker 
                                        //$category_icon_key = get_term_meta( $category['term_id'], 'idocs-category-taxo-icon-picker', true );
                                        $category_icon_key = IDOCS_Taxanomies::get_term_meta_caching( $category['term_id'], 'idocs-category-taxo-icon-picker', false);
                                        if ( $category_icon_key != null ) {
                                            ?>
                                            <!--<span class="idocs-navigation-box-category-icon"> -->
                                                <?php IDOCS_ICONS::echo_icon_svg_tag($category_icon_key, 0, 0, 1);?>
                                            <!--</span> -->
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
                                    <span class="idocs-navigation-box-sub-category-title">
                                        <?php echo esc_html(ucfirst($category['name']));?>
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
                <!---------------------------------------------->   
                <!-- Collapse Div -->
                <div id="collapse-<?php echo esc_attr($category_num . $sub_category_num);?>" class="accordion-collapse collapse <?php echo esc_attr( $category['show']);?>">
                    <div class="accordion-body idocs-navigation-box-accordion-body">
                        <div class="list-group">
                            <?php
                            //$current_doc_title = get_the_title(); // used later on
                            //$current_doc_content = get_the_content(); // used later on 
                            //$current_doc_id = get_the_id();
                            /*--------------------------------------------*/
                            while ($the_query->have_posts()) {

                                $the_query->the_post();
                                ?>
                                <a class="list-group-item list-group-item-action list-group-item-success idocs-navigation-box-document-item" href="<?php echo esc_url(get_the_permalink());?>" > 
                                
                                    <?php
                                    $icon_size = 16;
                                    $color = "";
                                    IDOCS_ICONS::echo_icon_svg_tag('rectangle-list', $icon_size, $icon_size, 1, $color);
                                    /*--------------------------------------------*/
                                    if ( $active_doc_id == get_the_ID() ) {
										//do_action( 'qm/debug', $current_doc_id );
										?>
										<span style="padding-left:5px;" class="idocs-navigation-box-document-item-active">
											<?php echo esc_html(ucfirst(get_the_title())); ?>
										<span>
										<?php
									} 
                                    /*--------------------------------------------*/
                                    else {
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
                            /*--------------------------------------------*/
                            if (array_key_exists('children', $category)) {
                                
                                self::display_category_tree($category['children'], $category_num*10, $post_category_name, $show_category_counter, $active_doc_id, $documents_order_by );
                                      
                            }
                            /*--------------------------------------------*/
                            ?>
                        </div>
                    </div>
                </div>
            </div> <!-- accordion-item -->
            <!---------------------------------------------->                   
            <?php
            $category_num++;
        }
    }
}
/*---------------------------------------------------------------------------------------*/
