<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

/**
 * @param Response $response
 * @param mixed $dataSet
 * @return Response
 */
function generate_token_jwt(Response $response, mixed $dataSet): Response
{
    global $env;

    $key = $env['APP_API_KEY'];
    $issued_at = time();
    $payload = [
        'iat' => $issued_at,
        'exp' => ($issued_at + 60),
        'sub' => $dataSet,
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    return json($response, ['jwt_token' =>  $jwt]);
}

/**
 * @param Request $request
 * @param Response $response
 * @return Response
 */
function validate_token_jwt(Request $request, Response $response): Response
{
    global $env;

    $key = $env['APP_API_KEY'];

    $authHeader = $request->getHeader('Authorization');
    if (empty($authHeader)) {
        throw new HttpUnauthorizedException($request);   
    }

    $jwt = str_replace('Bearer ', '', $authHeader[0]);

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return json($response, ['data' => $decoded]);
    } catch (ExpiredException $e) {
        return json($response, ['msg' => 'Token expired..'], 401);
    } catch (SignatureInvalidException $e) {
        return json($response, ['msg' => 'Invalid token signature..'], 401);
    } catch (BeforeValidException $e) {
        return json($response, ['msg' => 'Token not valid yet..'], 401);
    } catch (Exception $e) {
        return json($response, ['msg' => 'Invalid token..'], 401);
    }
}


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
