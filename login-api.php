<?php 
class Login {

    public $url;
    

    private $id_adress;
    private $id_bank;
    private $api_key;
    private $email;
    private $id_delis;

    private $name;
    private $street;
    private $houeNumber;
    private $zip;
    private $city;
    private $phone;
    private $emailCustomer;
    private $note;

    private $shippingName;
    private $notification;

    public function __construct($api_key,$email,$id_delis,$id_adress,$notification,$id_bank){
       $this-> apiKey =  $api_key;
       $this-> Email = $email;
       $this-> delisId =  $id_delis;
       $this-> idAdress =  $id_adress;
       $this-> noti =  $notification;
       $this-> idbank =  $id_bank;

    }

 public function request(){
        $dpd_options = get_option('woocommerce_dpd-setting-id_settings');

        $noti = array (
            'notifications' =>
             array(
                'notification' => array(
                    'destination' => $this-> email,
                    'type' => '1',
                    'rule' => '1',
            ),
        ),
    );
// add price and paid method 
       $cod = array (
                        'cod' => 
                         array (
                            'bankAccount' => array(
                                'id' =>  $this-> idbank,
                            ),
                             'paymentMethod' => '1',
                             'variableSymbol' => '123',
                             'amount'=>'1231',
                             'currency' => 'EUR'
                         ),
                    );



        $data = array (
                'jsonrpc' => '2.0',
            'method' => 'create',
            'params' => 
            array (
                'DPDSecurity' => 
                array (
                'SecurityToken' => 
                array (
                    'ClientKey' =>  $this-> apiKey,
                    'Email' =>  $this-> Email,
                ),
                ),
                'shipment' => 
                array (
                    'reference' => 'Test '.$this-> idOrder,
                    'delisId' => $this-> delisId,
                    'note' => 'poznamka',
                    'product' => $this-> shippingName,
                    'pickup' => 
                    array (
                    'date' => '20220530',
                    'timeWindow' => 
                    array (
                        'beginning' => '1000',
                    ),
                    ),
                    'addressSender' => 
                    array (
                    'id' => $this-> idAdress,
                    ),
                    'addressRecipient' => 
                    array (
                    'type' => 'b2b',
                    'name' =>  $this-> name,
                    'street' =>  $this-> street,
                    'houseNumber' =>  $this-> houseNumber,
                    'zip' =>  $this-> zip,
                    'country' => 703,
                    'city' =>  $this-> city ,
                    'phone' =>  $this-> phone,
                    'email' =>  $this-> email,
                    'note' =>  $this-> note,
                    ),
                    'notifications' => $this-> noti == 'yes' ? $noti : '',
                    'parcels' => 
                    array (
                    'parcel' => 
                        array (
                        'weight' => $this-> OrderW,
                        ),
                    ),
                    'services' => $dpd_options['Shipping'] == '1' && $dpd_options['notification'] == 'yes' || $dpd_options['Shipping'] != '1' ? $cod : '',
                    
            ),
            'id' => $this-> idOrder,
        ),
  );

        return $data;
    }


public function set_adress_recipient($name,$street,$houeNumber,$zip,$city,$phone,$emailCustomer,$note,$id,$shippingName,$weight){
 $this-> name = $name;
 $this-> street = $street;
 $this-> houseNumber =$houeNumber;
 $this-> zip = $zip;
 $this-> city = $city;
 $this-> phone =$phone;
 $this-> email = $emailCustomer;
 $this-> note = $note;
 $this-> idOrder = $id;
 $this-> OrderW = $weight;
 $this-> shippingName=$shippingName;
 if ($this-> OrderW == 0){
        $this-> OrderW = 4;
    }
 

 

}



}



  


  

?>