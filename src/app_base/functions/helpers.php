<?php

use Psr\Http\Message\ResponseInterface as Response;

if (!function_exists('json')) {

    /**
     * @param Response $response
     * @param array $data
     * @param integer $status
     * @return Response
     */
    function json(Response $response, array $data = [], int $status = 200): Response
    {
        $response->getBody()->write(json_encode(arr_upr(['data' => $data]), JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

if (!function_exists('arr_upr')) {
    
    /**
     * @param array|object $input
     * @param int $case
     * @return array
     */
    function arr_upr($input, $case = MB_CASE_TITLE)
    {
        $convToCamel = function ($str) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
        };

        if (is_object($input)) {
            $input = json_decode(json_encode($input), true);
        }

        $newArray = array();
        foreach ($input as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $newArray[$convToCamel($key)] = arr_upr($value, $case);
            } else {
                $newArray[$convToCamel($key)] = $value;
            }
        }
        return $newArray;
    }
}
