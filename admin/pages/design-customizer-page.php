<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
/*---------------------------------------------------------------------------------------*/
if ( empty($kb_terms) ) {
    ?>
    <div class="row">
        <div class="col-4" style="padding:10px">
            <?php echo esc_html__( 'No knowledge base instance is configured.', 'incredibledocs' );?>
        </div>
    </div>
    <?php
}
/*--------------------------------------------*/
else {
    ?>
    <div class="row">
        <div class="col-4" style="padding:10px">
            <label style="font-size: 1.1rem;" for="design-global-kb"><?php echo esc_html__( 'Select a Knowledge Base', 'incredibledocs' );?>:&nbsp;</label>
            <select id="design-global-kb" style="font-weight: bold;font-size: 1.1rem;">
                
                <?php
                foreach ( $kb_terms as $term) {
                
                    ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>" ><?php echo esc_html( $term->name ); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <hr>	
    </div>
    <!---------------------------------------------->
    <div class="container-fluid" style="margin-top:20px;">
        <!---------------------------------------------->        
        <div class="row">
            <div class="col-10">
                <div class="idocs-customizer-warning-box">
                    <?php echo esc_html__( 'Select the relevant knowledge base before clicking on the required module!', 'incredibledocs' );?>
                </div>
            </div>
        </div>
        <!---------------------------------------------->
        <div class="row">
            <!---------------------------------------------->
            <div class="col-md-5">
                <!-- First Column Content -->
                <div id="kb-view-section" class="idocs-customizer-main-box" data-siteurl="<?php echo esc_url(get_site_url());?>">
                    <h5><?php echo esc_html__( 'Knowledge Base View', 'incredibledocs' );?></h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="live-search-section-1" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Live-Search Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="breadcrumb-section" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Breadcrumb Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="categories-cards-section" class="idocs-customizer-sub-box" style="height: 5rem;">
                            <?php echo esc_html__( 'Categories Cards', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="faqs-section-1" class="idocs-customizer-sub-box" style="height: 5rem;">
                            <?php echo esc_html__( 'FAQs', 'incredibledocs' );?>
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------->
            <div class="col-md-5">
                <!-- First Column Content -->
                <div id="document-view-section" class="idocs-customizer-main-box" data-siteurl="<?php echo esc_url(get_site_url());?>">
                    <h5><?php echo esc_html__( 'Document View', 'incredibledocs' );?></h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="live-search-section-2" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Live-Search Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="breadcrumb-section" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Breadcrumb Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="sidebar-nav-section" class="idocs-customizer-sub-box-2">
                            <?php echo esc_html__( 'Sidebar Nav.', 'incredibledocs' );?>
                        </div>
                        <div id="document-content-section" class="idocs-customizer-sub-box-2">
                            <?php echo esc_html__( 'Document Content', 'incredibledocs' );?>
                        </div>
                        <div id="toc-section" class="idocs-customizer-sub-box-2">
                            <?php echo esc_html__( 'TOC', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="likes-rating-section" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Likes Rating (Pro)', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="feedback-form-section" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Feedback Form (Pro)', 'incredibledocs' );?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="document-tags-section" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Document Tags', 'incredibledocs' );?>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        <br>
        <!---------------------------------------------->
        <div class="row">
            <div class="col-md-5">
                <!-- First Column Content -->
                <div id="tag-view-section" class="idocs-customizer-main-box" style="height: 15rem;" data-siteurl="<?php echo esc_url(get_site_url());?>">
                    <h5><?php echo esc_html__( 'Tag View', 'incredibledocs' );?></h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="live-search-section-3" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Live-Search Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="content-cards-section" class="idocs-customizer-sub-box" style="height: 5rem;">
                            <?php echo esc_html__( 'Content Cards', 'incredibledocs' );?>
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------->
            <div class="col-md-5">
                <!-- First Column Content -->
                <div id="faq-group-view-section" class="idocs-customizer-main-box" style="height: 15rem;" data-siteurl="<?php echo esc_url(get_site_url());?>">
                    <h5><?php echo esc_html__( 'FAQ Group View', 'incredibledocs' );?></h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="live-search-section-4" class="idocs-customizer-sub-box">
                            <?php echo esc_html__( 'Live-Search Bar', 'incredibledocs' );?>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="faqs-section-2" class="idocs-customizer-sub-box" style="height: 5rem;">
                            <?php echo esc_html__( 'FAQs', 'incredibledocs' );?>
                        </div>
                    </div>
                </div>
            </div> 
            <!----------------------------------------------> 
        </div>
    </div>
    <?php
}