<?php
header( "Content-type: application/json" );
require_once "functions.php";

$filename = "database.json";

    if( $request->isMethod("GET") ){
        if( $request->isQuery() ){
            $query = $request->getQuery();
            if( count($query) === 1 ){
                $query = $query[0];
                if ( $request->controlValue($query) ){
                    $dbResponse = $db->getRequest($filename, $query);
                    $users = $dbResponse["users"];
                    if( count($dbResponse) > 1 ){
                        $errors = $dbResponse["errors"];
                        $response->send(["status" => 200, "errors"=>$errors, "users" => $users]);
                    }
                    $response->send(["status" => 200, "users" => $users]);
                }else{
                    $errorResponse = $response->queryError($query);
                    $response->send($errorResponse, $errorResponse["status"]);
                }
            }else{
                // Only a placeholder.
                // Usually more than 1 query should be OK.
                $response->send(
                    ["message" => "Only one query per request allowed"],
                    400
                );
            }
        }else{
            $allUsers = $db->getAll($filename);
            $response->send($allUsers);
        }
    }else{
        $response->send(
            ["message" => "Method not allowed"],
            405
        );
    }

?>