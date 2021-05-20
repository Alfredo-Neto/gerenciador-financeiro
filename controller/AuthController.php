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
        $token_awt = base64_decode($token_awt);
        $arrayDados = explode($this->delimitador, $token_awt);
        return $arrayDados;
    }

    private function validateAWT($token_awt){
        $arrayDados = $this->decodeAWT($token_awt);

        $dataAtual = new Datetime();
        $dataToken = new Datetime($arrayDados[3]);
        $interval = $dataAtual->diff($dataToken);
        // $dadosRetorno = [
        //     'atual' => $dataAtual->format('Y-m-d H:i:s'),
        //     'token' => $dataToken->format('Y-m-d H:i:s'),
        //     'resultado' => $interval->invert, //se 1, negativo
        //     'resultado' => $interval->format('s'),
        // ];

        $resultado = $dataAtual->getTimestamp() - $dataToken->getTimestamp();
        if($resultado >= 0){
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

            return new JsonResponse (['mensagem' => 'seu token é valido, pode fazer usa operação'], 200);

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
            $usuariosEncontrados = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($usuariosEncontrados)) {
                throw new Exception("Usuário não encontrado");
            }

            if (!password_verify ($request->password , $usuariosEncontrados[0]['password'])) {
                throw new Exception("Senha incorreta");
            }

            // Vamos criar um hash que significa que o usuario está autenticado e até que horas ele está autenticado

            $token_awt = $this->makeAWT($usuariosEncontrados[0]['name'],$usuariosEncontrados[0]['id']);
            // $desembaralhado = $this->decodeAWT($embaralhado);

            return new JsonResponse (['token_awt' => $token_awt], 200);
            
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
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

            // $rows = $result->fetchAll();
            return new JsonResponse (['mensagem' => $mensagem], 201);
        } catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()],500);
        }
    }
}


