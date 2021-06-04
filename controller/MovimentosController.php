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
    }