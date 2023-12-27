<?php


function logError($message) {
    // Log to a file - adjust the path as needed
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, '../logs/error.log');
}
function logDBCnanges($message) {
    // Log to a file - adjust the path as needed
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, '../logs/dbchanges.log');
}

// Function to send JSON response
function jsonResponse($data, $status = 200){
    try {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    } catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error while preparing JSON response']);
    }
}


function throwError($e,$msg='', $code = 500){
    logError('Exception: ' . $e->getMessage());
    throw new Exception($msg.$e->getMessage(), $code);
}



function not_found($status = 'error', $msg='Not Found',$code = 404){
    // throw new Exception($msg, $code);
    jsonResponse(['status' => $status , 'message' => $msg], $code);
}

function test($self = ''){
    jsonResponse(['status' => 'success' , 'message' => $self]);
}

// Add other utility functions as needed


// function query_db($sql, $params = [], $all = true, $paramKeys = []){
//     global $database;

//     if (empty($sql)) {return null;}
    
//     try{
//         $stmt = $database->con->prepare($sql);
//         $paramsSTM = '';
//         foreach ($params as $key => $value) {
//             $value = trim($value);
//             $paramsSTM .= " $key => $value";
//             $paramKey = $key +1;
//             // echo  $paramKey . $value;
//             if(!empty($paramKeys)){
//                 $paramKey = $paramKeys[$key];
//             }
//             $stmt->bindParam($paramKey, $value);
//         }

//         if($all === 'update'){
//             if($stmt->execute()){
                
//                 logDBCnanges("SQL: $sql \nParams:$paramsSTM");
//                 return true;
//             }else{
//                 return false;
//             }
//         }
//         // $stmt->debugDumpParams();
//         $stmt->execute();
//         $result = $all ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_OBJ);
//         logDBCnanges("SQL: $sql \n Params:$paramsSTM");
//         return $result !== false ? $result : null;

//     }catch (PDOException $e) {
//         logError('Exception: ' . $e->getMessage());
//         logError("SQL: $sql \n Params:$paramsSTM");
//         throwError($e,'Database Error: ');
//     }
// }
function query_db($sql, $params = [], $all = true){
    global $database;

    if (empty($sql)) {return null;}
    
    try{
        $stmt = $database->con->prepare($sql);
        $index = 0;
        $paramsSTM = '';
        foreach ($params as $paramKey => $paramValue) {
            if($paramKey === $index){
                $paramKey =  $index +1;
            }else{
                $paramKey = ":$paramKey";
            }
            $paramValue = trim($paramValue);
            $paramsSTM .= " $paramKey => $paramValue";
            $stmt->bindValue($paramKey, $paramValue);
        }

        if($all === 'update'){
            if($stmt->execute()){
                logDBCnanges("SQL: $sql \nParams:$paramsSTM");
                //chack that feald was updates
                return true;
            }else{
                return false;
            }
        }
        // $stmt->debugDumpParams();
        $stmt->execute();
        $result = $all ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_OBJ);
        logDBCnanges("SQL: $sql \n Params:$paramsSTM");
        return $result !== false ? $result : null;

    }catch (PDOException $e) {
        logError('Exception: ' . $e->getMessage());
        logError("SQL: $sql \n Params:$paramsSTM");
        throwError($e,'Database Error: ');
    }
}


function init_grid(){
    jsonResponse(['status' => 'success', 'data' => '']);
}


function getAllTables($get_arr){
    try {
        // Extract 'page' from $get_arr or set it to an empty string if not present
        $page = isset($get_arr['page']) ? $get_arr['page']:'';
        // Map valid page values or log an error and throw an exception for invalid values
        switch($page){
            case'lists':
                $page = 'lists';
                break;
            case'deals':
                $page = 'deals';
                break;
            default:
                logError("Get Tables: Page not found");
                throw new Exception("Page $page not found", 404);
        }
        // Query the database to get active tables based on the selected page
        $tables = query_db("SELECT * FROM `data_tables` WHERE `page` = :page AND active = 1",["page"=>$page]);
        // Throw an exception if no active tables are found
        if(!$tables){
            throw new Exception('No tables found', 404);
        }
        // Define keys to decode and keys to skip
        $decode = ['headers','columns','config','uris'];
        $skip = ['validation'];
        // Initialize an array to store the response data
        $responce= [];
        // Loop through each table and process its columns
        foreach ($tables as $tbl) {
            $current = [];
            // Process each key-value pair in the table
            foreach($tbl as $key=>$val){
                // Skip keys listed in $skip array
                if(in_array($key, $skip)){
                    continue;
                // Decode keys listed in $decode array
                }else if(in_array($key, $decode)){
                    $current[$key] = json_decode($val);
                }else {
                // Otherwise, use the original value
                    $current[$key] = $val;
                }
            }
             // Add the processed table data to the response array
            $responce[]=  $current;
            
            // TODO: here lookup the user and overwrite the config for the users prefrences
        }
        // Return a JSON response with the success status and data
        jsonResponse(['status' => 'success', 'data' => $responce]);
    } catch (Exception $e) {
        // Log the exception and return an error response
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}

function get_user_table_defaults(){
    $options = new stdClass;
    $options->draggable = false;
    $options->rowHeaders = null;
    $options->showDummyRows = false;
    $options->resizable = false;
    $options->treeName = false;
}

function deep_replace($obj, $obj2){
    foreach ($obj as $key => $value) {
        if (isset($obj2[$key])) {
            $obj[$key] = $obj2[$key];
        }
    }
}
function getTableName($table){
    switch($table){
        case'deals':
          return "contacts";
        case'fx':
            return "fx";
        case'customers':
            return"contacts";
        case'users':
            return"users";
        case'items':
            return"items";
        default:
            logError("get_table: table $table not found");
            throw new Exception("Table $table not found", 404);
    }
}
function get_table($get_arr){
    global $database;
    try {
        $start = isset($get_arr['ds']) ? $get_arr['ds']:'';
        $end = isset($get_arr['de']) ? $get_arr['de']:'';
        $table = isset($get_arr['table']) ? $get_arr['table']:'';
        $tableName = getTableName($table);
           
        $SQL = "SELECT * FROM $tableName";


        $data = array();
        $table_res = query_db($SQL);
        if(empty($table_res)){
            return not_found('error',"no data",404);
        }
        foreach ($table_res as $tbl){
            $data[] = $tbl;
        }
        jsonResponse(['status' => 'success', 'data' =>$data]);
    } catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}
function lists_tables($get_arr){

}

function new_row($get_arr,$post_arr){
    try {
        $table = $get_arr['table'];
        // $keys = [];  // 0=> value
        // $values = []; // ":key => value
        $keyVal = [];
        // $valueKeys = [];
        foreach ($post_arr as $postKey => $postValue) {
            if(!preg_match("/^[A-Za-z_\-]+$/", $postKey)){
                throw new Exception("Invalid field: $postKey", 1);
            }
            $res = validate_feald($table,['fieldName'=>$postKey,'value'=>$postValue],true);
            if(!$res){
                throw new Exception("Invalid field: $postKey", 1);
            }
            $keyVal[$postKey] = $postValue;
            // $values[":$postKey"] = $postValue;
            // $keys[] = $postKey;
            // $valueKeys [] = ":$key";
        }
        $keysSTR = implode(', ', array_keys($keyVal));
        $valuesSTR = implode(', :',array_keys($keyVal));
        // $placeholders = implode(', ', array_map(fn($key) => ":$key", $keys));
        // // foreach ($newTAble as $key => $value) {
            // echo $keysSTR;
            // var_dump($keyVal);
                        // var_dump($values);
        // }
        $SQL = "INSERT INTO ".getTableName($table)."($keysSTR) VALUES (:$valuesSTR)";
        // $SQL .= " ON DUPLICATE KEY UPDATE " . implode(', ', array_map(fn($key) => "$key = :$key", $keys));
        $resoults = query_db($SQL,$keyVal);
        jsonResponse(['status' => 'success', 'data'=>$keyVal]);
        // Assuming jsonResponse function has appropriate exception handling
        // test($post_arr);
    } catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}

function update_table($get_arr,$post_arr){
    try{
        
        if (!preg_match('/^\d+$/', $post_arr['id']) || !preg_match("/^[A-Za-z_\-]+$/", $post_arr['fieldName'])) {
            throw new Exception("Invalid fealds.", 1);
        }
        $id = intval($post_arr['id']);
        $fieldName=$post_arr['fieldName'];

        $table = $get_arr['table'];
        $tableName = getTableName($table);

        $valid = validate_feald($table,$post_arr,true);
        if(!$valid){
            throw new Exception('Not Found',404);
        }

        $sql = "UPDATE $tableName SET $fieldName = ? WHERE id = $id";

        $res = query_db($sql,[$valid['value']],'update');
        if($res){
            jsonResponse(['status' => 'success', 'data'=>['newValue'=>$valid['value']],"res"=>$res]);
        }else{
            throwError('err');
        }
    }catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}

function validate_feald($table,$post_arr,$isInternal=false){
    try {
        $fieldName=$post_arr['fieldName'];
        $value = $post_arr['value'];
        // $table
        $table_data = query_db("SELECT * FROM `data_tables` WHERE `name` = ? limit 1",[$table],false);
        if(!$table_data){
            if($isInternal){
                return false;
            }
            return jsonResponse(['status' => 'success', 'data'=>["isValid"=> true,"message"=>""]]);
        }
        // TODO: get table config to see what fealds need what validation and if they are requerd
        $res = validateField($fieldName,$value,$table_data);
        if($isInternal){
            $res['value'] = $value;
            return $res;
        }
        jsonResponse(['status' => 'success', 'data'=>$res]);
    } catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
};

function getDropdouns($get_arr){
    try {
        // Extract 'page' from $get_arr or set it to an empty string if not present
        $page = isset($get_arr['page']) ? $get_arr['page']:'';
        // Map valid page values or log an error and throw an exception for invalid values
        switch($page){
            case'lists':
                $page = 'lists';
                break;
            case'deals':
                $page = 'deals';
                break;
            default:
                logError("Get Tables: Page not found");
                throw new Exception("Page $page not found", 404);
        }
        // Query the database to get active tables based on the selected page
        $tables = query_db("SELECT * FROM `data_tables` WHERE `page` = :page AND active = 1",["page"=>$page]);
        // Throw an exception if no active tables are found
        if(!$tables){
            throw new Exception('No tables found', 404);
        }
        $dropdouns = [];
        foreach ($tables as $tbl) {
            if(empty($tbl->headers))continue;
            $tablesToGetList = json_decode($tbl->headers);
            foreach ($tablesToGetList as $key => $value) {
                if(!array_key_exists($key,$dropdouns)){
                    $dropdouns[$key] = $value;
                }
            }
            
        }
        if (empty($dropdouns)){
           return jsonResponse(['status' => 'success', 'data' => 'No Data']);
        }
        $DATA = [];
        foreach ($dropdouns as $dropdounKey=>$fealds ) {
            $SQL = "SELECT * FROM `$dropdounKey`";
            $res = query_db($SQL);
            if(!$res)continue;
            $tempRes = [];
            foreach ($res as $resValue) {
                $tempLine['id']=$resValue->id;
                foreach ($fealds as $fealdvalue) {
                    $tempLine[$fealdvalue]=$resValue->{$fealdvalue};
                }
                $tempRes[] = $tempLine;
            }
            $DATA= [$dropdounKey => $tempRes];
        }
        jsonResponse(['status' => 'success', 'data' => $DATA]);
    } catch (Exception $e) {
        // Log the exception and return an error response
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}
function getIdVal($get_arr){
    try {
        // Extract 'page' from $get_arr or set it to an empty string if not present
        $tableName = isset($get_arr['table']) ? $get_arr['table']:'';
        // Map valid page values or log an error and throw an exception for invalid values
        $tableName = getTableName($tableName);

        $colToVal = isset($get_arr['column']) ? $get_arr['column']:'';
        
        $table = query_db("SELECT * FROM `$tableName`"); 
        //optionaly look for active status and auth level
        // have a function can intract with auth detectino

        if(!$table){
            throw new Exception('No tables found', 404);
        }
        $responce = [];
        foreach ($table as $value) {
            if($value &&$value->{$colToVal})
           $responce[] = ["id"=>$value->id,$colToVal=>$value->{$colToVal}];
        }
        
        if (empty($responce)){
           return jsonResponse(['status' => 'success', 'data' => 'No Data']);
        }
        
        jsonResponse(['status' => 'success', 'data' => $responce]);
    } catch (Exception $e) {
        // Log the exception and return an error response
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}




function validateField($fieldName, $fieldValue, $table_data) {
    try {
        $fieldValue = trim($fieldValue);
        $validators = [
            'zip' => [
                'uk' => "/^[A-Za-z]{1,2}\d{1,2}\s?\d[A-Za-z]{2}$/",
                'world' => "/^\d{5}(?:-\d{4})?$/"
            ],
            'vat' => "/^[A-Za-z]{2}[A-Za-z0-9]{0,13}$/",
            'tel' => "/^(?:\+\d{1,4}\s?)?(?:(?:\d{1,3}-)?\d{3}-\d{4}|\d{10})$/",
            'url' => "/^(https?:\/\/)?([a-zA-Z0-9-]+\.){1,}[a-zA-Z]{2,}(\/\S*)?$/",
            'table_name' => "/^[A-Za-z_\-]+$/",
            // 'ALLCHARS'
        ];
        $valid = true;
        $msg = '';

        $validation = json_decode($table_data->validation)->{$fieldName};

        foreach ($validation->validators as $key => $value) {
            switch ($key) {
                case "MINLENGTH": 
                    if(strlen($fieldValue) < $value){
                        $valid = false;
                        $msg .= "Min length is $value ";
                    }
                    break;
        
                case "MAXLENGTH":
                    if(strlen($fieldValue) > $value){
                        $valid = false;
                        $msg .= "Max length is $value ";
                    }
                    break;

                case "ISEMAIL":
                    if(filter_var($fieldValue, FILTER_VALIDATE_EMAIL) === false){
                        $valid = false;
                        $msg .= "Not a real email ";
                    }
                    break;
                case "ISNUMBER":
                    // $id = intval($fieldValue);
                    if(!preg_match('/^\d+$/',$fieldValue) ){
                        $valid = false;
                        $msg .= (!empty($msg) ? '& ':'')."$fieldName must be numaric ";
                    }
                    break;
                case "ALLCHARS":
                    if(!preg_match('/^[a-zA-Z0-9-_ ]*$/',$fieldValue) ){ // add A-Za-z0-9 -_ and " " (space)
                        $valid = false;
                        $msg .=  !empty($msg) ? '& ':''."not a valid $fieldName ";
                    }
                    break;
                    
                case "REGEX":
                    $isValidRegex = false;

                    foreach ((array)$validators[$fieldName] as $regexPattern) {
                        if (preg_match($regexPattern, $fieldValue)) {
                            $isValidRegex = true;
                            break;
                        }
                    }
                    
                    if (!$isValidRegex) {
                        $valid = false;
                        $msg .= 'Invalid ' . $fieldName;
                    }
                    break;

                case "EXISTS":
                    $tableName = trim($validation->validators->EXISTS);
                    if (!preg_match($validators['table_name'], $fieldName)) {
                        // Invalid column name, handle the error as needed
                        $valid = false;
                        $msg .= 'Invalid column name';
                        break;
                    }
                    $sql = "SELECT * FROM `$tableName` WHERE `$fieldName` = ? limit 1";
                    $exists = query_db($sql,[$fieldValue],false);
                    if(!$exists){
                        $valid = false;
                        $msg .= $fieldValue." Dsont exsists";
                    }
                    break;
                case "DOSNTEXSIST":
                    $tableName = trim($validation->validators->DOSNTEXSIST);
                    if (!preg_match($validators['table_name'], $fieldName)) {
                        // Invalid column name, handle the error as needed
                        $valid = false;
                        $msg .= 'Invalid column name';
                        break;
                    }
                    $sql = "SELECT * FROM `$tableName` WHERE `$fieldName` = ? limit 1";
                    $exists = query_db($sql,[$fieldValue],false);
                    if($exists){
                        $valid = false;
                        $msg .= $fieldValue." alredy exsists";
                    }
                    break;
                // case "CREATEIFNOTEXSISTS":
                //     $tableName = trim($validation->validators->EXISTS);
                //     if (!preg_match($validators['table_name'], $fieldName)) {
                //         // Invalid column name, handle the error as needed
                //         $valid = false;
                //         $msg .= 'Invalid column name';
                //         break;
                //     }
                //     $sql = "SELECT * FROM `$tableName` WHERE `$fieldName` = ? limit 1";
                //     $exists = query_db($sql,[$fieldValue],false);
                //     if(!$exists){
                //         $sql = "INSERT INTO"
                //         $valid = false;
                //         $msg .= $fieldValue." alredy exsists";
                //     }
                //     break;
        
                default:
                    $valid = false;
                    $msg .= "unknown error";
            }
        }
        
        if(empty($fieldValue) && $validation->required){
            $valid = false;
            $msg = 'Invalid '.$fieldName;
        }else if(empty($fieldValue) && !$validation->required){
            $valid = true;
            $msg = '';
        }
        return ['isValid'=> $valid, 'message'=>$msg];
    } catch (Exception $e) {
        logError('Exception: ' . $e->getMessage());
        not_found('error',$e->getMessage(),$e->getCode());
    }
}


