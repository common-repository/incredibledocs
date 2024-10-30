<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/	
/* Custom KB Pages - SETTINGS PAGE */
/*---------------------------------------------------------------------------------------*/	
$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
/*--------------------------------------------*/
if ( empty( $kb_terms ) ) {
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

	$idocs_kbs_root_slug = IDOCS_Database::get_plugin_settings('idocs_kbs_root_slug');
	/*---------------------------------------------------------------------------------------*/					
	?>
	<div id="urls-settings">
		<!--------------------------------------->
		<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<!-- the following hidden input is used to trigger an action hook: "admin_post_{$action}, $action is the value below " -->
			<input type="hidden" name="action" value="idocs_custom_urls_form" />
			<?php
			// generate hidden security nonce field inside the form 
			wp_nonce_field( 'idocs_admin_settings_custom_urls_form_nonce' );    
			?>
			<!-- share the amount of knowledge-bases with the form processing -->
			<input type="hidden" name="amount_of_kbs" value="<?php echo esc_attr(count($kb_terms)); ?>"/>
			<!--------------------------------------->
			<div class="card idocs-custom-kb-page-card">
				<div class="card-body" >
					<h5><?php echo esc_html__( 'Custom KB Pages', 'incredibledocs' );?></h5>
					<hr>
					<!---------------------------------------------->
					<table class="table">
						<thead>
							<tr class="table-primary">
								<th scope="col"><?php echo esc_html__( 'KB Name', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Custom KB Page', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Page', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Shortcode', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'KB URL', 'incredibledocs' );?></th>
							</tr>
						</thead>
						<!---------------------------------------------->
						<tbody id="shortcodes-table-body">
						<?php
							$index = 0; 
							$all_pages = get_pages(); 
							$pages = []; // in case there are no pages (empty)
							/*--------------------------------------------*/
							foreach ($all_pages as $page) {
								$pages[] = ['page_id' => $page->ID, 'page_title' =>  $page->post_title];
							};
							//do_action( 'qm/debug', $pages );
							/*--------------------------------------------*/
							foreach ( $kb_terms as $term) {
								?>
								<tr scope="row">
									<!-- KB Name --> 
									<!---------------------------------------------->
									<td style="font-weight: bold;"> <?php echo esc_html($term->name); ?> </td>
									<!-- Custom KB Page --> 
									<!---------------------------------------------->
									<td> <?php
											$custom_kb_page_flag = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', false);
											//$custom_kb_page_flag = get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', true );
											if ( empty($custom_kb_page_flag) ) $custom_kb_page_flag = 0;
											//do_action( 'qm/debug', $custom_kb_page_flag );
											?>
											<select name="custom-kb-page-flag-select-<?php echo esc_attr($index);?>" id="custom-kb-page-flag-select-<?php echo esc_attr($index);?>">

												<option value="0" <?php selected( $custom_kb_page_flag, 0 ); ?>><?php echo esc_html__( 'Disabled', 'incredibledocs' ); ?></option>
												<option value="1" <?php selected( $custom_kb_page_flag, 1 ); ?>><?php echo esc_html__( 'Enabled', 'incredibledocs' ); ?></option>
													
											</select>
											<!-- share the knowledge term id for each select with the form processing -->
											<input type="hidden" name="term-id-select-<?php echo esc_attr($index);?>" value="<?php echo esc_attr($term->term_id); ?>">
									</td>
									<!-- Page --> 
									<!---------------------------------------------->
									<td> 
										<?php 
											if ($custom_kb_page_flag == 1) {

												$custom_kb_page_id = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', false);
												//$custom_kb_page_id = get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', true );
												//do_action( 'qm/debug', $custom_kb_page_id );
												?>
												<select name="custom-kb-page-id-select-<?php echo esc_attr($index);?>" id="custom-kb-page-id-select-<?php echo esc_attr($index);?>">
													<option value="0"><?php echo esc_html__("Select a Page", 'incredibledocs'); ?></option>

													<?php
													foreach ($pages as $page) {
														?>
														<option value="<?php echo esc_attr($page['page_id']); ?>" <?php selected( $page['page_id'], $custom_kb_page_id ); ?>><?php echo esc_html($page['page_title']); ?></option>
														<?php
													}
												?>
												</select>
												<?php
											}	
											else {
												echo esc_html("--------"); 
											}
										?> 
									</td>
									<!-- Shortcode -->
									<!----------------------------------------------> 
									<td> 
										<?php 
											if ($custom_kb_page_flag == 1) {

												// check that the configured page exist (maybe removed)
												$page_exist = false;
												/*--------------------------------------------*/
												foreach ($pages as $page) {

													if ( $page['page_id'] == $custom_kb_page_id ){
														$page_exist = true;
													}
												};
												if ( $page_exist ) {

													echo esc_html('[idocs_kb_view kb_id=' . $term->term_id   .']'); 

												}
												else {
													echo esc_html("--------"); 
												}
											}
										?> 
									</td>
									<!-- KB URL --> 
									<!---------------------------------------------->
									<td> 
										<?php 
											
											if ( empty ($custom_kb_page_id) ) $custom_kb_page_id = 0;
											//do_action( 'qm/debug', $custom_kb_page_id );
											// Custom KB Page = ON and page is defined --> use page slug 
											if ($custom_kb_page_flag == 1 && $custom_kb_page_id != 0 && $page_exist) {
												
												$post = get_post($custom_kb_page_id);

												echo esc_url(get_site_url() . '/' . $post->post_name ); 
												
											};
											/*--------------------------------------------*/
											// Custom KB Page = ON, but page not defined --> print error. 
											if ($custom_kb_page_flag == 1 && 
												( $custom_kb_page_id == 0 || !$page_exist)) {
												
												?>
													<span style="color:red">
														<?php echo esc_html__('Custom knowledgebase page is not defined.', 'incredibledocs' ); ?>
													</span>
												<?php
												
											};
											/*--------------------------------------------*/
											// Custom KB Page = OFF --> use default kb_slug.
											if ($custom_kb_page_flag == 0 ) {
												
												echo esc_url(get_site_url() . "/" . $idocs_kbs_root_slug .'-categories' . "/" . $term->slug );  
												
											};
										?> 
									</td>
								</tr>
								<?php
								$index++;
							}
						?>  
						</tbody>
					</table>
					<!---------------------------------------------->
					<p style="font-weight: bold;">
					<?php echo esc_html__( 'Note - In case of using a custom kb page, copy the relevant shortcode and paste it into a shortcode-block inside the selected page using a page editor. ', 'incredibledocs' );?>
				</div>
			</div>
			<!---------------------------------------------->
			<?php
			// submit button
			submit_button(text: __('Save Changes', 'incredibledocs'),
				type: "idocs-submit-settings-button", wrap: false);
			?>
			<!---------------------------------------------->
		</form>
	</div>	
	<?php
}
/*---------------------------------------------------------------------------------------*/

