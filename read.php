<?php
header( "Content-type: application/json" );
require_once "functions.php";

    if( $request->isMethod("GET") ){
        if( $request->isQuery() ){
            $query = $request->getQuery();
            if( count($query) === 1 ){
                $query = $query[0];
                if ( $request->controlValue($query) ){
                    $response->send($query);
                }else{
                    $response->queryError([$query]);
                }
            }else{
                $response->send(
                    ["status"=>400, "message" => "Only one query per request allowed", ],
                    400
                );
            }
        }else{
            $allUsers = $db->getAll($filename);
            $response->send(["status"=>200, "users" => $allUsers]);
        }
    }else{
        $response->send(
            ["message" => "Method not allowed"],
            405
        );
    }

?>