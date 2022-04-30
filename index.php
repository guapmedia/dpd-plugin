<?php
  /*
   Plugin Name: DPD for WOO 
   Plugin URI: 
   description: plugin communicates with API DPD 
   Version: 0.8.5
   Author: Roman Kyrych, Ondřej Lukáš
   Author URI: https://wwww.guapmedia.cz
   License: GNU-GPL 
   */

  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
     
       
      include 'login-api.php';

	function dpd_shiping_method_init() {
		if ( ! class_exists( 'WC_DPD_Login_Page' )) {
			include 'dpd-shipping-setting.php';
		}
	}

	add_action( 'woocommerce_shipping_init', 'dpd_shiping_method_init' );

    function add_dpd_methods( $methods ) {
		$methods['dpd-setting-id'] = 'WC_DPD_Login_Page';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_dpd_methods' );


	
   //---- create column to order for export buttn, fail massage and shipping label link 
    
function addColumnOrders( $columns ) {

    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        if ( 'order_status' === $column_name ) {
            $new_columns['export_dpd'] = __( 'Export do DPP');           
        }
    }

    return $new_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'addColumnOrders');

//--- add content column  
function ContentOrderExport($column){
     global $woocommerce, $post;
      $order = new WC_Order($post->ID);
         $status = $order->get_status();
         $order_id = $order->get_id();
         $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
        $click = get_post_meta( $order_id, '_codeClick', $unique = true );
        $idclick = get_post_meta( $order_id, '_cellIdOrder', $unique = true );
       $code = get_post_meta( $order_id, '_codeRes', $unique = true );
      $label = get_post_meta( $order_id, '_urlLabel', $unique = true );

    if ( 'export_dpd' === $column ) {
        if($status == 'processing' ){
            $post_id = isset($_GET['post']) ? $_GET['post'] : false;
    //if(! $post_id ) return; 

    $value=$order_id ;
    ?>
        
        <p><a href="?post_type=shop_order&detail=<?echo $value?>" class="button"><?php _e('Exportovat'); ?></a></p>
    
        
    <?php
    if ( isset( $_GET['detail'] ) && ! empty( $_GET['detail'] ) ) {
        
echo '<p>Value: '.$value.'</p>';
        update_post_meta( $order_id, '_codeClick', 'yes', $unique = false );
        update_post_meta( $order_id, '_cellIdOrder', $value, $unique = false );
         
    }    
       
        }elseif($status == 'completed'){
                echo $code. '</br>';
               ?>
                <p><a href="<?echo $label?>" target="_blank"><?php echo $order_id;?></a></p>
                <? 
        }
      
    }
        
}  
add_action( 'manage_shop_order_posts_custom_column' , 'ContentOrderExport' );

add_action( 'init', 'process_my_form' );
function process_my_form() {
     if( isset( $_GET['detail'] ) && ! empty( $_GET['detail'] ) ) {
          send_request_manual($_GET['detail']);
     }
}



    function write_to_console($data) {
        $console = 'console.log(' . json_encode($data) . ');';
        $console = sprintf('<script>%s</script>', $console);
        echo $console;
    }




// Register  Statuses
function wpex_wc_register_post_statuses() {
    register_post_status( 'wc-shipping-dpd', array(
        'label'                     => 'Dodaná',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Dodaná (%s)', 'Dodaná (%s)')
    ) );
}
add_filter( 'init', 'wpex_wc_register_post_statuses' );

// Add New Order Statuses to WooCommerce
function wpex_wc_add_order_statuses( $order_statuses ) {
    $new_order_statuses = array ( ) ;
    foreach ( $order_statuses as $key => $status ) {  
        $new_order_statuses [ $key ] = $status;
        if ( 'wc-processing' === $key ) {   
            $order_statuses['wc-completed'] = 'Dodaná';
        }
    }
    return $order_statuses;
}
add_filter( 'wc_order_statuses', 'wpex_wc_add_order_statuses' );


    // add metabox detaail page order 
    function mv_add_meta_boxes(){
        
        add_meta_box( 'export_field', __('Export do DPD','woocommerce'), 'metaBoxExport', 'shop_order', 'side', 'core' );
        
    }
    add_action( 'add_meta_boxes', 'mv_add_meta_boxes' );

    function metaBoxExport(){
        include 'odrer-detail-metabox.php';
        $metabox = new OrderDetail();
        $metabox -> c();         
    }
    
     function diwp_save_custom_metabox(){
 
    global $post;
    if(isset( $_POST['expo-detail'])){
        include 'odrer-detail-metabox.php';
        $metabox = new OrderDetail();
        $metabox ->manual_mehtod() ;
        if( isset( $_POST['serviseDpd']) ){
           update_post_meta ( $post->ID, $key='servis' , sanitize_text_field ( $_POST["serviseDpd"]));
        }
        if( isset( $_POST['notify']) ){
           update_post_meta ( $post->ID, $key='notify' , sanitize_text_field ( $_POST['notify'])); 
        }
        if( isset( $_POST['IdColl']) ){
            update_post_meta ( $post->ID, $key='Idcoll' , sanitize_text_field ( $_POST['IdColl']));
        }
        if( isset($_POST['IdBank'])) {
             update_post_meta ( $post->ID, $key='Idbank' , sanitize_text_field ( $_POST['IdBank']));
        }
        if( isset($_POST['WeigProduct']) ){
            update_post_meta ( $post->ID, $key ,'WeigProduct' , sanitize_text_field ( $_POST['WeigProduct']));
        }
        }
        
    }
 
    add_action('save_post', 'diwp_save_custom_metabox');




 
     
     function send_request_manual($order_id){
        global $woocommerce, $post;
         $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
         $order = wc_get_order( $order_id );
         
         $id = $order->get_id();
         $shipping_first_name = $order->get_shipping_first_name();
         $shipping_last_name  = $order->get_shipping_last_name();
         $shipping_full_name = $shipping_first_name.' '.$shipping_last_name;
         
        
        $shipping_company    = $order->get_shipping_company();
        $shipping_address_1  = $order->get_shipping_address_1();
        $pieces = explode(" ", $shipping_address_1);
        $pieces[0]; // piece1 string street
        $pieces[1]; // piece2 number house

        $shipping_city = $order->get_shipping_city();
        $shipping_postcode   = $order->get_shipping_postcode();

        $billing_email  = $order->get_billing_email();
        $billing_phone  = $order->get_billing_phone();

        $customer_note = $order->get_customer_note();

        //$shipping_address_2  = $order->get_shipping_address_2();
    foreach( $order->get_items("shipping") as $item_id => $item ){
            $shipping_method_title       = $item->get_method_title();
        }

        foreach( $order->get_items() as $item_id => $product_item ){
        $quantity = $product_item->get_quantity();
        $product = $product_item->get_product(); 
        $product_weight = $product->get_weight(); 
       
        $total_weight += floatval( $product_weight * $quantity );
    }
   $paid_method = $order-> get_payment_method();

        
    
        
        $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
        $sendApi = new Login( $dpd_options['api_key'],$dpd_options['email'],$dpd_options['delis_id'],$dpd_options['ID_adress'],$dpd_options['notification'],$dpd_options['ID_bank']);
        $sendApi->set_adress_recipient($shipping_full_name, $pieces[0], $pieces[1], $shipping_postcode, $shipping_city, $billing_phone, $billing_email, $customer_note,$order_id,$dpd_options['Shipping'],$total_weight,$paid_method);


          
     $url = "https://capi.dpd.sk/shipment/json";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


$data = $sendApi->request() ;
$body = json_encode( $data);


curl_setopt( $curl, CURLOPT_POSTFIELDS, $body );

$response = curl_exec($curl);
$ress = json_decode( $response,true);

$ackCode = $ress['result']['result'][0]['ackCode'];
$message = $ress['result']['result'][0]['mesagges'];
$label = $ress['result']['result'][0]['label'];

$err = curl_error($curl);

curl_close($curl);

    if($ackCode === 'success'){
    add_post_meta( $order_id, '_codeRes', $ackCode, $unique = true );
    add_post_meta( $order_id, '_urlLabel', $label, $unique = true );
    
    $note = __("Odeslali jsme objednávku do DPD API");
    $order->add_order_note( $note );

    $order->update_status('completed');

     echo $response;
    }

    

     

        
     }

     
     

     

  
 
    
}


  

?>