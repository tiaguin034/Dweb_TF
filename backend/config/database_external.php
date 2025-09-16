<?php
/**
 * Configuração do banco de dados para Vercel
 * MedVet Connect
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Configurações para Vercel com banco externo
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'medvet_connect';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        
        // Para PlanetScale ou outros bancos externos
        if(isset($_ENV['DATABASE_URL'])) {
            $url = parse_url($_ENV['DATABASE_URL']);
            $this->host = $url['host'];
            $this->db_name = ltrim($url['path'], '/');
            $this->username = $url['user'];
            $this->password = $url['pass'];
        }
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            
            // Para bancos externos, adicionar SSL se necessário
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            // Para PlanetScale
            if(strpos($this->host, 'aws.connect.psdb.cloud') !== false) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = true;
            }
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            throw new Exception("Erro de conexão com banco de dados");
        }

        return $this->conn;
    }
}
?>
