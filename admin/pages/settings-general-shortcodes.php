<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/* SETTINGS ADMIN PAGE */
/*---------------------------------------------------------------------------------------*/				
?>
<div id="container-fluid">
	<div class="row">
		<!-- Column 1 -->
        <div class="col-md-8">
			<div class="card idocs-shortcodes-card">
				<div class="card-body" >
					<h5><?php echo esc_html__( 'Frontend Shortcodes (Core)', 'incredibledocs' );?></h5>
					<hr>
					<!---------------------------------------------->
					<table class="table">
						<thead>
							<tr class="table-primary">
								<th scope="col"><?php echo esc_html__( 'Type', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Shortcode', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Code', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Parameters', 'incredibledocs' );?></th>
							</tr>
						</thead>
						<!---------------------------------------------->
						<tbody id="shortcodes-table-body">
						<?php 
							$shortcodes = IDOCS_Shortcodes:: get_core_shortcodes_list ();
							 /*--------------------------------------------*/
							foreach ( $shortcodes as $shortcode ) {
								?>
								<tr scope="row">
									<td> <?php echo esc_html($shortcode[0]); ?> </td>
									<td> <?php echo esc_html($shortcode[1]); ?> </td>
									<td> <?php echo esc_html($shortcode[2]); ?> </td>
									<td> <?php echo esc_html($shortcode[3]); ?> </td>
								</tr>
								<?php
							};
							/*--------------------------------------------*/	
							?>
						</tbody>
					</table>
					<!---------------------------------------------->
				</div>					
			</div>
			<!--
			<div class="card idocs-shortcodes-card">
				<div class="card-body" >
					<h5><?//php echo esc_html__( 'Frontend Shortcodes (Pro)', 'incredibledocs' );?></h5>
					<hr>
					
					<table class="table">
						<thead>
							<tr class="table-primary">
								<th scope="col"><?php //echo esc_html__( 'Type', 'incredibledocs' );?></th>
								<th scope="col"><?php //echo esc_html__( 'Shortcode', 'incredibledocs' );?></th>
								<th scope="col"><?php //echo esc_html__( 'Code', 'incredibledocs' );?></th>
								<th scope="col"><?php //echo esc_html__( 'Parameters', 'incredibledocs' );?></th>
							</tr>
						</thead>
						
						<tbody id="shortcodes-table-body">
						<?php 

							/*
							$shortcodes = IDOCS_Shortcodes:: get_pro_shortcodes_list ();

							foreach ($shortcodes as $shortcode ) {
								?>
								<tr scope="row">
									<td> <?php echo esc_html($shortcode[0]); ?> </td>
									<td> <?php echo esc_html($shortcode[1]); ?> </td>
									<td> <?php echo esc_html($shortcode[2]); ?> </td>
									<td> <?php echo esc_html($shortcode[3]); ?> </td>
								</tr>
								<?php
							}
							*/

							?>
						</tbody>
					</table>
					
				</div>					
			</div>
			-->
		</div>
		<!---------------------------------------------->
		<div class="col-md-4">
			<div class="card idocs-shortcodes-card">
				<div class="card-body" >
					<h5><?php echo esc_html__( 'Parameters', 'incredibledocs' );?></h5>
					<hr>
					<!---------------------------------------------->
					<table class="table">
						<thead>
							<tr class="table-primary">
								<th scope="col"><?php echo esc_html__( 'Parameters', 'incredibledocs' );?></th>
								<th scope="col"><?php echo esc_html__( 'Description', 'incredibledocs' );?></th>
							</tr>
						</thead>
						<!---------------------------------------------->
						<tbody id="shortcodes-table-body">
							<tr scope="row">
								<td> <?php echo esc_html('kb_id'); ?> </td>
								<td> <?php echo esc_html__('A unique number of the knowledge base term.', 'incredibledocs' ); ?> </td>
							</tr>
							<tr scope="row">
								<td> <?php echo esc_html('category_id'); ?> </td>
								<td> <?php echo esc_html__('A unique number of the category term.', 'incredibledocs' ); ?> </td>
							</tr>
							<tr scope="row">
								<td> <?php echo esc_html('document_id'); ?> </td>
								<td> <?php echo esc_html__('A unique number of the document post.', 'incredibledocs' ); ?> </td>
							</tr>
							<tr scope="row">
								<td> <?php echo esc_html('last_days'); ?> </td>
								<td> <?php echo esc_html__('A time frame in days.', 'incredibledocs' ); ?> </td>
							</tr>
							<tr scope="row">
								<td> <?php echo esc_html('top_n'); ?> </td>
								<td> <?php echo esc_html__('Limit the number of items.', 'incredibledocs' ); ?> </td>
							</tr>
						</tbody>
					</table>
					<!---------------------------------------------->
				</div>
			</div>
		</div>
	</div>
	<!---------------------------------------------->		
</div>	
<?php
/*---------------------------------------------------------------------------------------*/

