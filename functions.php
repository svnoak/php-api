<?php
namespace Functions;

$db = new Database();
$request = new Request();
$response = new Response();
$filename = "database.json";

error_reporting(-1);
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
            // TODO: create control that only array queries are allowed arrays as value.
            if( strpos($_SERVER["QUERY_STRING"], "&") ){
                $array = explode("&", $_SERVER["QUERY_STRING"]);
                foreach ( $array as $queryStrings ){
                    $queryArray = explode("=", $queryStrings);
                    if( strpos($queryArray[1], "," ) ){
                        $queryArray[1] = explode(",", $queryArray[1]);
                    }
                    array_push($query, [$queryArray[0]=>$queryArray[1]]);
                }
            }else{
                $queryArray = explode("=", $_SERVER["QUERY_STRING"]);
                array_push($query, [$queryArray[0]=>$queryArray[1]]);
        }
            return $query;
        }

        function controlValue($query){
            $param = key($query);
            $val = $query[$param];
            switch ($param) {
                case 'id':
                    intVal($val) ? true : false;
                    break;
                
                case 'ids':
                    is_array( $val) ? true : false;
                    break;
                
                case 'limit':
                    intVal($val) ? true : false;
                    break;
            }
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

        function queryError($query){
            $param = key($query);
            $val = $query[$param];
            switch ($param) {
                case 'id':
                    $this->send( ["error" => "Query 'id' must be of type int"], 400 );
                    break;
                
                case 'ids':
                    $this->send( ["error" => "Query 'ids' must be of type array and values of type int"], 400 );
                    break;
                
                case 'limit':
                    $this->send( ["error" => "Query 'limit' must be of type int"], 400 );
                    break;
            }
        }
    }

?>