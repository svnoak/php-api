<?php
require_once "functions.php";

$filename = "database.json";
$userID = json_decode(file_get_contents("php://input"),true)["id"];
    if( $request->isMethod("DELETE") ){
        if( $request->isContentType("application/json") ){
            $missingKeys = $request->checkKeys("delete", $userID);
            if( !$missingKeys ){
                $deletionResponse = $db->delete($filename, $userID);
                $deletedUser = $deletionResponse["deletedUser"];
                $statusCode = $deletionResponse["status"];
                if( isset($deletionResponse["error"]) ){
                    $error = $deletionResponse["error"];
                    $response->send(
                        ["status"=>$statusCode, "deletedUserID"=>$deletedUser, "error"=>$error],
                        $statusCode
                    );
                }
                $response->send(
                    ["status"=>$statusCode, "deletedUserID"=>$deletedUser],
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