<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Toggle_Switch extends WP_Customize_Control {
    
    /*--------------------------------------------*/
    public function enqueue(){
        
        wp_enqueue_style( 'toggle-custom-control',  IDOCS_ADMIN_URL . 'css/idocs-custom-controls-1.css', array(), IDOCS_VERSION, 'all' );
    //    wp_enqueue_style( 'skyrocket-custom-controls-css', IDOCS_ADMIN_URL . 'css/customizer.css', array(), '1.2', 'all' );


    }
    /*--------------------------------------------*/
    public function render_content(){
    ?>
        <div class="toggle-switch-control">
            <div class="toggle-switch">
                <input type="checkbox" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" class="toggle-switch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
                <label class="toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
                    <span class="toggle-switch-inner"></span>
                    <span class="toggle-switch-switch"></span>
                </label>
            </div>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if( ! empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>
        </div>
    <?php
    }
    
}
/*---------------------------------------------------------------------------------------*/
