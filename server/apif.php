<?php


// caech
$TABLEDATA = new stdClass();
$TABLEDATA->default_filters = array();

// read
function getTable($table){
    global $TABLEDATA;
    if(!isset($TABLEDATA->$table)){
        $TABLEDATA->$table = require_once('table-datatable.json');
    }
    return $TABLEDATA->$table;
 
}
// read with filters
function filters($table, $filters){
    $filters = new stdClass();
    $newTable = new stdClass();
}
// update data
function update($table,$location, $updated_data){
    // get the table
}
// create new data
function create($table,$data){
    // get the table
}
// delete data
function delete($table,$data){
    // get the table
}