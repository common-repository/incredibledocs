<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/	
/* URLs SETTINGS PAGE */
/*---------------------------------------------------------------------------------------*/	
//$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
$idocs_kbs_root_slug = IDOCS_Database::get_plugin_settings('idocs_kbs_root_slug');
/*---------------------------------------------------------------------------------------*/					
?>
<div id="urls-settings">
	<!--------------------------------------->
	<form action="options.php" method="post">
		<div class="card idocs-urls-card">
			<div class="card-body" >
				<h5><?php echo esc_html__( 'Automated URLs Structure', 'incredibledocs' );?></h5>
				<hr>
				<!---------------------------------------------->
				<table class="table">
					<thead>
						<tr class="table-primary">
							<th scope="col"><?php echo esc_html__( 'Item', 'incredibledocs' );?></th>
							<th scope="col"><?php echo esc_html__( 'View', 'incredibledocs' );?></th>
							<th scope="col"><?php echo esc_html__( 'URL Structure', 'incredibledocs' );?></th>
						</tr>
					</thead>
					<!---------------------------------------------->
					<tbody id="urls-table-body">
						<tr scope="row">
							<td style="font-weight: bold;"><?php echo esc_html__( 'Knowledge Base', 'incredibledocs' );?></td>
							<td><?php echo esc_html__( 'KB View', 'incredibledocs' );?></td>
							<td>website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-categories</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/</td>
						</tr>
						<tr scope="row">
							<td style="font-weight: bold;"><?php echo esc_html__( 'Category', 'incredibledocs' );?></td>
							<td><?php echo esc_html__( 'KB View', 'incredibledocs' );?></td>
							<td>website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-categories</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/<span style="color:BlueViolet; font-weight: bold;">category-slug</span>/</td>
						</tr>
						<tr scope="row">
							<td style="font-weight: bold;"><?php echo esc_html__( 'FAQ Group', 'incredibledocs' );?></td>
							<td><?php echo esc_html__( 'FAQ Group View', 'incredibledocs' );?></td>
							<td>
								website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-faqgroups</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/<span style="color:blue; font-weight: bold;">root</span>/<span style="color:DarkKhaki; font-weight: bold;">faqgroup-slug</span>
								<br>
								website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-faqgroups</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/<span style="color:BlueViolet; font-weight: bold;">category-slug</span>/<span style="color:DarkKhaki; font-weight: bold;">faqgroup-slug</span>
							</td>
						</tr>
						<tr scope="row">
							<td style="font-weight: bold;"><?php echo esc_html__( 'Tag', 'incredibledocs' );?></td>
							<td><?php echo esc_html__( 'Tag View', 'incredibledocs' );?></td>
							<td>website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-tags</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/<span style="color:brown; font-weight: bold;">tag-slug</span>/</td>
						</tr>
						<tr scope="row">
							<td style="font-weight: bold;"><?php echo esc_html__( 'Document', 'incredibledocs' );?></td>
							<td><?php echo esc_html__( 'Document View', 'incredibledocs' );?></td>
							<td>website/<span style="font-weight: bold;">custom-root-slug</span><span style="color:blue; font-weight: bold;">-content</span>/<span style="color:green; font-weight: bold;">kb-slug</span>/<span style="color:BlueViolet; font-weight: bold;">category-slug</span>/</span><span style="color:red; font-weight: bold;">document-slug</span></td>
						</tr>					
					</tbody>
				</table>
				<!---------------------------------------------->
				<p style="font-weight: bold;">Note - In case of using a custom knowledge-base page with a shortcode, 
				the <span style="color:black; font-weight: bold;">"kbs-root-slug</span>/<span style="color:green; font-weight: bold;">kb-slug"</span> will be automatically replaced with the page slug.<p>
			</div>
			<!---------------------------------------------->
			<?php
			settings_fields( IDOCS_PLUGIN_NAME . '_saved_options_group' );					
			// Prints (generate the HTML) out all settings sections added to a particular settings page.
			// do_settings_sections( string $page )
			do_settings_sections( 'general-tab-urls-section' );
			?>	
			<!---------------------------------------------->
		</div>
		<?php	
			// submit button
			submit_button(text: __('Save Changes', 'incredibledocs'),
				type: "idocs-submit-settings-button", wrap: false);
		?>
	</form>	
	<!--------------------------------------->
</div>	
<?php
/*---------------------------------------------------------------------------------------*/

