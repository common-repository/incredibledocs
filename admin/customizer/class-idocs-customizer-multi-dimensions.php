<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Multi_Dimensions extends WP_Customize_Control {
        
    public function enqueue() {

        wp_enqueue_script( 'idocs-custom-controls-2-js', IDOCS_ADMIN_URL . 'js/idocs-custom-controls-2.js', array( 'jquery' ), IDOCS_VERSION, true );
       // wp_enqueue_style( 'idocs-custom-controls-2-css',  IDOCS_ADMIN_URL . 'css/idocs-custom-controls-2.css');

    }    
    public function render_content() {

        if ( ! empty( $this->label ) ) : ?>
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif;
        if ( ! empty( $this->description ) ) : ?>
        <p class="description customize-control-description"><?php echo esc_html( $this->description ); ?></p>
        <?php endif;
        if (! is_array( $this->value() ) && ! empty( $this->value() ) )
            $saved_values = explode( ', ', $this->value() );
    
        //error_log($saved_values[0]);
        //var_dump($saved_values);
        
        ?> 
        <div class="box-model-wrapper">
        <?php
        foreach ( $this->choices as $key => $value ) {
            if ( 'margin' === $key ) { ?>
            <div class="box-model-margin" style="padding:0px;">
                <div style="padding:5px 0 5px 0"><?php echo esc_html__( 'Margin (top, right, bottom, left)', 'incredibledocs' ); ?></div>
                <?php
                $margin_count = 0;
                foreach ( $value as $m_key => $m_value ) {
                    ?>
                    <input style="max-width: 24%" type="number" placeholder="-" value="<?php echo esc_attr( $saved_values[ $margin_count ] );?>" class="box-model-field '<?php echo esc_attr( $m_key );?>">
                    <?php
                    $margin_count++;
                } ?>
            </div><?php
            }
            
            if ( 'padding' === $key ) { ?>
            <div class="box-model-padding" style="padding:0px;">
                <div style="padding:15px 0 5px 0">
                    <?php echo esc_html__( 'Padding (top, right, bottom, left)', 'incredibledocs' ); ?>
                </div>
                <?php
                $padding_count = 4; // margin takes array keys 0-3, padding 4-7.
                foreach ( $value as $p_key => $p_value ) {
                    ?>
                    <input style="max-width: 24%" type="number" placeholder="-" value="<?php echo esc_attr( $saved_values[ $padding_count ] );?>" class="box-model-field '<?php echo esc_attr( $p_key );?>">
                    <?php
                    $padding_count++;
                } ?>
            </div><?php
            }
            
        } ?>
            <input type="hidden" class="box-model-saved" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
        </div>
        <?php
        
    }
}
