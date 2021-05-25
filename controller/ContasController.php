<?php

class ContasController extends Controller
{
    public function index($request)
    {
        try {
            if(!property_exists($request, 'token_awt') || $request->token_awt == null 
            || $request->token_awt == ''){
                throw new Exception("Please inform token_awt field.", 1);
            }
            $this->validateAWT($request->token_awt);
            
            $pdo = DbConnectionFactory::get();
            $sql = "SELECT * FROM Contas";
            $statement = $pdo->prepare($sql);
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