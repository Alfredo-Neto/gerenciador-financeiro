<?php
namespace GenFin\Repository;
use GenFin\Database\DbConnectionFactory;

use PDO;

class ContasRepository {
    public function find($contaId, $usuarioId) {
        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Contas where id = :conta_id and usuario_id = :usuario_id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':conta_id', $contaId);
        $statement->bindValue(':usuario_id', $usuarioId);
        $statement->execute();
        $contaEncontrada = $statement->fetch(PDO::FETCH_ASSOC);

        return $contaEncontrada;
    }
}