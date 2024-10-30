<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* SETTINGS ADMIN PAGE */
/*---------------------------------------------------------------------------------------*/
$settings_tabs = IDOCS_Admin_Settings::get_settings_tabs();
//do_action( 'qm/debug', $settings_tabs  );
//Get the active tab from the $_GET param
$default_tab = 'design';
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;
//Get the active section from the $_GET param
$default_section = null;
$section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : $default_section;
/*---------------------------------------------------------------------------------------*/
?>
<!-- Overall Setting Page Container -->
<div class="container-fluid">
	<!------------------------------------------------------------------------------------------->
	<!-- setting menu with tabs -->
	<nav class="nav-tab-wrapper" id="settings-menu" data-selected-tab="<?php echo esc_attr($tab);?>" data-selected-section="<?php echo esc_attr($section);?>">
		<?php
		foreach( $settings_tabs as $tab_key => $tab_info ) {
			
			if ($tab_info['tab_active']) {
				?>
					<a href="?page=idocs-settings&tab=<?php echo esc_attr($tab_key);?>" 
					   class="nav-tab <?php if($tab === $tab_key):?>nav-tab-active<?php endif; ?>" title = "Menu Tab Link">
					   <?php echo esc_html($tab_info['tab_title'])?>
					</a>
				<?php
			}
		}
		?>
	</nav>
	<!------------------------------------------------------------------------------------------->
	<div class="tab-content">
		<?php 
		if ($section == null) 
			$section = $settings_tabs[$tab]['default_section'];	
		?>
		<!-- layout tab with sections -->
		<nav class="nav-tab-wrapper">
			<?php
				foreach( $settings_tabs[$tab]['sections'] as $section_key => $section_info ) {
					
					if ($section_info['section_active']) {
						?>
							<a href="?page=idocs-settings&tab=<?php echo esc_attr($tab);?>&section=<?php echo esc_attr($section_key);?>" 
							class="nav-tab <?php if($section === $section_key):?>nav-tab-active idocs-section-active<?php endif; ?>" title = "Menu Tab Link">
							<?php echo esc_html($section_info['section_title']);?>
							</a>
						<?php
					}

				}	
			?>
		</nav>
		<?php
		/*-------------------------------------------------*/	
		?>
		<!-- section content -->
		<div class="section-content">
			<?php
				// get the relevant page path for the page (core or pro version)
				$page_path = $settings_tabs[$tab]['sections'][$section]['page_path'];
				//error_log($page_path);
				require_once $page_path . 'pages/'. $settings_tabs[$tab]['sections'][$section]['section_page'];
			?>		
		</div>
		<!---------------------------------------------->	
	</div> <!-- tab-content -->
</div> <!-- idocs-setting-page -->
<?php

