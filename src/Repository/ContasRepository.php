<?php
namespace GenFin\Repository;
use GenFin\Database\DbConnectionFactory;
use GenFin\Entity\Conta;

use PDO;

class ContasRepository {
    public function find($contaId, $usuarioId) {
        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Contas where id = :conta_id and usuario_id = :usuario_id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':conta_id', $contaId);
        $statement->bindValue(':usuario_id', $usuarioId);
        $statement->execute();
        $contaEncontrada = $statement->fetch(PDO::FETCH_OBJ);

        return $contaEncontrada;
    }

    public function update($conta) {
        $pdo = DbConnectionFactory::get();
        $sql = "UPDATE Contas SET saldo = :saldo where usuario_id=:usuario_id and id=:contaId";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':saldo', $conta->saldo);
        $statement->bindValue(':contaId', $conta->id);
        $statement->bindValue(':usuario_id', $conta->usuarioId);
        $statement->execute();
    }
}