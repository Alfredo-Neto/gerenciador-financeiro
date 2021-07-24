<?php

namespace GenFin\Controller;
use PDO;
use Exception;
use PDOException;
use GenFin\Entity\Conta;
use GenFin\Entity\Movimento;
use GenFin\Lib\JsonResponse;
use GenFin\Controller\Controller;
use GenFin\Lib\AuthorizationException;
use GenFin\Repository\ContasRepository;
use GenFin\Database\DbConnectionFactory;
use GenFin\Repository\MovimentosRepository;
use GenFin\Service\ContasService;

// CRIAR UMA ROTA PARA LISTAR TODOS OS MOVIMENTOS DE UMA CONTA
class MovimentosController extends Controller {

    private MovimentosRepository $movimentosRepository;
    private ContasRepository $contasRepository;
    private ContasService $contasService;

    public function __construct() {
        $this->movimentosRepository = new MovimentosRepository();
        $this->contasRepository = new ContasRepository();
        $this->contasService = new ContasService();
    }
    
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

            $movimentosEncontrados = $this->movimentosRepository->findAll($arrDados[2], $request->contaId);

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

        public function create ($request) 
        {
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

                $conta = $this->contasRepository->find($request->contaId, $arrDados[2]);

                if ($conta == false) {
                    throw new Exception ("Esta conta não existe");
                }

                $pdo = DbConnectionFactory::get();
                // $pdo->beginTransaction();
                $movimento = new Movimento();
                $movimento->descricao = $request->descricao;
                $movimento->valor = $request->valor;
                $movimento->tipo = $request->tipo;
                $movimento->contaId = $request->contaId;
                $movimento->usuarioId = $arrDados[2];
                $this->movimentosRepository->create($movimento);
                
                // pegar todos os movimentos do usuario
                
                $this->contasService->atualizarMovimentosDaConta($conta);
                // $pdo->commit();
                return new JsonResponse(['mensagem' => 'DEU BOM!'], 200);

            } catch (AuthorizationException $e) {
                // $pdo->rollBack();
                return new JsonResponse(['mensagem' => $e->getMessage()], 401);
            } catch (PDOException $e) {
                file_put_contents('log.txt', $e->getMessage() . '\n', FILE_APPEND);
                // $pdo->rollBack();
                return new JsonResponse (['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
            } catch (Exception $e) {
                // $pdo->rollBack();
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
                $sql = "SELECT * FROM Movimentos where id = :id_movimento and conta_id = :conta_id and usuario_id = :usuario_id";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':conta_id', $request->contaId);
                $statement->bindValue(':id_movimento', $request->movimentoId);
                $statement->bindValue(':usuario_id', $arrDados[2]);
                $statement->execute();
                $movimentoEncontrado = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$movimentoEncontrado) {
                    throw new Exception("Este movimento nao existe para esta conta e para este usuario");
                }

                $sql = "DELETE FROM Movimentos WHERE id = :id_movimento and conta_id = :conta_id and usuario_id = :usuario_id";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':id_movimento', $request->movimentoId);
                $statement->bindValue(':conta_id', $request->contaId);
                $statement->bindValue(':usuario_id', $arrDados[2]);
                $statement->execute();

                return new JsonResponse(["mensagem => Movimento apagado"], 200);

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