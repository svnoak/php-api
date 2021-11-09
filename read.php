<?php
header( "Content-type: application/json" );
require_once "functions.php";

$db = new Functions\Database();
$request = new Functions\Request();
$response = new Functions\Response();
$filename = "database.json";

    if( $request->isMethod("GET") ){
        if( $request->isQuery() ){
            $query = $request->getQuery();
            $response->send($query);
        }else{
            $allUsers = $db->getAll($filename);
            $response->send($allUsers);
        }
    }else{
        $response->send(
            ["message" => "YOU SUCK!"],
            418
        );
    }

//$db->find(1, $filename);

?>