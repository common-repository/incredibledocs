<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly        
if ( ! class_exists( 'WP_Customize_Control' ) )  {
    exit; 
}
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer_Padding_Dimensions extends WP_Customize_Control {
    
    /*--------------------------------------------*/
    public function enqueue() {

      wp_enqueue_script( 'idocs-custom-controls-2-js', IDOCS_ADMIN_URL . 'js/idocs-custom-controls-2.js', array( 'jquery' ), IDOCS_VERSION, true );
      //wp_enqueue_style( 'idocs-custom-controls-2-css',  IDOCS_ADMIN_URL . 'css/idocs-custom-controls-2.css', array(), '1.2', 'all' );


    }
    /*--------------------------------------------*/
    public function render_content() {
         
          if ( ! empty( $this->label ) ) : ?>
          <span class="customize-control-title"><?php echo esc_html( $this->label ) . ' (top, right, bottom, left)'; ?></span>
          <?php endif;
          if ( ! empty( $this->description ) ) : ?>
          <p class="description customize-control-description"><?php echo esc_html( $this->description ); ?></p>
          <?php endif;

          if (! is_array( $this->value() ) && ! empty( $this->value() ) )
          $saved_values = explode( ', ', $this->value() );
          //var_dump($saved_values);
          ?>
          <div class="box-model-wrapper" style="padding:0px;">
          <?php
          foreach ( $this->choices as $key => $value ) {
              
              if ( 'padding' === $key ) { ?>
              <div class="box-model-padding" style="padding:0px;">
                  <?php
                  $padding_count = 0; 
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
      /*
    public function render_content() {

      //  error_log("I am here");
       
        if ( ! empty( $this->label ) ) : ?>
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif;
        if ( ! empty( $this->description ) ) : ?>
        <p class="description customize-control-description"><?php echo esc_html( $this->description ); ?></p>
        <?php endif;
        if (! is_array( $this->value() ) && ! empty( $this->value() ) )
        $saved_values = explode( ', ', $this->value() );
        //var_dump($saved_values);
        ?>
        <div class="box-model-wrapper" style="height:180px; margin-top:0px">
        <?php
        foreach ( $this->choices as $key => $value ) {
            
            if ( 'padding' === $key ) { ?>
            <div class="box-model-padding">
                <span><?php echo esc_html__( 'Padding', 'incredibledocs' ); ?></span>
                <?php
                $padding_count = 0; 
                foreach ( $value as $p_key => $p_value ) {
                    ?>
                    <input type="number" placeholder="-" value="<?php echo esc_attr( $saved_values[ $padding_count ] );?>" class="box-model-field '<?php echo esc_attr( $p_key );?>">
                    <?php
                    $padding_count++;
                } ?>
            </div><?php
            }
        } ?>
            <div class="box-model-content">
                <span><?php echo esc_html__( 'Content', 'incredibledocs' ); ?></span>
            </div>
            <input type="hidden" class="box-model-saved" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
        </div>
        <?php
    }
    */
}
/*---------------------------------------------------------------------------------------*/
