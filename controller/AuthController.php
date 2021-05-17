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

            if(!property_exists($request, 'username') || $request->username == null 
            || $request->username == ''){
                throw new Exception("Please inform username field.", 1);
            }

            if(!property_exists($request, 'password') || $request->password == null 
            || $request->password == ''){
                throw new Exception("Please inform password field.", 1);
            }

            if(!property_exists($request, 'repeat') || $request->repeat == null 
            || $request->repeat == ''){
                throw new Exception("Repeat password is empty.", 1);
            }

            if(strcmp($request->password, $request->repeat) != 0){
                throw new Exception("Passwords must be equal.", 1);
            }

            $username = $request->username;
            $password = $request->password;

            $pdo = DbConnectionFactory::get();

            $sql = "SELECT * FROM Usuarios where name like '$username'";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $numRowsUsersSameName = $statement->fetchAll(PDO::FETCH_ASSOC);

            if(count($numRowsUsersSameName) > 0){
                throw new Exception("User already signed up.", 1);
            }

            $sql = "INSERT INTO Usuarios (name, password)
            VALUES (:username, :password)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $password);
            $result = $statement->execute();

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


