<?php

#ContasController.php

class ContasController {
    

    public function index($request)
    {
        //validar se o usuario tem login válido

        try {
            ///usuario não autorizado
            $pdo = DbConnectionFactory::get();
            $sql = "SELECT * FROM Contass";
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
        catch (Exception $e) {
            return new JsonResponse (['mensagem' => $e->getMessage()], 500);
        }
    }
}