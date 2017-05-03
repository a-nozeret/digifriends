<?php
class Database {
    # Config
    private $db_name  = "digifriends";
    private $username = "root";
    private $password = "root";
    private $hostname = "localhost";
    public $connection;

    # Connect
    public function getConnection() {

        $this->connection = null;

        try {
            $this->connection = new PDO("mysql:host=" . $this->hostname . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->connection;
    }

}
