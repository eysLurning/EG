<?php
    require_once '../../assets/includes/init.php';
    // validate user
    if(isset($_GET['modal'])){
        switch ($_GET['modal']) {
            case 'new_contact':
                require_once 'new_contact.php';
                break;
            
            default:
                # code...
                break;
        }
    }