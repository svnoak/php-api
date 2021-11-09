<?php
namespace Functions;
    class Database{
        function open($filename){
            return json_decode(file_get_contents($filename), true);
        }

        function getAll($filename){
            $db = $this->open($filename);
            return $db;
        }

        function find($id, $filename){
            $db = $this->open($filename);
            $column = array_column($db, "id");
            $index = array_search($id, $column);
            return $db[$index];
        }

        function create($id, $filename){

        }

        function delete(){

        }

        function update(){

        }
    }

    class Request{
        function isContentType($type){
            return $_SERVER["CONTENT_TYPE"] === $type;
        }

        function isMethod($method){
            return $_SERVER["REQUEST_METHOD"] === $method;
        }

        function isQuery(){
            if (isset($_SERVER["QUERY_STRING"])){
                return true;
            }else{
                return false;
            }
        }

        function getQuery(){
            $query = [];
            if( str_contains($_SERVER["QUERY_STRING"], "&") ){
                $array = explode("&", $_SERVER["QUERY_STRING"]);
                foreach ( $array as $queryStrings ){
                    $queryArray = explode("=", $queryStrings);
                    array_push($query, [$queryArray[0]=>$queryArray[1]]);
                }
            }else{
                $queryArray = explode("=", $_SERVER["QUERY_STRING"]);
                array_push($query, [$queryArray[0]=>$queryArray[1]]);
        }
        return $query;
    }
}

    class Response{
        function send($message, $statusCode = 200){
            header("Content-Type: application/json");
            http_response_code($statusCode);
            $json = json_encode($message);
            echo $json;
            exit();
        }
    }
?>