<?php
// Include database connection
include_once '../config/database.php';

// Include query object
include_once '../objects/query.php';

// Class instance
$database = new Database();
$db = $database->getConnection();
$query = new Query($db);

// Read all products
$results = $query->readAll();

// Output in json format
header('Content-Type: application/json');
echo json_encode($results);
