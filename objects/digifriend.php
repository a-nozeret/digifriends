<?php
class Digifriend {

    // Database connection and table name
    private $connection;
    private $table_name = "digifriends";

    // Object properties
    public $id;
    public $value;
    public $query_id;

    public function __construct($db){
        $this->connection = $db;
    }

    public function readByQueryId($id) {

        // Select by query_id
        $sql_query = "SELECT id, value, query_id FROM " . $this->table_name . " WHERE query_id = ? ORDER BY value";

        $stmt = $this->connection->prepare($sql_query);

        $stmt->execute([$id]);

        $digifriends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $digifriends;
    }

    public function create($query_id, $value) {
        // Calculate
        $digifriends = $this->calculateDigifriends($value);

        // Prepare sql query
        $sql_query = "INSERT INTO " . $this->table_name . " (value, query_id) VALUES ";
        foreach($digifriends as $d){
            $sql_query .= '(?,'.$query_id.'),';
        }
        $sql_query = substr($sql_query, 0, -1);

        $stmt = $this->connection->prepare($sql_query);
        return $stmt->execute($digifriends);
    }

    private function calculateDigifriends($x) {
        $digifriends = array();
        for ($i = 1; $i <= $x; $i++) {
            $computed = false;
            if ($i % 3 === 0) {
                if ($i % 5 === 0) {
                    $computed = true;
                    $digifriends[] = $i + $i * 3 * 5 * ROUND(SQRT($x)) + 2;
                }
                else {
                    $digifriends[] = $i + $i * $x;
                }
            }
            if ($i % 5 === 0 && !$computed) {
                $digifriends[] = $i + $i * ROUND(SQRT($x)) + 1;
            }
        }
        return $digifriends;
    }
}
