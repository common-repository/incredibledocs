<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/ 
// Display a block of text such as instructional information.
class IDOCS_Customizer_WarningNotice extends WP_Customize_Control {

    /*--------------------------------------------*/
    // overriding the render_content() to render the HTML for the new control 
    public function render_content() {

        // $this->label and $this->description are arguments that are specified when using add_control()
    ?>
        <div class="simple-notice-custom-control">
            <?php if( ! empty( $this->label ) ) { ?>
                <span style="font-size: 1.1em;" class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php } ?>
        </div>
    <?php
    }
}
/*---------------------------------------------------------------------------------------*/
