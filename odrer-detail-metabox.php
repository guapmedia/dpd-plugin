<?php 
class OrderDetail{
    
    private $we;
    private $bank;
    private $adress;
    private $notify;
    private $ser;
        

public function manual_mehtod(){
        global $woocommerce, $post;
        $order = new WC_Order($post->ID);
         $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
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
   
        $weight = $total_weight;
        
        $send = true;

    





        $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
        $sendApi = new Login( $dpd_options['api_key'],$dpd_options['email'],$dpd_options['delis_id'],$dpd_options['ID_adress'],$dpd_options['notification'],$dpd_options['ID_bank']);
        $sendApi->set_adress_recipient($shipping_full_name, $pieces[0], $pieces[1], $shipping_postcode, $shipping_city, $billing_phone, $billing_email, $customer_note,$id,$dpd_options['Shipping'],$weight);
        
        
        
        //$sended = $sendApi->result_massage();
        //$urlLab = $sendApi->result_ulr_label();
       // $re = $sendApi-> req_full();
        //echo $id;
        //echo $re;
          
     $url = "https://capi.dpd.sk/shipment/json";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$data = $sendApi->request();
$body = json_encode( $data);


curl_setopt( $curl, CURLOPT_POSTFIELDS, $body );

$response = curl_exec($curl);
$ress = json_decode( $response,true);

$ackCode = $ress['result']['result'][0]['ackCode'];
//$message = $ress['result']['result'][0]['mesagges'][0][0];
$label = $ress['result']['result'][0]['label'];

$err = curl_error($curl);

curl_close($curl);


if($err){
return "curlError: ".$err;
}else{
    
    if($ackCode ==='success'){
        
        add_post_meta( $id, '_codeRes', $ackCode, $unique = true );
        add_post_meta( $id, '_urlLabel', $label, $unique = true);
        $order->update_status('completed');
        $note = __("Odeslali jsme objednávku do DPD API");
        $order->add_order_note( $note );
        
    
    }else{
        echo $ackCode.'</br></br>';
        echo $response;
    }       
 

    }
    
}

    

    public function a(){
        global $woocommerce, $post,$staCheck;
        $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
        $order = new WC_Order($post->ID);
         $status = $order->get_status();
         $id = $order->get_id();
         $check = "";
         $staCheck = $order->has_status($status);
         $metaAckCode = get_post_meta( $id,  $key = '_codeRes', $single = true );
       // var_dump(get_post_meta( $id,  $key= 'WeigProduct'));
        //$this->get_post_meta();
       

        ?>
                <form method="post" name="form">
                    <label for="IdBank">ID Bankovního účtu</label><br>
                    <input type="text" id="IdBank" name="IdBank" value='<? echo $dpd_options['ID_bank'] ;?>'><br>
                    <label for="IdColl">ID Svozové místo:</label><br>
                    <input type="text" id="IdColl" name="IdColl" value='<? echo $dpd_options['ID_adress'];?>' ><br>

                    
                    <label for="WeigProduct">Váha zásilky:</label><br>
                    <select name="WeigProduct">
                        <option value="2,5"<?=$dpd_options['weight'] == '2,5' ? ' selected="selected"' : '';?>>Lehký balík do 3kg</option>
                        <option value="5"<?=$dpd_options['weight'] == '5' ? ' selected="selected"' : '';?>>Težký balík od 3kg - 31,5kg</option>
                    </select><br>

                    <label for="serviseDpd">Přepravní produkt:</label><br>
                    <select id="ship" name="serviseDpd">
                        <option value="1"<?=$dpd_options['Shipping'] == '1' ? ' selected="selected"' : '';?>>DPD Home</option>
                        <option value="9"<?=$dpd_options['Shipping'] == '9' ? ' selected="selected"' : '';?>>DPD Classic</option>
                        <option value="3"<?=$dpd_options['Shipping'] == '3' ? ' selected="selected"' : '';?>>DPD 10:00</option>
                        <option value="4"<?=$dpd_options['Shipping'] == '4' ? ' selected="selected"' : '';?>>DPD 12:00</option>
                        <option value="2"<?=$dpd_options['Shipping'] == '2' ? ' selected="selected"' : '';?>>DPD 18:00 / DPD Guarantee</option>
                    </select></br>
                    <label for="notifi">Notifikace:</label><br>
                    
                    <input type="checkbox" id="notifi" name="notify"  <? checked( $dpd_options['notification'], 'yes' );?>>
                    <input type="submit" value="Exportovat" name="expo-detail">
                   </form> 
                    
        <?php
        
    
}

   



    public function c(){
        global $woocommerce, $post,$staCheck;
         $dpd_options = get_option('woocommerce_dpd-setting-id_settings');
         $order = new WC_Order($post->ID);
         $status = $order->get_status();
         $id = $order->get_id();
         $check = "";
         $staCheck = $order->has_status($status);
         $metaAckCode = get_post_meta( $id,  $key = '_codeRes', $single = true );
         $metaLabel =   get_post_meta( $id,  $key = '_urlLabel', $single = true );
         
         if($status === 'completed'){
             echo $metaAckCode.'</br></br>';
             //echo $metaLabel.'</br></br>';
             ?>
                <p><a href="<?echo $metaLabel?>" target="_blank"><?php echo $id;?></a></p>
                <? 
                	
            
             
         }else{
            echo $this-> a();
         }

        
    }

    public function get_post_meta(){
        global $woocommerce, $post;
        $order = new WC_Order($post->ID);
        $we = get_post_meta ( $post->ID, 'WeigProduct' );
        $bank = get_post_meta ( $post->ID, 'Idbank' );
        $adress = get_post_meta ( $post->ID, 'Idcoll' );
        $notify = get_post_meta ( $post->ID, 'notify' );
        $ser = get_post_meta ( $post->ID, 'servis' );

        foreach ($ser as $key => $value) {
            echo "servis = $value\n";
        }
        foreach ($notify as $key => $value) {
            echo "notify = $value\n";
        }
        foreach ($adress as $key => $value) {
            echo "adres = $value\n";
        }
        foreach ($bank as $key => $value) {
            echo "bank = $value\n";
        }
        foreach ($we as $a => $v) {
            echo "vaha = $v\n";
        }
       
        echo $we;
        
        

		
	}

   
  

    

}
?>