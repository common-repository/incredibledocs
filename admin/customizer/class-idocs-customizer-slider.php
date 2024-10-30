<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Slider extends WP_Customize_Control {
    
    /*--------------------------------------------*/
    public function enqueue() {

        wp_enqueue_script( 'idocs-custom-controls-1-js', IDOCS_ADMIN_URL . 'js/idocs-custom-controls-1.js', array( 'jquery', 'jquery-ui-core', 'wp-color-picker' ), IDOCS_VERSION, true );
        wp_enqueue_style( 'idocs-custom-controls-1-css',  IDOCS_ADMIN_URL . 'css/idocs-custom-controls-1.css', array(), IDOCS_VERSION, 'all' );
        

    }
    /*--------------------------------------------*/
    public function render_content() {
    ?>
        <div class="slider-custom-control">
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><input type="number" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" class="customize-control-slider-value" <?php $this->link(); ?> />
            <div class="slider" slider-min-value="<?php echo esc_attr( $this->input_attrs['min'] ); ?>" slider-max-value="<?php echo esc_attr( $this->input_attrs['max'] ); ?>" slider-step-value="<?php echo esc_attr( $this->input_attrs['step'] ); ?>"></div><span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="<?php echo esc_attr( $this->value() ); ?>"></span>
        </div>
    <?php
    }
}
/*---------------------------------------------------------------------------------------*/