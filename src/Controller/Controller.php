<?php

namespace GenFin\Controller;
use PDO;
use \DateTime;
use \DateInterval;
use GenFin\Lib\AuthorizationException;
use GenFin\Database\DbConnectionFactory;

class Controller
{
    private $delimitador = '9416485941';
    private $qtdTempoToken = 60 * 10 * 6;

    protected function makeAWT($nome,$id){
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

    protected function validateAWT($token_awt){
        $arrayDados = $this->decodeAWT($token_awt);

        $dataAtual = new Datetime();
        $dataToken = new Datetime($arrayDados[3]);

        $resultado = $dataToken->getTimestamp() - $dataAtual->getTimestamp();
        if($resultado <= 0){
            throw new AuthorizationException("Seu token não é mais valido! Favor Relogar!", 1);
        }

        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Usuarios where id = $arrayDados[2] and name like '$arrayDados[1]'";

        $statement = $pdo->prepare($sql);
        $statement->execute();
        $usuariosEncontrados = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$usuariosEncontrados) {
            throw new AuthorizationException("Usuário inválido! Favor Relogar!", 1);
        }

        return $arrayDados;
    }
}
