<?php
namespace GenFin\Repository;
use GenFin\Entity\Movimento;
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
        $movimentos = $statement->fetchAll(PDO::FETCH_CLASS, 'GenFin\Entity\Movimento');
        return $movimentos;
    }

    public function find ($usuarioId, $contaId, $movimentoId) {
        $pdo = DbConnectionFactory::get();
        $sql = "SELECT * FROM Movimentos where usuario_id = :usuario_id and conta_id = :conta_id and id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':usuario_id', $usuarioId);
        $statement->bindValue(':conta_id', $contaId);
        $statement->bindValue(':id', $movimentoId);
        $statement->execute();
        $movimento = $statement->fetch(PDO::FETCH_CLASS,'GenFin\Entity\Movimento');
        return $movimento;
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