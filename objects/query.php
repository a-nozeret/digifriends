<?php
include_once '../objects/digifriend.php';
class Query {

    // Database connection and table name
    private $connection;
    private $table_name = "queries";

    // Object properties
    public $id;
    public $value;
    public $date_updated;

    public function __construct($db){
        $this->connection = $db;
    }

    public function readByValue(){
        // Prepare
        $sql_query = "SELECT id, value FROM " . $this->table_name . " WHERE value = :value";
        $stmt = $this->connection->prepare($sql_query);

        // Retrieve data
        $value = htmlspecialchars(strip_tags($this->value));
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update row
        $this->update();

        return $result;
    }

    public function update(){
        $sql_query = "UPDATE " . $this->table_name . " SET date_updated = CURRENT_TIMESTAMP WHERE value = ?";

        $stmt = $this->connection->prepare($sql_query);
        $stmt->execute([$this->value]);
    }

    public function readAll(){

        // Select all data
        $sql_query = "SELECT id, value, date_updated FROM " . $this->table_name . " ORDER BY date_updated DESC";

        $stmt = $this->connection->prepare($sql_query);
        $stmt->execute();

        $queries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $queries;
    }

    public function create(){

        // Check first if query exists in database
        $query = new Query($this->connection);
        $query->value = $this->value;
        $tmp = $query->readByValue();

        if (!empty($tmp)) {
            $query->update();
            return $tmp;
        }

        try{
            $sql_query = "INSERT INTO " . $this->table_name . " SET value = :value";

            $stmt = $this->connection->prepare($sql_query);

            // Sanitize
            $value = htmlspecialchars(strip_tags($this->value));

            $stmt->bindParam(':value', $value);

            if ($stmt->execute()) {
                $new_object = $query->readByValue()[0];

                // Create related Digifriends
                $digifriend = new Digifriend($this->connection);
                $digifriend->create($new_object['id'], $new_object['value']);

                // Return the newly created object
                return $new_object;
            }

        }
        catch(PDOException $exception){
            die('ERROR: ' . $exception->getMessage());
        }
    }
}
