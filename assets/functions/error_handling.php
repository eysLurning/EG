<?php


function myException($exception) {
    echo "<b>Exception:</b> " . $exception->getMessage();
}
set_exception_handler('myException');