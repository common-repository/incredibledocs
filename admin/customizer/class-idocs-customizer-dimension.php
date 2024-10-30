<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Dimension extends WP_Customize_Control {

    /*--------------------------------------------*/
	public function render_content() {
		?>
		<div class="dimension-field">
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <input type="number" data-default-val="<?php echo esc_attr($this->settings[ 'default' ]->value()); ?>" value="<?php echo esc_attr($this->value()); ?>" <?php $this->input_attrs(); $this->link(); ?>>
		</div>
		<?php
	}
}
/*---------------------------------------------------------------------------------------*/
