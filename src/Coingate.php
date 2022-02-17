<?php

namespace Artisanssoft\Coingate;



class Coingate 
{
    public function __construct(){

        // cryptocurrency Payment Gateway Constants
        if (!defined('CRIPTO_BASE_URL')) define('CRIPTO_BASE_URL', "https://api-sandbox.coingate.com");
        if (!defined('CRIPTO_AUTH_TOKEN')) define('CRIPTO_AUTH_TOKEN', 'Token 9g-poL22qLXBNCAfLeiAPgm-Lp9Rsss3pzzF4AqR');
    }
    
    function test(){
        echo "Hello Word";
        echo CRIPTO_BASE_URL;
    }

    /**
     * cryptocurrency Payment Gateway
     * Make Payment
     */
    public function cryptocurrencyPay($order_id,$amount,$price_currency,$receive_currency){
       try {
                $url = CRIPTO_BASE_URL."/v2/orders";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                    "Content-Type: application/json",
                    "Authorization:".CRIPTO_AUTH_TOKEN.""
                    );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $data = '{"price_amount":"'.$amount.'","price_currency":"'.$price_currency.'","receive_currency":"'.$receive_currency.'","order_id":'.$order_id.'}';

                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(isset(json_decode($resp)->id)){
                    $response['status'] =200;
                    $response['msg'] ="sucess"; 
                    $response['data'] =json_decode($resp); 
                    $response['payment_url'] = json_decode($resp)->payment_url;
                    return $response;
                } else{
                    $response['status'] =400;
                    $response['data'] =json_decode($resp); 
                    $response['msg'] ="Something went wrong"; 
                    $response['payment_url'] = "";
                    return $response;
                }
            } catch (\Throwable $th) {
                $response['status'] =400;
                $response['data'] =""; 
                $response['msg'] =$th; 
                $response['payment_url'] = "";
                return response()->json($response);
            }
    }

    /**
     * check cryptocurrency Payment Gateway status
     * checkStatus
     */
    public function cryptocurrencyPayStatus($order_id,$cryto_order_id){
        try {
                $url = CRIPTO_BASE_URL."/v2/orders/".$cryto_order_id;
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                "Content-Type: application/json",
                "Authorization:".CRIPTO_AUTH_TOKEN.""
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(isset(json_decode($resp)->id)){
                    if(json_decode($resp)->status=="paid"){
                        $pay_detail = $this->getCryptoPaymentDetail($order_id,json_decode($resp)->payment_url);
                    } else {
                        $pay_detail = "";
                    }
                    $response['status'] =200;
                    $response['msg'] ="success"; 
                    $response['data'] =json_decode($resp); 
                    $response['payment_detail'] =$pay_detail; 
                    $response['payment_status'] = json_decode($resp)->status;
                    return $response;
                } else{
                    $response['status'] =400;
                    $response['data'] =""; 
                    $response['msg'] ="Something went wrong"; 
                    $response['payment_status'] = "";
                    $response['payment_detail'] =""; 
                    return $response;
                }
                  //code...
            } catch (\Throwable $th) {
                $response['status'] =400;
                $response['data'] =""; 
                $response['msg'] =$th; 
                $response['payment_status'] = "";
                return $response;
            }
    }



    /**
     * get Invice data
     * get payment detail
     */
    private function getCryptoPaymentDetail($order_id,$url)
    {
        try {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $headers = array(
                    "Content-Type:application/json",
                    "User-Agent:PostmanRuntime/7.26.8",
                 );

                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);
                if(isset(json_decode($resp)->id)){
                    $response['status'] =200;
                    $response['msg'] ="success"; 
                    $response['data'] =$resp; 
                    $response['transaction_id'] = count(json_decode($resp)->transactions)>0? json_decode($resp)->transactions[0]->txid:"";
                    return $response;
                } else{
                    $response['status'] =400;
                    $response['data'] =""; 
                    $response['msg'] ="fail"; 
                    $response['transaction_id'] = "";
                    return $response;
                }
                  //code...
            } catch (\Throwable $th) {
                $response['status'] =400;
                $response['data'] =""; 
                $response['msg'] =$th; 
                $response['transaction_id'] = "";
                return $response;
            }
    }
}
