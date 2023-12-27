<?php 
session_start();
if($_SERVER['REQUEST_METHOD'] ==  'POST'){
    // $_SESSION['post'][]=$_POST;
    // echo "this is a put request\n";
    parse_str(file_get_contents("php://input"),$post_vars);
    // // echo $post_vars['fruit']." is the fruit\n";
    // echo "<pre>";
    // echo "adasd<pasdsadasdre>asdsadasd";
    var_dump($post_vars);
    echo'{
            "result": false,
            "message": "Error message from the server"
          }';
      
}
// var_dump($_SERVER['REQUEST_METHOD']);
// echo `{
//     "result": true,
//     "data": {"name":"asdsa"}
// }`;

echo `{
    "result": false,
    "message": "Error message from the server"
  }`;
  


// ["updatedRows"]=>
//   array(1) {
//     [0]=>
//     array(13) {
//       ["name"]=>
//       string(6) "asddsa"
//       ["address"]=>
//       string(10) "BBC Sterrt"
//       ["city"]=>
//       string(9) "somewhere"
//       ["created"]=>
//       string(19) "2023-12-10 16:12:40"
//       ["currency"]=>
//       string(3) "USD"
//       ["id"]=>
//       string(1) "2"
//       ["country"]=>
//       string(3) "USD"
//       ["email"]=>
//       string(15) "sadsd@dsdas.ads"
//       ["type"]=>
//       string(0) ""
//       ["files_count"]=>
//       string(1) "0"
//       ["_leaf"]=>
//       string(4) "true"
//       ["rowKey"]=>
//       string(1) "1"
//       ["_attributes"]=>
//       array(6) {
//         ["rowNum"]=>
//         string(1) "2"
//         ["checked"]=>
//         string(5) "false"
//         ["disabled"]=>
//         string(5) "false"
//         ["checkDisabled"]=>
//         string(5) "false"
//         ["rowSpan"]=>
//         string(0) ""
//         ["tree"]=>
//         array(3) {
//           ["parentRowKey"]=>
//           string(0) ""
//           ["hidden"]=>
//           string(5) "false"
//           ["expanded"]=>
//           string(0) ""
//         }
//       }
//     }
//   }

