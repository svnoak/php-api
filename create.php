<?php
require_once "functions.php";

$db = new Functions\Database();
$server = new Functions\Requests();
$filename = "database.json";

    echo $server->isRequest("GET");


//$db->find(1, $filename);

?>