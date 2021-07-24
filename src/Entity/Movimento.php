<?php

namespace GenFin\Entity;

use GenFin\Entity\Model;

class Movimento extends Model{
    public $id;
    public $descricao;
    public $valor;
    public $tipo;
    public $usuarioId;
    public $contaId;
    public $ormMapping = [
        "usuario_id" => "usuarioId",
        "conta_id" => "contaId"
    ];

    // criar função ORMMapping no próprio Model

    public function ORMMapping()
    {
        if (isset($this->usuario_id) && $this->usuarioId == null){
            $this->usuarioId = $this->usuario_id;
            unset($this->usuario_id);
        }
        if(isset($this->conta_id) && $this->contaId == null){
            $this->contaId = $this->conta_id;
            unset($this->conta_id);
        }
    }
}