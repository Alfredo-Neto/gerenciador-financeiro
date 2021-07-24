<?php

namespace GenFin\Service;

use GenFin\Controller\MovimentosController;
use GenFin\Repository\ContasRepository;
use GenFin\Repository\MovimentosRepository;
use GenFin\Entity\Conta;

class ContasService {

    private MovimentosRepository $movimentosRepository;
    private ContasRepository $contasRepository;

    public function __construct() {
        $this->movimentosRepository = new MovimentosRepository;
        $this->contasRepository = new ContasRepository;
    }

    public function atualizarMovimentosDaConta(Conta $conta) {
        $movimentosEncontrados = $this->movimentosRepository->findAll($conta->usuarioId, $conta->id);
            
        $totalDaConta = 0;
        foreach ($movimentosEncontrados as $key => $movimento) {
            if ($movimento->tipo == 2) {
                $totalDaConta += $movimento->valor;
            } else {
                $totalDaConta -= $movimento->valor;
            }
        }

        $conta->saldo = $totalDaConta;
        $this->contasRepository->update($conta);
    }
}