<?php
require_once "functions.php";

$filename = "database.json";
$userData = json_decode(file_get_contents("php://input"),true);
    if( $request->isMethod("POST") ){
        if( $request->isContentType("application/json") ){
            $missingKeys = $request->checkKeys("update", $userData);
            var_dump($missingKeys);
            if( $missingKeys !== false ){
                $updateResponse = $db->update($filename, $userData);
                $updatedUser = $updateResponse["updatedUser"];
                $statusCode = $updateResponse["status"];
                if( isset($updateResponse["error"]) ){
                    $error = $updateResponse["error"];
                    $response->send(
                        ["status"=>$statusCode, "user"=>$updatedUser, "error"=>$error],
                        $statusCode
                    );
                }
                $response->send(
                    ["status"=>$statusCode, "user"=>$updatedUser],
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