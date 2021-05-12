<?php

 class DbConnectionFactory {
    private static $host = 'localhost';
    private static $user = 'root';
    private static $password = 'colossos934';
    private static $name = 'gerenciador_financeiro';

    public static function get() {
        try
        {
            $pdo = new PDO( 'mysql:host=' . self::$host . ';dbname=' . self::$name, self::$user, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch ( PDOException $e )
        {
            echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
        }
    }
}