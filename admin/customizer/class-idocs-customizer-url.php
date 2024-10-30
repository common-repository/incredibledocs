<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_URL extends WP_Customize_Control {
    public $type = 'url';

    public function render_content() {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <input type="url" <?php $this->link(); ?> value="<?php echo esc_url( $this->value() ); ?>" />
        </label>
        <?php
    }
}