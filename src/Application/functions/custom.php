<?php

function send_otp($new_pass_otp, $phone)
{
    global $env;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $env['OTP_URL'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="UTF-8"?>
            <SingleSmsApiModel>
                <SenderId>TRD-HR</SenderId>
                <Is_Unicode>true</Is_Unicode>
                <Message>รหัสผ่านใหม่ของท่านคือ : ' . $new_pass_otp . '</Message>
                <MobileNumbers>66' . substr($phone, 1) . '</MobileNumbers>
                <ApiKey>' . $env['OTP_API_KEY'] . '=</ApiKey>
                <ClientId>' . $env['OTP_CLIENTT_ID'] . '</ClientId>
            </SingleSmsApiModel>',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/xml'
        ),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));

    $response = curl_exec($curl);
    $error = curl_error($curl);
    $errno = curl_errno($curl);

    curl_close($curl);
}