<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
?>
<form action="options.php" method="post">
	<?php	
	settings_fields( IDOCS_PLUGIN_NAME . '_saved_options_group' );					
	// Prints (generate the HTML) out all settings sections added to a particular settings page.
	// do_settings_sections( string $page )
	?>
	<!---------------------------------------------->
	<div class="card idocs-uninstall-card">
		<div class="card-body">
			<h5><?php echo esc_html__( 'Uninstall Settings', 'incredibledocs' );?></h5>
			<hr>
			<?php
			do_settings_sections( 'general-tab-uninstall-section' );
			?>
		</div>
	</div>
	<!---------------------------------------------->
	<?php
	// submit button
	submit_button(text: esc_html__('Save Changes', 'incredibledocs'),
		type: "idocs-submit-settings-button", wrap: false);
	?>
</form>
<?php
/*---------------------------------------------------------------------------------------*/

							