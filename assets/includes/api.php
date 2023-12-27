<?php 
session_start();
// $not_found = "<!DOCTYPE HTML PUBLIC -'//IETF//DTD HTML 2.0//EN'><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p><hr><address>Apache/2.4.56 (Win64) OpenSSL/1.1.1t PHP/8.2.4 Server at localhost Port 80</address></body></html>";

// if(true){
//      http_response_code(404);
//      die($not_found ) ;
// }

require_once 'init.php';
require_once 'api_functions.php';


try {

    // Get the request method and URI
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];


    function handle_get($get_arr){
        if(!isset($get_arr['ep'])) {
            throw new Exception('Endpoint not specified', 400);
            // return not_found();
        }

        switch ($get_arr['ep']) {
            // returns all tables for the givin page
            case 'getAllTables':
                getAllTables($get_arr);
                break;
            //alternative to getAllTables
            case 'pageTables':
                getAllTables($get_arr);
                break;

            case 'getIdVal':
                getIdVal($get_arr);
                break;

            // return nothing for tsting purpeses
            case 'test':
                test();
                break;

            case 'get_user_table_defaults':
                get_user_table_defaults();
                break;
            // returns the spesific table props = table == tableName ds&de filterd dte range
            case 'get_table':
                get_table($get_arr);
                break;
            case 'getDropdouns':
                getDropdouns($get_arr);
                break;


            default:
                throw new Exception('Not Found', 404);
            // jsonResponse(['status' => 'error', 'message' => 'Not Found'], 404);
            // break;
        }
    }


    function handle_post($get_arr,$post_arr){

        if (!isset($get_arr['ep'])) {
            throw new Exception('Endpoint not specified', 400);
        }

        switch ($get_arr['ep']) {
            case 'update_table':
                $table = $get_arr['table'];
                // not_found();
                // test($post_arr);
                update_table($get_arr,$post_arr);
                break;

            case 'new_row':
                new_row($get_arr,$post_arr);
                break;

            case 'validate_field':
                $table = $get_arr['table'];
                validate_feald($table,$post_arr);
                break;
            
            default:
                throw new Exception('Not Found', 404);
            //     not_found();
            // break;
        }
    }



    //Main swich 
    switch ($method){
        case 'GET':
            handle_get($_GET);
            // jsonResponse(['status' => 'success', 'data' => $users]);
            break;

        case 'POST':
            // var_dump($_POST);
            handle_post($_GET,$_POST);
            break;

        // case 'PUT':
        // case 'PATCH':
        //     not_found();
        //     // handle_put_patch();
        // break;

        // case 'DELETE':
        //     not_found();
        //     // handle_delete();
        // break;

        default:
            // Handle 404 Not Found
            throw new Exception('Method Not Allowed', 405);
            // not_found();
        // break;
    }

} catch (Exception $e) {
    jsonResponse(['status' => 'error', 'message' => $e->getMessage()], $e->getCode());
}

// throw new Exception('not found',404);
