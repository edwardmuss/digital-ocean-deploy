<?php

status_result();

function status_result()
{
    $data = file_get_contents('php://input');

    #log the response
    $logFile = "result.json";
    $log = fopen($logFile, "a");
    fwrite($log, $data);
    fclose($log);

    $resultParam = $data['Result']['ResultParameters']['ResultParameter'];
   // $DebitPartyName = $data['Result']['ResultParameters']['ResultParameter'][12]->Value;

    $amount = $data['Result']['ResultParameters']['ResultParameter'][10]['Value'];
    $DebitPartyPhoneWithName = $data['Result']['ResultParameters']['ResultParameter'][0]['Value'];
    $phone = substr($DebitPartyPhoneWithName, 0, 12);
    
    $phoneString = substr($DebitPartyPhoneWithName, 0, strpos($DebitPartyPhoneWithName, '-'));
    $phone9 = substr(trim($phoneString), -9);
    
    $values = $DebitPartyPhoneWithName;
    $values = explode("-",$values);
    $values = trim($values[1]);
    $firstName = substr($values, 0, strpos($values, ' '));

    $message = "$greetings $firstName., your payment of KSH $amount to Roysan Property Care has been received. For any enquiry contact 0716673200.";
    
    // $message = 'Dear '.$firstName.', your payment of KSH '.$amount. ' to Roysan Property Care has been received. For any enquiry contact 0716673200.';
    //Log::info('status_result: - $message - $message');
    //Log::info(json_encode($message));
    $number = $phone9;
    $this->sendSMS($message, $number);
}


/**
     * @param $message
     * @param $number
     */
    function sendSMS($message, $number)
    {
        $url = 'https://send.pepeasms.com/api/services/sendsms/';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'partnerID' => 'eeee',
            'apikey'    => '4444',
            'mobile'    => $number,
            'message'   => $message,
            'shortcode' => 'ASWE',
            'pass_type' => 'plain', //bm5 {base64 encode} or plain
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
       print_r($curl_response);
    }

    # greeting message
    function greeting_msg() 
    {
        $hour = date('H');
        if ($hour >= 18) {
            $greeting = "Good Evening";
        } elseif ($hour >= 12) {
            $greeting = "Good Afternoon";
        } elseif ($hour < 12) {
            $greeting = "Good Morning";
        }
        return $greeting;
    }