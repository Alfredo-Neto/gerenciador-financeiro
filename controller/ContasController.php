<?php

class ContasController extends Controller
{
    public function index($request)
    {
        try {
            if(!property_exists($request, 'token_awt') || $request->token_awt == null 
            || $request->token_awt == ''){
                throw new AuthorizationException ("Please inform token_awt field.", 1);
            }
            $arrDados = $this->validateAWT($request->token_awt);

            $pdo = DbConnectionFactory::get();
            $sql = "SELECT * FROM Contas WHERE usuario_id = :usuario_id";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':usuario_id', $arrDados[2]);
            $statement->execute();
            $contasEncontradas = $statement->fetchAll(PDO::FETCH_ASSOC);
            return new JsonResponse (['contas' => $contasEncontradas], 200);
        }
        catch (PDOException $e) {
            file_put_contents ('log.txt' , $e->getMessage() . '\n' , FILE_APPEND); // log mixuruca
            //escrever uma entrada no log com a mensagem do erro $e->getMessage();
            return new JsonResponse (['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
        }
        catch (AuthorizationException $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 401);
        }
        catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
        }
    }

    public function create($request)
    {
        try {
            if(!property_exists($request, 'token_awt') || $request->token_awt == null
            || $request->token_awt == ''){
                throw new Exception("Please inform token_awt field.", 1);
            }
            
            $arrDados = $this->validateAWT($request->token_awt);
            
            if(!property_exists($request, 'nome') || $request->nome == null 
            || $request->nome == ''){
                throw new Exception("Please inform nome field.", 1);
            }

            if(!property_exists($request, 'saldo') || $request->saldo == null 
            || $request->saldo == ''){
                throw new Exception("Please inform saldo field.", 1);
            }
            
            $pdo = DbConnectionFactory::get();
            $sql = "INSERT INTO Contas (nome, saldo, usuario_id)
            VALUES (:nome, :saldo, :usuario_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':nome', $request->nome);
            $statement->bindValue(':saldo', $request->saldo);
            $statement->bindValue(':usuario_id', $arrDados[2]);
            $statement->execute();

            return new JsonResponse (['mensagem' => 'Conta criada com sucesso!' ], 200);

        }catch (PDOException $e) {
            file_put_contents ('log.txt' , $e->getMessage() . '\n' , FILE_APPEND); // log mixuruca
            //escrever uma entrada no log com a mensagem do erro $e->getMessage();
            return new JsonResponse (['mensagem' => 'Ocorreu um erro no banco de dados! Favor tente novamente!'], 500);
        }
        catch (AuthorizationException $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 401);
        }
        catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
        }
            
    }
}
/*
digita nome senha
clica no login
sistema front dispara request pro back
se nome e senha ta certo, vc da o ok! (retorna o token_awt novinho em folha)

no front se ok, vc vai lá pro feed.

get
post

-> saber que você é você
-> saber que vc se logou certinho antes


carregar uma imagen (manda o token)
fazer um post (manda token)
consultar pra ver se tem notificação (mandatoken)
*/