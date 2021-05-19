<?php

class AuthController
{
    public function login($request)
    {
        try {

            if (!property_exists($request, 'username') && !property_exists($request, 'password') 
            || $request->username == '' || $request->username == null
            || $request->password == '' || $request->password == null )
            {
                throw new Exception("Campos precisam ser preenchidos");
            }
            
          $pdo = DbConnectionFactory::get();
          $sql = "SELECT * FROM Usuarios";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $numRowsUsersSameName = $statement->fetchAll(PDO::FETCH_ASSOC);

          return new JsonResponse (['mensagem' => $numRowsUsersSameName], 200);
          
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()],500);
        }
        
        // if($this->checkLogin($request->username, $request->password)) { // se a verificação do login for verdadeira
        //     return new JsonResponse (['token' => '123456'],200);
        // } else {
        //     return new JsonResponse ([],401);
        // }
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

            $passwordHash = password_hash ($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Usuarios (name, password)
            VALUES (:username, :password)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $passwordHash);
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


