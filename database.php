<?php
/**
 * ARQON - THE VAULT | Database Connection Manager
 * Padrão: Singleton
 * Segurança: Prevenção estrita contra SQL Injection (Native Prepares)
 */

declare(strict_types=1);

class Database {
    // Armazena a única instância da conexão
    private static ?PDO $instance = null;

    // Configurações de acesso (baseadas no seu setup.php)
    private string $host = 'localhost';
    private string $db   = 'arqon';
    private string $user = 'root';
    private string $pass = 'Senai@118';

    // O construtor é privado para impedir que a classe seja instanciada via 'new'
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        
        $options = [
            // Lança exceções em caso de erro (fundamental para try/catch)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            
            // Retorna os dados como um array associativo limpo (ideal para JSON)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            
            // BLINDAGEM: Desativa a emulação. O MySQL faz o prepare real.
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$instance = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Em produção, nunca mostre $e->getMessage() para o usuário final.
            // Aqui registramos no log do servidor e damos uma mensagem genérica.
            error_log("ARQON DB ERROR: " . $e->getMessage());
            die(json_encode([
                "status" => "error", 
                "message" => "O Cofre está temporariamente inacessível. Falha de conexão."
            ]));
        }
    }

    // Previne a clonagem da instância
    private function __clone() {}

    // Previne a desserialização da instância
    public function __wakeup() {
        throw new Exception("Não é possível desserializar um Singleton.");
    }

    /**
     * Ponto de acesso global para a conexão
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            new self();
        }
        return self::$instance;
    }
}