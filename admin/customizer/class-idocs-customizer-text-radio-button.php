<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Text_Radio_Button extends WP_Customize_Control {
   
    /*--------------------------------------------*/
    public function enqueue() {
    }
    /*--------------------------------------------*/
    public function render_content() {
    ?>
        <div class="text_radio_button_control">
            <?php if( !empty( $this->label ) ) { ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php } ?>
            <?php if( !empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>

            <div class="radio-buttons">
                <?php foreach ( $this->choices as $key => $value ) { ?>
                    <label class="radio-button-label">
                        <input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php $this->link(); ?> <?php checked( esc_attr( $key ), $this->value() ); ?>/>
                        <span><?php echo esc_html( $value ); ?></span>
                    </label>
                <?php	} ?>
            </div>
        </div>
    <?php
    }
}
/*---------------------------------------------------------------------------------------*/