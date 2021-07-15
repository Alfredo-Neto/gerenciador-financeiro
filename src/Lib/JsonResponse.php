<?php

namespace GenFin\Lib;

class JsonResponse //implements Response
{
    private $data;
    private $code;
    
    public function __construct($data, $code)
    {
        $this->data = $data;
        $this->code = $code;
    }

    public function process()
    {
        http_response_code($this->code); //https://www.php.net/manual/pt_BR/function.http-response-code.php
        return json_encode([
            // 'code' => $this->code, // FINS DE DEBUG
            // 'codeString' => $this->processCode(), // FINS DE DEBUG
            'data' => $this->data
        ]);
    }

    private function processCode()
    {
        switch ($this->code) {
            case '200':
                return 'OK';
                break;

            case '201':
                return 'CREATED';
                break;

            case '400':
                return 'BAD REQUEST';
                break;
                
            case '401':
                return 'UNAUTHORIZED';
                break;

            case '404':
                return 'NOT FOUND';
                break;

            case '500':
                return 'INTERNAL SERVER ERROR';
                break;

            default:
                throw new Exception("HTTP error code not supported", 1);
                break;
        }
    }
}
