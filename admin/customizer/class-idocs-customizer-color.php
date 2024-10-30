<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Color extends WP_Customize_Control {
   
    public $palette;
    public $show_opacity;
    /*--------------------------------------------*/
    public function enqueue() {

        wp_enqueue_script( 'idocs-custom-controls-1-js', IDOCS_ADMIN_URL . 'js/idocs-custom-controls-1.js', array( 'jquery', 'wp-color-picker' ), IDOCS_VERSION, true );
        wp_enqueue_style( 'idocs-custom-controls-1-css',  IDOCS_ADMIN_URL . 'css/idocs-custom-controls-1.css', array('wp-color-picker'), IDOCS_VERSION, 'all' );
        
    }
    /*--------------------------------------------*/
    public function render_content() {

        // Process the palette
        if ( is_array( $this->palette ) ) {
            $palette = implode( '|', $this->palette );
        } else {
            // Default to true.
            $palette = ( false === $this->palette || 'false' === $this->palette ) ? 'false' : 'true';
        }

        // Support passing show_opacity as string or boolean. Default to true.
        $show_opacity = ( false === $this->show_opacity || 'false' === $this->show_opacity ) ? 'false' : 'true';

        ?>
            <label>
                <?php // Output the label and description if they were passed in.
                if ( isset( $this->label ) && '' !== $this->label ) {
                  
                    ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label );?></span>
                    <?php
                }
                if ( isset( $this->description ) && '' !== $this->description ) {
                    
                    ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description );?></span>
                    <?php
                } ?>
            </label>
            <div>
                <input class="alpha-color-control" type="text" data-show-opacity="<?php echo esc_attr($show_opacity); ?>" data-palette="<?php echo esc_attr( $palette ); ?>" data-default-color="<?php echo esc_attr( $this->settings['default']->default ); ?>" <?php $this->link(); ?>  />
            </div>
        <?php
    }
}
/*---------------------------------------------------------------------------------------*/

