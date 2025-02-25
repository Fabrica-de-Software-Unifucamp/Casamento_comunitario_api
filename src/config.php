<?php

class Config {
    private $host = 'mysql';
    private $dbname;
    private $username;
    private $password;
    protected $conn = null;

    public function __construct() {
        $this->dbname = getenv('MYSQL_DATABASE');
        $this->username = getenv('MYSQL_USER');
        $this->password = getenv('MYSQL_PASSWORD');

        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "❌ Erro na conexão: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

?>
