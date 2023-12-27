<?php
session_start();
// ob_start();
require_once 'apif.php';
// if($_SESSION['auth'] <= 0){
//     return throw new ErrorException('Unauthrised',401);
// }
// echo 'hi';

// if(isset($_GET['table'])){
//     echo require_once('table-datatable.json');
// }
// if(isset($_get['t']) && $_GET['t'] === $_SESSION['token'])

// echo '<pre>';
// var_dump($TABLEDATA);
if(isset($_GET['t'])){
    $getVals = new stdClass();
    foreach ($_GET as $key => $value){
        $getVals->$key = $value;
    }

    foreach ($getVals as $key => $value){
        switch($key){
            case 'table':
                getTable($value);
                break;
            case 'dfilters':
                // TODO: get default (per user) and json encode
                // Expecting format '[{"index":3,"value":"Any","checkd":false},{"index":2,"value":"asdasd","checkd":true}]';
                echo '[{"index":3,"value":"Any","checkd":false},{"index":2,"value":"asdasd","checkd":true}]';
                // echo  filters($getVals->table, $getVals->filter);
                break;
        }
    }
}

