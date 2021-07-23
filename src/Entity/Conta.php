<?php
namespace GenFin\Entity;

class Conta extends Model{
    public $id;
    public $nome;
    public $saldo;
    public $usuarioId;

    public function ORMMapping()
    {
        if(isset($this->usuario_id) && $this->usuarioId == null){
            $this->usuarioId = $this->usuario_id;
            unset($this->usuario_id);
        }
    }
}