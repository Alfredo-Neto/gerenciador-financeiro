<?php

class AuthController
{

    private $delimitador = '9416485941';
    private $qtdTempoToken = 10;

    private function makeAWT($nome,$id){
        $data = new Datetime();
        $data->add(new DateInterval('PT' . $this->qtdTempoToken . 'S'));
        $dataFormatada = $data->format('Y-m-d H:i:s');

        $token_awt = $this->delimitador . $nome . $this->delimitador . $id . $this->delimitador . $dataFormatada . $this->delimitador;
        $token_awt = base64_encode($token_awt);
        return $token_awt;
    }

    private function decodeAWT($token_awt){
        $decoded_token = base64_decode($token_awt);
        $arrayDados = explode($this->delimitador, $decoded_token);
        return $arrayDados;
    }

    private function validateAWT($token_awt){
        $arrayDados = $this->decodeAWT($token_awt);

        $dataAtual = new Datetime();
        $dataToken = new Datetime($arrayDados[3]);

        $resultado = $dataToken->getTimestamp() - $dataAtual->getTimestamp();
        if($resultado <= 0){
            throw new Exception("Seu token não é mais valido! Favor Relogar!", 1);
        }

        return true;
    }

    public function testeToken($request)
    {
        try {

            if(!property_exists($request, 'token_awt') || $request->token_awt == null 
            || $request->token_awt == ''){
                throw new Exception("Please inform token_awt field.", 1);
            }
    
            $this->validateAWT($request->token_awt);

            return new JsonResponse (['mensagem' => 'seu token é valido, pode fazer sua operação'], 200);

        } catch (Exception $e) {
            // Seu token não é mais valido! Favor Relogar! //vamos criar uma forma de especificar esse erro!!
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
        }
    }

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
            $sql = "SELECT * FROM Usuarios where name like '$request->username'";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $usuariosEncontrados = $statement->fetch(PDO::FETCH_ASSOC); //fetchall retorna um array // fetch retorna um usuario só

            if (empty($usuariosEncontrados)) {
                throw new Exception("Usuário não encontrado");
            }

            if (!password_verify ($request->password , $usuariosEncontrados['password'])) {
                throw new Exception("Senha incorreta");
            }

            $token_awt = $this->makeAWT(
                $usuariosEncontrados['name'],
                $usuariosEncontrados['id']
            );

            return new JsonResponse (['token_awt' => $token_awt], 200);
            
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
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

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Usuarios (name, password)
            VALUES (:username, :password)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $passwordHash);
            $result = $statement->execute();

            $mensagem = '';
            if($result == true) {
                $mensagem = 'Cadastrado com sucesso!';
            } else {
                $mensagem = 'Erro no cadastro! Verifique seu email!';
            }

            return new JsonResponse (['mensagem' => $mensagem], 201);
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()],500);
        }
    }
}


