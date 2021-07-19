<?php

namespace GenFin\Kernel;

use stdClass;
use GenFin\Lib\JsonResponse;

class Kernel
{
    private $method;
    private $uri;
    private $rotas;

    public function bootstrap()
    {
        try {
            // $this->setupDefaultErrorHandling();
            $this->setCorsHeaders();
            $this->handleRequest();
            $this->loadRoute();
            $this->callController();
        } catch (\Throwable $e) {
            $response = new JsonResponse(['mensagem' => $e->getMessage()], 500);
            echo $response->process();
        }
    }

    private function setupDefaultErrorHandling() {
        set_error_handler(function (int $errNo, string $errMsg, string $file, int $line) {});
        set_exception_handler(function ($exception) {
            $message =  "Error: ".$exception->getMessage();
            $response = new JsonResponse(['message' => $message], 500);
            echo $response->process();
        });
    }

    private function setCorsHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }

    private function handleRequest()
    {
        $request = json_decode(file_get_contents('php://input'));
        if ($request == null)
            $request = new stdClass();

        foreach ($_GET as $key => $value) {
            $request->$key = $value;
        }

        $request->token_awt = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        $uriTratamento = $_SERVER["REQUEST_URI"];
        $uriTratamento = explode('?', $uriTratamento);

        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->uri = $uriTratamento[0];
        $this->request = $request;
    }

    private function loadRoute()
    {
        $rotas = [];
        $rotas["POST"]["/login"] = ['AuthController', "login"];
        $rotas["POST"]["/register"] = ['AuthController', "register"];
        $rotas["POST"]["/testeToken"] = ['AuthController', "testeToken"];
        $rotas["GET"]["/contas"] = ['ContasController', "index"];
        $rotas["GET"]["/contas/get"] = ['ContasController', "get"];
        $rotas["POST"]["/contas"] = ['ContasController', "create"];
        $rotas["GET"]["/movimentos"] = ['MovimentosController', "index"];
        $rotas["POST"]["/movimentos"] = ['MovimentosController', "create"];
        $rotas["DELETE"]["/movimentos"] = ['MovimentosController', "delete"];

        $this->rotas = $rotas;
    }

    private function callController()
    {
        file_put_contents('logRotas.html', "buscando rota: $this->method $this->uri" . '<br>', FILE_APPEND);
        $response = null;
        if (isset($this->rotas[$this->method][$this->uri])) {
            $meuController = $this->instanciaClasse($this->rotas[$this->method][$this->uri][0]);
            $response = $this->executaMetodo($meuController, $this->rotas[$this->method][$this->uri][1], [$this->request]);
        } else {
            file_put_contents('logRotas.html', "rota não encontrada!" . '<br>', FILE_APPEND);
            $response = new JsonResponse(['mensagem' => 'rota não encontrada!'], 405);
        }
        echo $response->process();
    }

    private function instanciaClasse($nomeDaClasse)
    {
        $nomeDaClasse = "GenFin\\Controller\\$nomeDaClasse";
        return new $nomeDaClasse();
    }

    private function executaMetodo($objeto, $nomeDoMetodo, $parametros = [])
    {
        return $objeto->{$nomeDoMetodo}(...$parametros);
    }
}
