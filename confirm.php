<?php

    $response = 
    '{
        "TransactionType": "Pay Bill",
        "TransID": "RAJ7PFM0SH",
        "TransTime": "20230119121544",
        "TransAmount": "1.00",
        "BusinessShortCode": "4095073",
        "BillRefNumber": "test",
        "InvoiceNumber": "",
        "OrgAccountBalance": "212391.00",
        "ThirdPartyTransID": "",
        "MSISDN": "2547 ***** 797",
        "FirstName": "EDWARD"
    }';

    # Response from M-PESA Stream
    $mpesaResponse = file_get_contents('php://input');

    #log the response
    $logFile = "confirm.json";
    $log = fopen($logFile, "a");
    fwrite($log, $response);
    fclose($log);

    $jsonMpesaResponse = json_decode($response, true);

    $number = $jsonMpesaResponse['MSISDN'];
    $amount = $jsonMpesaResponse['TransAmount'];
    $transID = $jsonMpesaResponse['TransID'];

    // generateToken();exit;

    //Query trans Status
    transactionStatus($transID);
    /**
     *
     */
    function generateToken()
    { 
        $consumerKey = 'EVYbkTApCj4KpBbEqzFXXNZtzCwAco9F'; //Fill with your app Consumer Key
        $consumerSecret = 'ylNbVY2Axgogcmwq'; // Fill with your app Secret

        // $credentials = base64_encode($consumerKey . ":" .  $consumerSecret);

        // echo $credentials;

        $headers = ['Content-Type:application/json; charset=utf8'];

        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);

        $access_token = $result->access_token;

        // echo $access_token; exit;
        return $access_token;
        
        curl_close($curl);
    }

    /**
     * @param $url
     * @param $body
     * @return bool|string
     */
    function makeHttp($url, $body)
    {
        $token = generateToken();
        $curl  = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL        => $url,
                CURLOPT_HEADER     => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/json',
                    'Authorization: Bearer' . $token,
                ),
            )
        );
        $data_string = json_encode($body);
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $data_string,
            )
        );
        $response = curl_exec($curl);
        return $response;
        print_r($response);
    }

    /**
     * @param Request $request
     */
    function balanceResultURL(Request $request)
    {
       // Log::info('Account balance url endpoint hit');
    }

    /**
     * @param Request $request
     */
    function timeoutURL(Request $request)
    {
       // Log::info('Timeout url endpoint ');
    }


    /**
     * @param Request $request
     */
    function validation(Request $request)
    {
       // Log::info('validation endpoint hit');
    }

    /**
     * @param $message
     * @param $number
     */


/**
     * Transaction Status API checks the status of a B2B, B2C and C2B APIs transactions.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    function transactionStatus($transID) {
        $token = generateToken();
        $url = 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'CommandID'             => 'TransactionStatusQuery',
            'PartyA'                => '4095073',
            'IdentifierType'        => '4',
            'Remarks'               => 'Check Status',
            'Initiator'             => 'pepeaweb',
            'SecurityCredential'    => 'hGLkYUo2taZBBO7PAvvYZFhFIiuJkxpR8BW9eTRsNzrKBDWcSZVy8zpXc5VklZ5xJz2oDxTkJgp8ZkZHNu1KSE5r6p0ce+YxRwOmW7njyR4+LdADHdUOaSbBcem7uzy34QCroPNTvg9oe28cbs9Q81Y+9RH8y57URB7u6Uug4Z8/oiiasACl0XbR03zkqNRo5pm4BhyczwAxcU329XBtoLJ4HTn2YfxfclrMD2S90OH7+uvcSUjtGYtVct2aqEwdAK2249EJbhuJF1OhH3SCklCMVENqDoZ4UNxttJTg5EM0e/ceF/4J4G4FykOS2GfOxqn4g/y8UNDUp+r3AjBsGw==',
            'QueueTimeOutURL'       => 'https://www.pepeasms.com/payments/c2b/status_timeout.php',
            'ResultURL'             => 'https://www.pepeasms.com/payments/c2b/status_results.php',
            'TransactionID'         =>  $transID,
            'Occasion'              => 'PEPEA'
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        print_r($curl_response);
        // echo $curl_response;
    }

?>
