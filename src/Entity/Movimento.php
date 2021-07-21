<?php

namespace GenFin\Entity;

use GenFin\Entity\Model;

class Movimento implements Model{
    public $id;
    public $descricao;
    public $valor;
    public $tipo;
    public $usuarioId;
    public $contaId;

    public function __construct ()
    {
        $this->ORMMapping();
    }

    public function ORMMapping()
    {
        if(isset($this->usuario_id) && $this->usuarioId == null){
            $this->usuarioId = $this->usuario_id;
            unset($this->usuario_id);
        }
        if(isset($this->conta_id) && $this->contaId == null){
            $this->contaId = $this->conta_id;
            unset($this->conta_id);
        }
    }
}