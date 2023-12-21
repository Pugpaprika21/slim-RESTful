<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

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

if (!function_exists('file_uploaded')) {

    /**
     * @param string $directory
     * @param UploadedFileInterface $uploadedFile
     * @return string
     */
    function file_uploaded(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}

if (!function_exists('esc')) {

    /**
     * @param string $data
     * @return string
     */
    function esc(string $data): string
    {
        if (!is_string($data)) {
            $data = (string)$data;
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $data;
    }
}
