<?php

class Controller
{
    private $delimitador = '9416485941';
    private $qtdTempoToken = 10 * 6;

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

        //VAMOS PEGAR O ID DO USUARIO E RETORNAR, E VAMOS TAMBEM, ANTES DISSO,
        // PEGAR O ID E O NAME DO USER E FAZER UM SELECT PARA VER SE ELE EXISTE DE FATO
        // AFIM DE EVITAR FRAUDEZINHA

        $dataAtual = new Datetime();
        $dataToken = new Datetime($arrayDados[3]);

        $resultado = $dataToken->getTimestamp() - $dataAtual->getTimestamp();
        if($resultado <= 0){
            throw new Exception("Seu token não é mais valido! Favor Relogar!", 1);
        }

        return true;
    }
}
