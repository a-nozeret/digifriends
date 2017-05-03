<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST){

    // Include database connection
    include_once '../config/database.php';

    // Query object
    include_once '../objects/query.php';

    // Class instance
    $database = new Database();
    $db = $database->getConnection();
    $query = new Query($db);

    // Set query property values
    $query->value = $_POST['value'];

    // create the product
    $result = $query->create();

    echo json_encode($result);
}
