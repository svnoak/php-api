<?php
require_once "functions.php";

$filename = "database.json";
$newUser = json_decode(file_get_contents("php://input"),true);

    if( $request->isMethod("POST") ){
        if( $request->isContentType("application/json") ){
            $missingKeys = $request->checkKeys("create", $newUser);
            if( !$missingKeys ){
                $creationResponse = $db->create($filename, $newUser);
                $createdUser = $creationResponse["user"];
                $statusCode = $creationResponse["status"];
                if( isset($creationResponse["error"]) ){
                    $error = $creationResponse["error"];
                    $response->send(
                        ["status"=>$statusCode, "response"=>$createdUser, "error"=>$error],
                        $statusCode
                    );
                }
                $response->send(
                    ["status"=>$statusCode, "response"=>$createdUser],
                    $statusCode
                );
            }else{
                $response->send(
                    ["status" => 400, "message" => "Key or Value missing"],
                    400
                );
            }
        }else{
            $response->send(
                ["status"=> 400,"message" => "Bad Request: Wrong Content-Type"],
                400
            );
        }
    }else{
        $response->send(
            ["status"=> 405, "message" => "Method not allowed"],
            405
        );
    }

?>