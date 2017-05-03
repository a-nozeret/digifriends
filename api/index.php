<?php
// Include database connection
include_once '../config/database.php';

// Include objects
include_once '../objects/query.php';
include_once '../objects/digifriend.php';


// Class instance
$database = new Database();
$db = $database->getConnection();
$selected_value = $_POST['selected_value'];
$query = new Query($db);
$query->value = $selected_value;
$digifriend = new Digifriend($db);

// Get results
$result = array();
$result['query'] = $query->readByValue();
$result['digifriends'] = $digifriend->readByQueryId($result['query'][0]['id']);

// Output in json format
header('Content-Type: application/json');
echo json_encode($result);
