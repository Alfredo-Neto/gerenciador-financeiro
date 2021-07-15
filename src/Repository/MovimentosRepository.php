<?php
namespace GenFin\Repository;
use PDO;
use GenFin\Database\DbConnectionFactory;

class MovimentosRepository {
    public function findAll($usuarioId, $contaId) {
        $pdo = DbConnectionFactory::get();
            $sql = "SELECT * FROM Movimentos where usuario_id = :usuario_id and conta_id = :conta_id";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':usuario_id', $usuarioId);
            $statement->bindValue(':conta_id', $contaId);
            $statement->execute();
            $movimentosEncontrados = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $movimentosEncontrados;
    }

    public function create($movimento) {
        $pdo = DbConnectionFactory::get();
        $sql = "INSERT INTO Movimentos(descricao, valor, tipo, usuario_id, conta_id) 
        VALUES(:descricao, :valor, :tipo, :usuario_id, :conta_id)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':descricao', $movimento->descricao);
        $statement->bindValue(':valor', $movimento->valor);
        $statement->bindValue(':tipo', $movimento->tipo);
        $statement->bindValue(':usuario_id', $movimento->usuarioId);
        $statement->bindValue(':conta_id', $movimento->contaId);
        $statement->execute();
    }
}