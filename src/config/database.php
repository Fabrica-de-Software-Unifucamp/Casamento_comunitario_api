<?php

class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        $this->host = 'mysql';
        $this->dbname = getenv('MYSQL_DATABASE');
        $this->username = getenv('MYSQL_USER');
        $this->password = getenv('MYSQL_PASSWORD');
    }

    public function connect() {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8", 
                $this->username, 
                $this->password
            );
            
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $pdo; 
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}

?>
