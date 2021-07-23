<?php

namespace GenFin\Entity;

abstract class Model implements ModelInterface {
    
    public function __construct ()
    {
        $this->ORMMapping();
    }
}