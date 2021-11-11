<?php
namespace Functions;

error_reporting(-1);

$db = new Database();
$request = new Request();
$response = new Response();

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
            return $index;
        }

        function read($filename, $id){
            $index = $this->find($filename, $id);
            if( $index === false ){
                return false;
            }
            $db = $this->open($filename);
            return $db[$index];
        }

        // TODO: IF FILE DOES NOT EXIST, no error message yet
        function create($filename, $userData){
            $request = new Request();
            $response = new Response();
            $db = $this->open($filename);
            $newID = $this->getMaxID($filename) + 1;
            $userData["id"] = $newID;
            
            foreach( $userData as $key=>$value ){
                $val = [$key=>$value];
                $control = $request->controlValue($val);
                if( !$control ){
                    $typeError = $response->queryError($val);
                    $response = [
                        "status"=>400, 
                        "user"=>$userData,
                        "error"=>$typeError["message"]
                    ];
                    return $response;
                }
            }

            $db[] = $userData;
            $json = json_encode($db, JSON_PRETTY_PRINT);
            file_put_contents($filename, $json);
            $response = [
                "status"=>200, "user"=>$userData
            ];
            return $response;
        }

        function delete($filename, $id){
            $user = $this->find($filename, $id);
            if( $user === false ){
                return ["status"=>404, "error"=>"User not found", "users"=>""];
            }
            $db = $this->open($filename);
            
            array_splice($db,$user,1);
            $json = json_encode($db, JSON_PRETTY_PRINT);
            file_put_contents($filename, $json);
            $response = [
                "status"=>200,
                "deletedUser"=>$id,
            ];
            return $response;
        }

        function update($filename, $userData){
            $id = $userData["id"];
            $db = $this->open($filename);
            $userIndex = $this->find($filename, $id);

            foreach( array_keys($userData) as $key ){
                $db[$userIndex][$key] = $userData[$key];
            }

            $json = json_encode($db, JSON_PRETTY_PRINT);
            file_put_contents($filename, $json);

            $response = [
                "status"=>200,
                "updatedUser"=>$db[$userIndex]
            ];
            return $response;
        }

        function getRequest($filename, $query){
            $key = key($query);
            $value = $query[$key];
            switch ($key) {
                case 'id':
                    $user = $this->read($filename, $value);
                    if ( !$user ) {
                        return ["status" => 404, "users"=>"", "errors" => "User does not exist"];
                    }
                    return ["users" => $user];
                    break;
                case 'limit':
                    $limitedUsers = $this->limitUsers($filename, $value);
                    return ["users"=>$limitedUsers];
                    break;
                case 'ids':
                    $errors = [];
                    $users = [];
                    foreach( $value as $id ){
                        $user = $this->read($filename, $id);
                        if( !$user ){
                            array_push($errors, ["status" => 404, "message" => "User id $id not found"]);
                        }else {
                            array_push($users, $user);
                        }
                    }
                    $response["users"] = $users;
                    $response["errors"] = $errors;
                    return $response;
                    break;
            }
        }

        function getMaxID($filename){
            $db = $this->open($filename);
            $column = array_column($db, "id");
            $maxID = max($column);
            return $maxID;
        }
    }

    class Request extends Database{
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
                case 'limit':
                case 'age':
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
                
                case 'first_name':
                case 'last_name':
                    return is_string($val);
                    break;
                
                case 'email':
                    return strpos($val, "@");
                    break;
            }
        }
        
        function checkKeys($type, $values){
            switch ($type) {
                case 'create':
                    $keys = [
                        "first_name",
                        "last_name",
                        "email",
                        "age"
                    ];
                    return $this->compareKeys($keys, $values);
                    break;

                case 'delete':
                    $keys = [
                        "id"
                    ];
                    return $this->compareKeys($keys, $values);
                    break;
                
                case 'update':
                    $keys = [
                        "id"
                    ];
                    if ( count($values) < 2 ){
                        return false;
                    }

                    $comparison = $this->compareKeys($keys, $values);
                    if ( $comparison ) {
                        return $comparison;
                    }

                    return true;
                    break;
            }
        }

        function compareKeys($key, $input){
            $missingKeys = array_diff($keys, array_keys($input));
            if ( !$missingKeys ){
                return false;
            }else{
                return $missingKeys;
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
                case 'age':
                case 'limit':
                    return ["status" => 400, "message" => "'$param' must be of type int"];
                    break;

                case 'first_name':
                case 'last_name':
                    return ["status" => 400, "message" => "'$param' must be of type int"];
                    break;
                
                case 'ids':
                    return ["status" => 400, "message" => "'$param' must be of type array and values of type int"];
                    break;
                
                case 'email':
                    return ["status" => 400, "message" => "'$param' must be a valid email"];
                    break;
            }
        }
    }

?>