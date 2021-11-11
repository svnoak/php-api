<?php
namespace Functions;

$db = new Database();
$request = new Request();
$response = new Response();

error_reporting(-1);
    class Database{
        function open($filename){
            return json_decode(file_get_contents($filename), true);
        }

        function getAll($filename){
            $db = $this->open($filename);
            return $db;
        }

        function limitUsers($filename, $length){
            $allUsers = $this->getAll($filename);
            shuffle($allUsers);
            $limitedUsers = array_slice($allUsers, 0, $length);
            return $limitedUsers;
        }

        function find($filename, $id){
            $db = $this->open($filename);
            $column = array_column($db, "id");
            $index = array_search($id, $column);
            if( $index === false ){
                return false;
            }
            return $db[$index];
        }

        function create($filename, $user){

        }

        function delete($filename, $id){

        }

        function update($filename, $id, $user){

        }

        function getRequest($filename, $query){
            $key = key($query);
            $value = $query[$key];
            switch ($key) {
                case 'id':
                    return $this->find($filename, $value);
                    break;
                case 'limit':
                    return $this->limitUsers($filename, $value);
                    break;
                case 'ids':
                    $errors = [];
                    $users = [];
                    foreach( $value as $id ){
                        $userFound = $this->find($filename, $id);
                        if( !$userFound ){
                            array_push($errors, ["status" => 404, "message" => "User id $id not found"]);
                        }else {
                            array_push($users, $userFound);
                        }
                    }
                    $response["users"] = $users;
                    $response["errors"] = $errors;
                    return $response;
                    break;
            }
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
            // TODO: create control that only array queries are allowed arrays as value.
            if( strpos($_SERVER["QUERY_STRING"], "&") ){
                $array = explode("&", $_SERVER["QUERY_STRING"]);
                foreach( $array as $queryStrings ){
                    $query = $this->explodeQuery($queryStrings);
                }
            }else{
                $query = $this->explodeQuery($_SERVER["QUERY_STRING"]);
        }
            return $query;
        }

        function explodeQuery($queryString){
            $query = [];
            $queryArray = explode("=", $queryString);
            if( strpos($queryArray[1], "," ) ){
                $queryArray[1] = explode(",", $queryArray[1]);
            }
            array_push($query, [$queryArray[0]=>$queryArray[1]]);
            return $query;
        }

        function controlValue($query){
            $param = key($query);
            $val = $query[$param];
            switch ($param) {
                case 'id':
                    return is_numeric($val);
                    break;
                
                case 'ids':
                    if ( is_array($val) ){
                        foreach($val as $id){
                            if ( !is_numeric($id) ) {
                                return is_numeric($id);
                            }
                        }
                    }
                    return is_array($val);
                    break;
                
                case 'limit':
                    return is_numeric($val);
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
                    $this->send( ["status" => 400, "message" => "Query 'id' must be of type int"], 400 );
                    break;
                
                case 'ids':
                    $this->send( ["status" => 400, "message" => "Query 'ids' must be of type array and values of type int"], 400 );
                    break;
                
                case 'limit':
                    $this->send( ["status" => 400, "message" => "Query 'limit' must be of type int"], 400 );
                    break;
            }
        }
    }

?>