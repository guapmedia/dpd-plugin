<?php
class WC_DPD_Login_Page extends WC_Shipping_Method {
				
				public function __construct() {
					$this->id                 = 'dpd-setting-id'; 
					$this->method_title       = __( 'DPD standartní nastavení' );  
					$this->method_description = __( 'DPD general setting for API DPD SK ' );

					
					$this->title              = "My Shipping Method"; 
                    
      
					$this->init();
				}

				
				function init() {
					
					$this->init_form_fields(); 
					$this->init_settings(); 
                   
                    $this->delis_id 				= $this->get_option( 'delis_id' );
		            $this->email 	= $this->get_option( 'email' );
		            $this->api_key 	= $this->get_option( 'api_key' );
		            $this->ID_bank		= $this->get_option( 'ID_bank' );
		            $this->ID_adress 		= $this->get_option( 'ID_adress' );
                    $this->notification 		= $this->get_option( 'notification' );
                    $this->shipping 		= $this->get_option( 'shipping' );
                    $this->weight 		= $this->get_option( 'weight' );
					
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                    //add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'setting_process_save' ) );
				}

				

        function init_form_fields(){
		
		$this->form_fields = array(
			'delis_id' => array(
				'title' => 'ID delis',
				'type' => 'text',
				'label' => __('This is the username you got from DPD to login to their service'),
                'description' 	=> __( 'unique customer identifier assigned by DPD.'),
				'desc_tip' => true,
			),
			'email' => array(
				'title' => 'Klient email',
				'type' => 'text',
				'label' => __('This is the password you got from DPD to login to their service'),
                'description' 	=> __( 'This controls the title which the user sees during checkout.'),
				'desc_tip' => true
			),
			'api_key' => array(
				'title' => 'API key ',
				'type' => 'text',
				'label' => __('Your api key'),
                'description' 	=> __( 'unique authentication key required to access API.'),
				'desc_tip' => true
			),
			'ID_bank' => array(
				'title' => 'ID bankového účtu',
				'type' => 'text',
				'label' => __('This enables DPD Label printing for all orders'),
                'description' 	=> __( 'This controls the title which the user sees during checkout.'),
				'desc_tip' => true
			),
			'ID_adress' => array(
				'title' 		=> 'ID svozove adresy',
				'type' 			=> 'text',
				'description' 	=> __( ''),
				'desc_tip'		=> true
			),
            'Shipping' => array(
				'title' 		=> 'Doprava',
				'type' 			=> 'select',
				'description' 	=> __( ''),
				'desc_tip'		=> true,
                'options' => array(
                    '1' => 'DPD Classic',
                    '9' => 'DPD Home',
                    '3' => 'DPD 10:00',
                    '4' => 'DPD 12:00',
                    '2' => 'DPD 18:00 / DPD Guarantee',
                )                  
			),
            'weight' =>array(
                'title'=>'Váha doručovácího produktu',
                'type'=>'select',
                'description' => __(''),
                'options' => array(
                    '2,5' => 'Lehký balík',
                    '5'=> 'těžký balík'
                )
            ),
            'notification' => array(
				'title' 		=> 'Notifikace',
				'type' 			=> 'checkbox',
				'description' 	=> __( ''),
				'desc_tip'		=> true,
                'default'		=> 'no'
                
			)
		); 
	}
    
	// --- save 
	public function setting_process_save(){

		
		$email 	= sanitize_text_field($_POST['email']);
		$api_key	= sanitize_text_field($_POST['api_key']);

        $delis_id 		= sanitize_text_field($_POST['delis_id']);
        $ID_bank 		= sanitize_text_field($_POST['ID_bank']);
        $ID_adress 		= sanitize_text_field($_POST['ID_adress']);
        $notification 		= sanitize_text_field($_POST['notification']);
        $shipping 		= sanitize_text_field($_POST['Shipping']);
        $weight 		= sanitize_text_field($_POST['weight']);

		update_option( 'email', $email );
		update_option( 'api_key', $api_key );

		update_option( 'delis_id', $delis_id );
        update_option( 'ID_bank', $ID_bank );
		update_option( 'ID_adress', $ID_adress );
		update_option( 'notification', $notification );
        update_option( 'shipping', $shipping );
        update_option( 'weight', $weight );
	}
    


				
}
?>