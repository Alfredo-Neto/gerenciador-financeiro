<?php

// CRIAR UMA ROTA PARA LISTAR TODOS OS MOVIMENTOS DE UMA CONTA
class MovimentosController extends Controller {
    
   public function index($request){
        try { 
            if(!property_exists($request, 'token_awt') || $request->token_awt == null 
            || $request->token_awt == ''){
                throw new AuthorizationException ("Please inform token_awt field.", 1);
            }
            $arrDados = $this->validateAWT($request->token_awt);
            
            if(!property_exists($request, 'contaId') || $request->contaId == null 
            || $request->contaId == ''){
                throw new Exception ("Please inform contaId field.", 1);
            }

            $pdo = DbConnectionFactory::get();
            $sql = "SELECT * FROM Movimentos where usuario_id = :usuario_id and conta_id = :conta_id";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':usuario_id', $arrDados[2]);
            $statement->bindValue(':conta_id', $request->contaId);
            $statement->execute();
            $movimentosEncontrados = $statement->fetchAll(PDO::FETCH_ASSOC);

            return new JsonResponse(['movimentos' => $movimentosEncontrados], 200);

            } catch (PDOException $e) {
                    file_put_contents ('log.txt', $e->getMessage() . '\n' , FILE_APPEND);
                    return new JsonResponse (['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
            } catch (AuthorizationException $e) {
                    return new JsonResponse(['mensagem' => $e->getMessage()], 401);
            } catch (Exception $e) {
                    return new JsonResponse(['mensagem' => $e->getMessage()], 500);
            }
        }

        public function create ($request) {
            try {
                if(!property_exists($request, 'token_awt') || $request->token_awt == null 
                || $request->token_awt == ''){
                    throw new AuthorizationException ("Please inform token_awt field.", 1);
                }
                $arrDados = $this->validateAWT($request->token_awt);
                
                if(!property_exists($request, 'contaId') || $request->contaId == null 
                || $request->contaId == ''){
                    throw new Exception ("Please inform contaId field.", 1);
                }

                if(!property_exists($request, 'descricao') || $request->descricao == null 
                || $request->descricao == ''){
                    throw new Exception ("Please, fill in the field descricao", 1);
                }
                
                if(!property_exists($request, 'valor') || $request->valor == null 
                || $request->valor == ''){
                    throw new Exception ("Please, fill in the field valor", 1);
                }

                if(!property_exists($request,'tipo') || $request->tipo == null 
                || $request->tipo == ''){
                    throw new Exception ("Please, fill in the field tipo", 1);
                }

                $pdo = DbConnectionFactory::get();
                $pdo->beginTransaction();
                $sql = "SELECT * FROM Contas where id = :conta_id";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':conta_id', $request->contaId);
                $statement->execute();
                $contasEncontradas = $statement->fetch(PDO::FETCH_ASSOC);

                if ($contasEncontradas == false) {
                    throw new Exception ("Esta conta não existe");
                }

                $sql = "INSERT INTO Movimentos(descricao, valor, tipo, usuario_id, conta_id) 
                VALUES(:descricao, :valor, :tipo, :usuario_id, :conta_id)";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':descricao', $request->descricao);
                $statement->bindValue(':valor', $request->valor);
                $statement->bindValue(':tipo', $request->tipo);
                $statement->bindValue(':usuario_id', $arrDados[2]);
                $statement->bindValue(':conta_id', $request->contaId);
                $statement->execute();

                // pegar todos os movimentos do usuario
                $sql = "SELECT * FROM Movimentos where usuario_id=:usuario_id and conta_id=:contaId";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':contaId', $request->contaId);
                $statement->bindValue(':usuario_id', $arrDados[2]);
                $statement->execute();
                $movimentosEncontrados = $statement->fetchAll();
                
                $totalDaConta = 0;
                foreach ($movimentosEncontrados as $key => $movimento) {
                    if ($movimento["tipo"] == 2) {
                        $totalDaConta += $movimento["valor"];
                    } else {
                        $totalDaConta -= $movimento["valor"];
                    }
                }

                $sql = "UPDATE Contas SET saldo = :saldo where usuario_id=:usuario_id and id=:contaId";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':saldo', $totalDaConta);
                $statement->bindValue(':contaId', $request->contaId);
                $statement->bindValue(':usuario_id', $arrDados[2]);
                $statement->execute();
                

                $pdo->commit();
                return new JsonResponse(['mensagem' => 'Deu bom!'], 200);

            } catch (AuthorizationException $e) {
                $pdo->rollBack();
                return new JsonResponse(['mensagem' => $e->getMessage()], 401);
            } catch (PDOException $e) {
                file_put_contents('log.txt', $e->getMessage() . '\n', FILE_APPEND);
                $pdo->rollBack();
                return new JsonResponse (['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
            } catch (Exception $e) {
                $pdo->rollBack();
                return new JsonResponse(['mensagem' => $e->getMessage()], 500);
            }
        }

        public function delete ($request) {

            try {
                if(!property_exists($request, 'token_awt') || $request->token_awt == null 
                || $request->token_awt == ''){
                    throw new AuthorizationException ("Please inform token_awt field.", 1);
                }
                $arrDados = $this->validateAWT($request->token_awt);
                
                if(!property_exists($request, 'contaId') || $request->contaId == null 
                || $request->contaId == ''){
                    throw new Exception ("ContaId not informed.", 1);
                }

                if(!property_exists($request, 'movimentoId') || $request->movimentoId == null 
                || $request->movimentoId == ''){
                    throw new Exception ("MovimentoId not informed.", 1);
                }

                $pdo = DbConnectionFactory::get();
                $sql = "SELECT * FROM Contas where id = :conta_id";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':conta_id', $request->contaId);
                $statement->execute();
                $movimentoEncontrado = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$movimentoEncontrado) {
                    throw new Exception("Este movimento nao existe");
                } else {
                    return new JsonResponse(["data" => $movimentoEncontrado], 200);
                }

            } catch (AuthorizationException $e) {
                return new JsonResponse(["message" => $e->getMessage()], 401);
            } catch (PDOException $e) {
                file_put_contents('log.txt', $e->getMessage() . '\n', FILE_APPEND);
                return new JsonResponse(['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
            } catch (Exception $e) {
                return new JsonResponse(["message" => $e->getMessage()], 500);
            }
        }
    }