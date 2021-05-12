<?php

class AuthController
{
    private $cadastros = [
        'alfredo' => '123456',
        'rui' => '321654',
    ];

    private function checkLogin ($username, $password)
    {
        if( isset( $this->cadastros[$username] ) ) { //se o login existe no array
            if ($this->cadastros[$username] == $password) { // se a senha é igual ao valor daquele login no array
                return true;
            }
        }
        return false;
    }

    public function login($request)
    {
        if($this->checkLogin($request->username, $request->password)) { // se a verificação do login for verdadeira
            return new JsonResponse (['token' => '123456'],200);
        } else {
            return new JsonResponse ([],401);
        }
    }

    public function register($request)
    {
        try {
            $name = $request->username;
            $password = $request->password;

            $pdo = DbConnectionFactory::get();
            $sql = "INSERT INTO Usuarios (name, password)
            VALUES ('$name', '$password')";
            $result = $pdo->query( $sql );

            $mensagem = '';
            if($result == true) {
                $mensagem = 'foi!';
            } else {
                $mensagem = 'deu pau';
            }

            // $rows = $result->fetchAll();
            return new JsonResponse (['mensagem' => $mensagem], 201);
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()],500);
        }
    }
}


